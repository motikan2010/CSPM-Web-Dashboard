<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Cloud;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CloudService
{

    /**
     * TODO
     *
     * @return mixed
     */
    public function getCloud()
    {
        return Cloud::where([
            'user_id' => Auth::id(),
        ])->get();
    }

    /**
     * TODO
     *
     * @param $cloudId
     * @return mixed
     */
    public function getCloudById($cloudId)
    {
        return Cloud::select(['id', 'name', 'created_at', 'updated_at'])->where([
            'id' => $cloudId,
            'user_id' => Auth::id(),
        ])->first();
    }

    /**
     * TODO
     *
     * @param array $where
     * @return mixed
     */
    public function getFirstCloudWhere(array $where)
    {
        return Cloud::where($where)->first();
    }

    public function createAwsCloud(string $name, string $awsKey, string $awsSecret)
    {
        return Cloud::create([
            'id' => Str::lower(Str::random(18)),
            'name' => $name,
            'user_id' => Auth::id(),
            'cloud_type_id' => 1,
            'credential_1' => $awsKey,
            'credential_2' => $awsSecret,
        ]);
    }

    /**
     * @param string $cid
     * @return array
     */
    public function getAwsCredential(string $cid) : array {
        $cloud = Cloud::select(['credential_1', 'credential_2'])->where([
            'id' => $cid
        ])->first();

        return [
            'AwsAccessKeyId' => $cloud->credential_1,
            'AwsSecretAccessKey' => $cloud->credential_2
        ];
    }

    public function createCloud(string $name, string $awsKey, string $awsSecret)
    {
        return Cloud::create([
            'id' => Str::lower(Str::random(18)),
            'name' => $name,
            'user_id' => Auth::id(),
            'cloud_type_id' => 1,
            'credential_1' => $awsKey,
            'credential_2' => $awsSecret,
        ]);
    }

    public function deleteCloudById(string $cloudId) : bool
    {
        if ( Cloud::where(['id' => $cloudId, 'user_id' => Auth::id()])->delete() > 0) {
            return true;
        } else {
            return false;
        }
    }

}
