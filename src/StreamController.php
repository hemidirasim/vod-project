<?php
namespace App;

class StreamController {
    private $videoManager;
    
    public function __construct(VideoManager $videoManager) {
        $this->videoManager = $videoManager;
    }
    
    public function playVideo(string $date) {
        header('Content-Type: application/vnd.apple.mpegurl');
        
        $segments = $this->videoManager->getPlaylist($date);
        if (empty($segments)) {
            $this->sendError("No segments found for date: {$date}");
            return;
        }

        $this->generateM3U8Playlist($segments);
    }

    private function generateM3U8Playlist(array $segments) {
        echo "#EXTM3U\n";
        echo "#EXT-X-VERSION:3\n";
        echo "#EXT-X-TARGETDURATION:10\n";
        echo "#EXT-X-MEDIA-SEQUENCE:0\n\n";
        
        foreach ($segments as $segment) {
            echo "#EXTINF:10.0,\n";
            echo $segment['url'] . "\n";
        }
        
        echo "#EXT-X-ENDLIST";
    }

    private function sendError(string $message) {
        header('HTTP/1.1 404 Not Found');
        echo $message;
    }
}