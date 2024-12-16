<?php
namespace App;

use Aws\S3\S3Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class VideoManager {
    private $s3Client;
    private $logger;
    private $config;

    public function __construct(array $config) {
        $this->config = $config;
        $this->initLogger();
        $this->initS3Client();
    }

    private function initLogger() {
        $this->logger = new Logger('vod');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/vod.log', Logger::DEBUG));
    }

    private function initS3Client() {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => $this->config['spaces']['region'],
            'endpoint' => $this->config['spaces']['endpoint'],
            'credentials' => [
                'key'    => $this->config['spaces']['key'],
                'secret' => $this->config['spaces']['secret'],
            ],
        ]);
    }

    public function uploadSegment(string $filePath, string $date): bool {
        try {
            $fileName = basename($filePath);
            $result = $this->s3Client->putObject([
                'Bucket' => $this->config['spaces']['bucket'],
                'Key'    => "vod/{$date}/{$fileName}",
                'Body'   => fopen($filePath, 'r'),
                'ACL'    => 'public-read'
            ]);
            
            $this->logger->info("Successfully uploaded segment: {$fileName}");
            return true;
        } catch (\Exception $e) {
            $this->logger->error("Failed to upload segment: " . $e->getMessage());
            return false;
        }
    }

    public function getPlaylist(string $date): array {
        try {
            $result = $this->s3Client->listObjects([
                'Bucket' => $this->config['spaces']['bucket'],
                'Prefix' => "vod/{$date}/"
            ]);

            $segments = [];
            foreach ($result['Contents'] ?? [] as $object) {
                $segments[] = [
                    'url' => $this->getPublicUrl($object['Key']),
                    'size' => $object['Size'],
                    'modified' => $object['LastModified']
                ];
            }
            
            return $segments;
        } catch (\Exception $e) {
            $this->logger->error("Failed to get playlist: " . $e->getMessage());
            return [];
        }
    }

    private function getPublicUrl(string $key): string {
        return "https://{$this->config['spaces']['bucket']}.{$this->config['spaces']['region']}.digitaloceanspaces.com/{$key}";
    }
}
