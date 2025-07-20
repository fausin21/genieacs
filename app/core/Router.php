<?php

class Router {
    private static $routes = [];
    
    public static function get($path, $callback) {
        self::$routes['GET'][$path] = $callback;
    }
    
    public static function post($path, $callback) {
        self::$routes['POST'][$path] = $callback;
    }
    
    public static function route() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Remove query string from URI
        $requestUri = strtok($requestUri, '?');
        
        // Remove base path if needed
        $requestUri = str_replace('/public', '', $requestUri);
        
        // Handle simple query parameter routing
        $page = $_GET['p'] ?? 'dashboard';
        $action = $_GET['action'] ?? 'index';
        $id = $_GET['id'] ?? null;
        
        try {
            switch ($page) {
                case 'dashboard':
                    $controller = new Dashboard();
                    $controller->index();
                    break;
                    
                case 'devices':
                    $controller = new Devices();
                    if ($action === 'show' && $id) {
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
                    self::handleApiRoutes();
                    break;
                    
                default:
                    self::notFound();
                    break;
            }
        } catch (Exception $e) {
            self::handleError($e);
        }
    }
    
    private static function handleApiRoutes() {
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
    
    public static function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    public static function notFound() {
        http_response_code(404);
        include __DIR__ . '/../views/errors/404.php';
        exit;
    }
    
    private static function handleError($exception) {
        if (Config::get('DEBUG')) {
            echo "<h1>Error</h1>";
            echo "<p><strong>Message:</strong> " . $exception->getMessage() . "</p>";
            echo "<p><strong>File:</strong> " . $exception->getFile() . "</p>";
            echo "<p><strong>Line:</strong> " . $exception->getLine() . "</p>";
            echo "<pre>" . $exception->getTraceAsString() . "</pre>";
        } else {
            http_response_code(500);
            include __DIR__ . '/../views/errors/500.php';
        }
        exit;
    }
    
    public static function url($page, $params = []) {
        $url = "?p=$page";
        foreach ($params as $key => $value) {
            $url .= "&$key=" . urlencode($value);
        }
        return $url;
    }
} 