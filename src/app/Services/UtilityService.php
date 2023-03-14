<?php
declare(strict_types=1);

namespace App\Services;


use Aws\S3\S3Client;

class UtilityService
{

    public function s3GetObject(string $key, string $saveAs)
    {
        $s3Client = $this->createS3Client();
        $s3Client->getObject([
            'Bucket' => $_ENV['AWS_ECS_S3_BUCKET'],
            'Key' => $key,
            'SaveAs' => $saveAs
        ]);
    }

    private function createS3Client()
    {
        return new S3Client([
            'version' => 'latest',
            'region'  => $_ENV['AWS_DEFAULT_REGION'],
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ]
        ]);
    }
}
