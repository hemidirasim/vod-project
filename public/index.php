<?php
require __DIR__ . '/../vendor/autoload.php';

use App\VideoManager;
use App\StreamController;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Load config
$config = require __DIR__ . '/../config/config.php';

// Initialize services
$videoManager = new VideoManager($config);
$streamController = new StreamController($videoManager);

// Simple router
$route = $_GET['route'] ?? '';

try {
    switch ($route) {
        case 'play':
            $date = $_GET['date'] ?? date('Y-m-d');
            $streamController->playVideo($date);
            break;
            
        case 'upload':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $file = $_FILES['segment'] ?? null;
                if ($file && $file['error'] === UPLOAD_ERR_OK) {
                    $date = $_POST['date'] ?? date('Y-m-d');
                    $result = $videoManager->uploadSegment($file['tmp_name'], $date);
                    echo json_encode(['success' => $result]);
                }
            }
            break;
            
        default:
            include __DIR__ . '/templates/player.php';
    }
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "Error: " . $e->getMessage();
}