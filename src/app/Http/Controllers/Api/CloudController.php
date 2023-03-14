<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\CloudNewRequest;
use App\Services\CloudService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CloudController extends Controller
{

    private CloudService $cloudService;

    public function __construct(CloudService $cloudService)
    {
        $this->cloudService = $cloudService;
    }


    public function list() {
        $clouds = $this->cloudService->getCloud();
        return response()->json([
            'clouds' => $clouds
        ]);
    }

    public function detail(Request $request){
        $id = $request->get('id');
        $cloud = $this->cloudService->getCloudById($id);
        if ( $cloud === null ) {
            return response()->json(['status' => 'error'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['cloud' => $cloud]);
    }

    /**
     * 検査クラウド情報の追加
     *
     * @param CloudNewRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function new(CloudNewRequest $request) {
        $name = $request->post('name');
        $awsKey = $request->post('aws_key');
        $awsSecret = $request->post('aws_secret');
        $cloud = $this->cloudService->createCloud($name, $awsKey, $awsSecret);

        return response()->json(['id' => $cloud->id]);
    }

    /**
     * 検査クラウドを削除
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request){
        $id = $request->get('id');
        if ( !$this->cloudService->deleteCloudById($id) ) {
            return response()->json(['status' => 'error'], Response::HTTP_BAD_REQUEST);
        }
        return response()->json(['status' => 'success', 'cid' => $id], Response::HTTP_OK);
    }

}
