<?php
/**
 * GenieACS Portal - Front Controller
 * Professional GenieACS Management Portal
 */

// Define directory separator
define('DS', DIRECTORY_SEPARATOR);

// Define root paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . DS . 'app');
define('PUBLIC_PATH', __DIR__);

// Simple autoloader for our classes
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

// Error handler for production
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    $errorType = 'Error';
    switch ($severity) {
        case E_WARNING:
            $errorType = 'Warning';
            break;
        case E_NOTICE:
            $errorType = 'Notice';
            break;
    }
    
    if (Config::get('DEBUG')) {
        echo "<div class='alert alert-danger'><strong>{$errorType}:</strong> {$message} in <code>{$file}</code> on line <strong>{$line}</strong></div>";
    } else {
        error_log("PHP {$errorType}: {$message} in {$file} on line {$line}");
    }
    
    return true;
});

// Exception handler
set_exception_handler(function($exception) {
    http_response_code(500);
    
    if (Config::get('DEBUG')) {
        echo "<div class='container mt-5'>";
        echo "<div class='alert alert-danger'>";
        echo "<h4>Uncaught Exception</h4>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . "</p>";
        echo "<p><strong>Line:</strong> " . $exception->getLine() . "</p>";
        echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "<div class='container mt-5'>";
        echo "<div class='alert alert-danger text-center'>";
        echo "<h4>Oops! Something went wrong</h4>";
        echo "<p>We're sorry, but something went wrong. Please try again later.</p>";
        echo "</div>";
        echo "</div>";
        
        error_log("Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
    }
});

try {
    // Initialize configuration
    Config::init();
    
    // Start output buffering
    ob_start();
    
    // Route the request
    Router::route();
    
    // Flush output
    ob_end_flush();
    
} catch (Exception $e) {
    // Clear any output
    ob_end_clean();
    
    // Handle the exception
    if (Config::get('DEBUG')) {
        echo "<div class='container mt-5'>";
        echo "<div class='alert alert-danger'>";
        echo "<h4>Application Error</h4>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
        echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</div>";
        echo "</div>";
    } else {
        http_response_code(500);
        include APP_PATH . DS . 'views' . DS . 'errors' . DS . '500.php';
    }
    
    // Log the error
    error_log("Application Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
}

// Performance monitoring (optional)
if (Config::get('DEBUG')) {
    $memory = memory_get_peak_usage(true);
    $time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
    
    echo "<!-- Debug Info: Memory: " . round($memory / 1024 / 1024, 2) . "MB, Time: " . round($time * 1000, 2) . "ms -->";
} 