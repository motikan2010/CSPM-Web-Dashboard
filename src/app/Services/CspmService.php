<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\CspmResult;
use App\Models\CspmStatus;
use Aws\Ecs\EcsClient;
use Aws\Ecs\Exception\EcsException;
use Aws\S3\Exception\S3Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CspmService
{

    private CloudService $cloudService;
    private UtilityService $utilityService;

    public function __construct(CloudService $cloudService, UtilityService $utilityService)
    {
        $this->cloudService = $cloudService;
        $this->utilityService = $utilityService;
    }

    /**
     * CSPMの実行
     *
     * @param $cloudId
     * @param bool $unSafe
     * @return bool
     */
    public function runCspm($cloudId, $unSafe = false): array
    {
        if ( !$unSafe ) {
            // 実行対象の保持者を確認
            if ( $this->cloudService->getFirstCloudWhere(['id' => $cloudId, 'user_id' => Auth::id()]) == null ) {
                return [
                    'status' => false,
                ];
            }
        }
        $cspmResult = $this->runCspmTask($cloudId);
        $execDate = $cspmResult['now'];
        $ecsStatusCode = $cspmResult['success_flag'] ? $cspmResult['response']['@metadata']['statusCode'] : 400;
        CspmStatus::create([
            'cloud_id' => $cloudId,
            'exec_date' => $execDate,
            'response_status_code' => $ecsStatusCode,
            'status' => 1,
        ]);

        return [
            'status' => true,
            'execDate' => $execDate,
            'responseStatusCode' => $ecsStatusCode,
        ];
    }

    /**
     * TODO
     *
     * @param $cloudId
     * @return array
     */
    private function runCspmTask($cloudId): array
    {
        $awsCredential = $this->cloudService->getAwsCredential($cloudId);
        $targetAwsAccessKeyId = $awsCredential['AwsAccessKeyId'];
        $targetAwsSecretAccessKey = $awsCredential['AwsSecretAccessKey'];
        $s3BucketName = $_ENV['AWS_ECS_S3_BUCKET'];
        $s3BucketKeyPrefix = $cloudId;
        $now = date('YmdHis');

        $ecsCommand = $this->generateEcsCommand();

        $success_flag = true;
        $response = null;
        try {
            $response = $this->getEcsClient()->runTask([
                'cluster' => $_ENV['AWS_ECS_CLUSTER'],
                'count' => 1,
                'enableECSManagedTags' => true,
                'enableExecuteCommand' => false,
                'launchType' => 'FARGATE',
                'networkConfiguration' => [
                    'awsvpcConfiguration' => [
                        'assignPublicIp' => 'ENABLED',
                        'securityGroups' => explode(',', $_ENV['AWS_SECURITY_GROUPS']),
                        'subnets' => explode(',', $_ENV['AWS_SUBNETS'])
                    ],
                ],
                'overrides' => [
                    'containerOverrides' => [
                        [
                            'name' => $_ENV['AWS_ECS_CONTAINER_NAME'],
                            'command' => $ecsCommand,
                            'environment' => [
                                [
                                    'name' => 'AWS_ACCESS_KEY_ID',
                                    'value' => $targetAwsAccessKeyId,
                                ], [
                                    'name' => 'AWS_SECRET_ACCESS_KEY',
                                    'value' => $targetAwsSecretAccessKey,
                                ], [
                                    'name' => 'S3_BUCKET_NAME',
                                    'value' => $s3BucketName,
                                ], [
                                    'name' => 'S3_BUCKET_KEY_PREFIX',
                                    'value' => $s3BucketKeyPrefix,
                                ], [
                                    'name' => 'RESULT_FILE_NAME',
                                    'value' => "{$now}_result.json",
                                ], [
                                    'name' => 'COLLECTION_FILE_NAME',
                                    'value' => "{$now}_collection.json",
                                ]
                            ]
                        ]
                    ]
                ],
                'taskDefinition' => $_ENV['AWS_TASK_DEFINITION']
            ]);

            Log::info((string)$response);
        } catch (EcsException $ex) {
            Log::error($ex->getMessage());
            $success_flag = false;
        } catch (\Throwable $ex ) {
            log::error($ex->getMessage());
            $success_flag = false;
        }

        return [
            'now' => $now,
            'success_flag' => $success_flag,
            'response' => $response
        ];
    }

    /**
     * 検査結果をダウンロード&DBに保存
     *
     * @param $cloudId
     * @param $execDate
     * @param bool $isAllLoadFlag
     */
    public function loadCspmResult(string $cloudId, string $execDate,  bool $isAllLoadFlag = false)
    {
        if ( $isAllLoadFlag === true ) {
            $cspmList = $this->getNotLoadedCspmList();
            foreach ( $cspmList as $cspm ) {
                $successFlag = $this->downloadCspmResult($cspm['cloud_id'], $cspm['exec_date']);
                if ( $successFlag ) {
                    $this->storeCspmResult($cspm['cloud_id'], $cspm['exec_date']);
                }
            }
        } else {
            $successFlag = $this->downloadCspmResult($cloudId, $execDate);
            if ( $successFlag ) {
                $this->storeCspmResult($cloudId, $execDate);
            }
        }
    }

    /**
     * 検査結果をダウンロード
     *
     * @param $cloudId
     * @param $execDate
     */
    public function downloadCspmResult($cloudId, $execDate): bool
    {
        Log::info("Start downloadCspmResult : {$cloudId}/{$execDate}_result.json");

        try {
            $this->utilityService->s3GetObject("{$cloudId}/{$execDate}_result.json", $this->getCspmResultPath($cloudId, $execDate));
        } catch (S3Exception $ex){
            Log::warning("Fail downloadCspmResult : {$cloudId}/{$execDate}_result.json");
            return false;
        }
        Log::info("End downloadCspmResult : {$cloudId}/{$execDate}_result.json");

        return true;
    }

    /**
     * TODO
     *
     * @param string $cloudId
     * @return mixed
     */
    public function getCspmStatus(string $cloudId)
    {
        $cloud = $this->cloudService->getCloudById($cloudId);
        if ( $cloud === null ) {
            return [];
        }
        return CspmStatus::where(['cloud_id' => $cloudId])->orderBy('id', 'desc')->get();
    }

    public function getResultByUserId(int $userId): array
    {
        return CspmStatus::select(['cs.cloud_id', 'cs.exec_date', 'cs.response_status_code', 'cs.status'])
            ->from('cspm_statuses as cs')
            ->join('clouds as c', function($join) {
                $join->on('c.id', '=', 'cs.cloud_id');
            })->where('c.user_id', '=', $userId)->limit(3)->get()->toArray();
    }

    /**
     * TODO
     *
     * @param string $cloudId
     * @param string $execDate
     * @param int $page
     * @param bool $unSafe
     * @return
     */
    public function getCspmResult(string $cloudId, string $execDate, int $page, bool $unSafe = false): array
    {
        if ( !$unSafe ) {
            if ( $this->cloudService->getFirstCloudWhere(['id' => $cloudId, 'user_id' => Auth::id()]) == null ) {
                die('Error'); // TODO
            }
        }

        $count = 100;
        $cspmResult = json_decode(File::get($this->getCspmResultPath($cloudId, $execDate)), true);
        return [
            'current_page' => $page,
            'last_page' => ceil(count($cspmResult) / $count),
            'data' => array_slice($cspmResult, ($page-1) * $count, $count)
        ];
    }

    /**
     * ダウンロードした結果をDBに保存
     *
     * @param $cloudId
     * @param $execDate
     */
    private function storeCspmResult($cloudId, $execDate)
    {
        Log::info("End storeCspmResult : cloudId: {$cloudId} execDate: {$execDate}");

        $insertRowsChunk = collect(array_map(function ($result) use ($cloudId, $execDate) {
            return [
                'cloud_id' => $cloudId,
                'exec_date' => $execDate,
                'plugin' => $result->plugin,
                'category' => $result->category,
                'title' => $result->title,
                'description' => $result->description,
                'resource' => $result->resource,
                'region' => $result->region,
                'status' => $result->status,
                'message' => $result->message,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }, json_decode(File::get($this->getCspmResultPath($cloudId, $execDate)))))->chunk(100);

        try {
            DB::beginTransaction(); // トランザクション開始

            // 削除
            CspmResult::where(['cloud_id' => $cloudId, 'exec_date' => $execDate])->delete();

            // 挿入
            foreach ( $insertRowsChunk as $insertRows ) {
                DB::table((new CspmResult())->getTable())->insert($insertRows->toArray());
            }

            // ステータスを"ロード済み"に変更
            CspmStatus::where(['cloud_id' => $cloudId, 'exec_date' => $execDate])->update(['status' => 2]);

            DB::commit(); // コミット
        } catch ( \Throwable $ex ) {
            DB::rollBack();
            Log::error($ex->getMessage());
        }

        Log::info("End storeCspmResult : cloudId: {$cloudId} execDate: {$execDate}");
    }

    /**
     * Diff ファイルを作成する
     *
     * @param $cloudId
     */
    public function createCollectionDiff($cloudId)
    {
        $cspmStatusList = CspmStatus::select(['cloud_id', 'exec_date'])
            ->where(['cloud_id' => $cloudId, 'status' => 2])->orderBy('id')->get()->toArray();

        // CSPM の collection ファイルをダウンロード (cspm_collection/ に保存)
        foreach ( $cspmStatusList as $cspmStatus ) {
            $this->utilityService->s3GetObject("{$cloudId}/{$cspmStatus['exec_date']}_collection.json", $this->getCspmCollectionPath($cloudId, $cspmStatus['exec_date']));
        }

        // 変わりやすい値の行を削除 (cspm_collection_2/ に保存)
        $patterns = [
            '/\s+"time": ".+?"/',
            '/\s+"time": ".+?"$/m',
            '/\s+"requestId": ".+?",/',
            '/\s+"RequestId": ".+?"$/m',
            '/,\n\s+"retryDelay": .+?$/m',
            '/\s+"extendedRequestId": ".+?",/',
        ];
        foreach ( $cspmStatusList as $cspmStatus ) {
            file_put_contents($this->getCspmCollection2Path($cloudId, $cspmStatus['exec_date']),
                preg_replace($patterns, '', file_get_contents($this->getCspmCollectionPath($cloudId, $cspmStatus['exec_date']))));
        }

        // DiffのHTMLを作成
        for ( $i = 0; $i < count($cspmStatusList) - 1; $i++ ) {
            $srcExecDate = $cspmStatusList[$i]['exec_date'];
            $dstExecDate = $cspmStatusList[$i+1]['exec_date'];
            $srcCollectionPath = $this->getCspmCollection2Path($cloudId, $srcExecDate);
            $dstCollectionPath = $this->getCspmCollection2Path($cloudId, $dstExecDate);

            $output = null;
            $diffPath = $this->getCspmDiffPath($cloudId, $srcExecDate, $dstExecDate);

            // .diffファイルを作成 (cspm_diff/ に保存)
            exec("diff -u {$srcCollectionPath} {$dstCollectionPath} > {$diffPath}");

            // diffのHTMLファイルを作成 (cspm_diff_html/ に保存)
            exec("diff2html -i file -F {$this->getCspmDiffHtmlPath($cloudId, $srcExecDate, $dstExecDate)} -- {$diffPath}", $output);
            print_r($output);
        }
    }

    /**
     * @return EcsClient
     */
    private function getEcsClient()
    {
        return new EcsClient([
            'version' => 'latest',
            'region'  => $_ENV['AWS_DEFAULT_REGION'],
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ]
        ]);
    }

    /**
     * TODO
     *
     * @return string[]
     */
    private function generateEcsCommand()
    {
        $mainCmd = 'node /var/scan/cloudsploit/index.js --console=none --json=/tmp/result.json --collection=/tmp/collection.json --cloud aws ';
        //$mainCmd .= '--plugin rootAccountInUse ';
        $mainCmd .= '&& node /var/scan/cloudsploit/uploader.js';
        return ['sh', '-c', $mainCmd];
    }

    /**
     * TODO
     *
     * @param $cloudId
     * @param $execDate
     * @return string
     */
    private function getCspmResultPath($cloudId, $execDate)
    {
        $storeDir = storage_path("app/cspm_result/{$cloudId}/");
        if ( !File::exists($storeDir) ) {
            File::makeDirectory($storeDir);
        }
        return "{$storeDir}/{$execDate}.json";
    }

    /**
     * TODO
     *
     * @param $cloudId
     * @param $execDate
     * @return string
     */
    private function getCspmCollectionPath($cloudId, $execDate)
    {
        $storeDir = storage_path("app/cspm_collection/{$cloudId}/");
        if ( !File::isDirectory($storeDir) ) {
            File::makeDirectory($storeDir);
        }
        return "{$storeDir}/{$execDate}.json";
    }

    /**
     * TODO
     *
     * @param $cloudId
     * @param $execDate
     * @return string
     */
    private function getCspmCollection2Path($cloudId, $execDate)
    {
        $storeDir = storage_path("app/cspm_collection_2/{$cloudId}/");
        if ( !File::isDirectory($storeDir) ) {
            File::makeDirectory($storeDir);
        }
        return "{$storeDir}/{$execDate}.json";
    }

    /**
     * TODO
     *
     * @param $cloudId
     * @param $srcExecDate
     * @param $dstExecDate
     * @return string
     */
    private function getCspmDiffPath($cloudId, $srcExecDate, $dstExecDate)
    {
        $storeDir = storage_path("app/cspm_diff/{$cloudId}");
        if ( !File::isDirectory($storeDir) ) {
            File::makeDirectory($storeDir);
        }
        return "{$storeDir}/{$srcExecDate}_{$dstExecDate}.diff";
    }

    /**
     * TODO
     *
     * @param $cloudId
     * @param $srcExecDate
     * @param $dstExecDate
     * @return string
     */
    private function getCspmDiffHtmlPath($cloudId, $srcExecDate, $dstExecDate)
    {
        $storeDir = storage_path("app/cspm_diff_html/{$cloudId}");
        if ( !File::isDirectory($storeDir) ) {
            File::makeDirectory($storeDir);
        }
        return "{$storeDir}/{$srcExecDate}_{$dstExecDate}.html";
    }

    /**
     * 未ロードの CSPMステータス を取得
     *
     * @return mixed
     */
    private function getNotLoadedCspmList()
    {
        return CspmStatus::select(['cloud_id', 'exec_date'])->where(['status' => 1])->get()->toArray();
    }
}
