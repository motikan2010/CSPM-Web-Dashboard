<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\CloudService;
use App\Services\CspmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CspmController extends Controller
{

    private CloudService $cloudService;
    private CspmService $cspmService;

    public function __construct(CloudService $cloudService, CspmService $cspmService)
    {
        $this->cloudService = $cloudService;
        $this->cspmService = $cspmService;
    }

    /**
     * CSPMのステータス情報を取得
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request){
        $id = $request->get('id');
        $cspmStatusList = $this->cspmService->getCspmStatus($id);
        if ( ! count($cspmStatusList) > 0 ) {
            // TODO
            die('Error');
        }

        return response()->json(['statusList' => $cspmStatusList]);
    }

    public function allResult()
    {
        $userId = Auth::user()->id;
        $cspmResultList = $this->cspmService->getResultByUserId($userId);
        return response()->json([
            'cspm_result_list' => $cspmResultList,
        ]);
    }

    /**
     * @param Request $request
     */
    public function result(Request $request) {
        $id = $request->get('id');
        $execDate = $request->get('exec_date');
        $page = (int)$request->get('page', 1);

        $cspmResult = $this->cspmService->getCspmResult($id, $execDate, $page);
        return response()->json([
            'cspm_result' => $cspmResult
        ]);
    }

    /**
     * CSPMの実行
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function run(Request $request) {
        $id = $request->post('id');

        $result = $this->cspmService->runCspm($id);

        return response()->json([
            'id' => $id,
            'status' => [
                'exec_date' => $result['execDate'],
                'response_status_code' => $result['responseStatusCode']
            ],
        ], Response::HTTP_OK);
    }
}
