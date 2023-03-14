<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CloudSaveRequest;
use App\Models\CspmStatus;
use App\Services\CloudService;
use App\Services\CspmService;
use Illuminate\Support\Facades\Auth;

class CloudController extends Controller
{
    private CloudService $cloudService;
    private CspmService $cspmService;

    public function __construct(CloudService $cloudService, CspmService $cspmService)
    {
        $this->cloudService = $cloudService;
        $this->cspmService = $cspmService;
    }

    /**
     * クラウドの一覧
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index() {
        $clouds = $this->cloudService->getCloud();

        return view('cloud.index')->with([
            'clouds' => $clouds
        ]);
    }

    /**
     * クラウドの詳細
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id) {
        $cloud = $this->cloudService->getCloudById($id);
        if ( $cloud === null ) {
            return redirect()->route('cloud.index');
        }
        $cspmStatus = $this->cspmService->getCspmStatus($id);

        return view('cloud.show')->with([
            'cloud' => $cloud,
            'cspmStatusList' => $cspmStatus,
        ]);
    }

    /**
     * CSPM検査結果の表示
     *
     * @param $id
     * @param $execDate
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function result($id, $execDate)
    {
        if ( $this->cloudService->getFirstCloudWhere(['id' => $id, 'user_id' => Auth::id()]) == null ) {
            return redirect()->route('cloud.index');
        }
        $cspmResultList = $this->cspmService->getCspmResult($id, $execDate);

        return view('cloud.result')->with(['cspmResultList' => $cspmResultList]);
    }

    /**
     * クラウド情報登録の画面
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function register() {
        return view('cloud.register');
    }

    /**
     * クラウド情報の保存
     *
     * @param CloudSaveRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(CloudSaveRequest $request) {
        $name = $request->post('name');
        $awsKey = $request->post('aws_key');
        $awsSecret = $request->post('aws_secret');
        $cloud = $this->cloudService->createCloud($name, $awsKey, $awsSecret);

        return redirect()->route('cloud.show', ['id' => $cloud->id]);
    }

    /**
     * CSPM検査の実行
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function run($id) {

        // 実行対象の保持者を確認
        if ( $this->cloudService->getFirstCloudWhere(['id' => $id, 'user_id' => Auth::id()]) == null ) {
            return redirect()->route('cloud.index');
        }
        $this->cspmService->runCspm($id);

        return redirect()->route('cloud.show', ['id' => $id]);
    }

    /**
     * クラウド削除確認の画面
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function deletePage($id) {
        $cloud = $this->cloudService->getFirstCloudWhere(['id' => $id, 'user_id' => Auth::id()]);

        if ( $cloud === null) {
            return redirect()->route('cloud.index');
        }

        return view('cloud.delete')->with([
            'cloud' => $cloud
        ]);
    }

}
