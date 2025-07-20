<?php
/**
 * GenieACS Portal - Simple Native PHP Routing
 * Akses: index.php?halaman=dashboard atau index.php?halaman=devices
 */

// Define paths
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . DS . 'app');

// Simple autoloader
spl_autoload_register(function ($className) {
    $paths = [
        APP_PATH . DS . 'core' . DS . $className . '.php',
        APP_PATH . DS . 'controllers' . DS . $className . '.php',
        APP_PATH . DS . 'models' . DS . $className . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Error handling
set_error_handler(function($severity, $message, $file, $line) {
    echo "<div class='alert alert-danger'><strong>Error:</strong> {$message} in <code>{$file}</code> on line <strong>{$line}</strong></div>";
    return true;
});

try {
    // Initialize configuration
    Config::init();
    
    // Get page parameter
    $halaman = $_GET['halaman'] ?? 'dashboard';
    $action = $_GET['action'] ?? 'index';
    $id = $_GET['id'] ?? null;
    
    // Simple routing
    switch ($halaman) {
        case 'dashboard':
            $controller = new Dashboard();
            $controller->index();
            break;
            
        case 'devices':
        case 'perangkat':
            $controller = new Devices();
            if ($action === 'detail' && $id) {
                $controller->show($id);
            } elseif ($action === 'update' && $id) {
                $controller->update($id);
            } elseif ($action === 'task' && $id) {
                $controller->createTask($id);
            } else {
                $controller->index();
            }
            break;
            
        case 'api':
            handleApiRoutes();
            break;
            
        case 'debug':
            $pageTitle = 'Debug & Test Koneksi';
            include APP_PATH . DS . 'views' . DS . 'debug.php';
            break;
            
        default:
            // 404 page
            include APP_PATH . DS . 'views' . DS . 'errors' . DS . '404.php';
            break;
    }
    
} catch (Exception $e) {
    echo "<div class='container mt-5'>";
    echo "<div class='alert alert-danger'>";
    echo "<h4>Application Error</h4>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
    echo "</div>";
}

function handleApiRoutes() {
    header('Content-Type: application/json');
    
    $action = $_GET['action'] ?? '';
    $deviceId = $_GET['device_id'] ?? '';
    
    switch ($action) {
        case 'refresh_device':
            if ($deviceId) {
                $controller = new Devices();
                echo json_encode($controller->refreshDevice($deviceId));
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        case 'reboot_device':
            if ($deviceId) {
                $controller = new Devices();
                echo json_encode($controller->rebootDevice($deviceId));
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'API endpoint not found']);
            break;
    }
    exit;
}

// Helper function for URLs
function url($halaman, $params = []) {
    $url = "index.php?halaman=$halaman";
    foreach ($params as $key => $value) {
        $url .= "&$key=" . urlencode($value);
    }
    return $url;
} 