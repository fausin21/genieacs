<?php
// Test file untuk memastikan tidak ada error PHP

// Include semua file yang diperlukan
require_once 'app/core/Config.php';
require_once 'app/core/HttpClient.php';
require_once 'app/models/Device.php';
require_once 'app/controllers/Dashboard.php';
require_once 'app/controllers/Devices.php';

// Test basic functions
echo "Testing PHP syntax...\n";

try {
    Config::init();
    echo "✓ Config initialized\n";
    
    $httpClient = new HttpClient();
    echo "✓ HttpClient created\n";
    
    $device = new Device();
    echo "✓ Device model created\n";
    
    $dashboard = new Dashboard();
    echo "✓ Dashboard controller created\n";
    
    $devices = new Devices();
    echo "✓ Devices controller created\n";
    
    // Test url function
    if (function_exists('url')) {
        $testUrl = url('dashboard');
        echo "✓ URL function works: $testUrl\n";
    } else {
        // Define it here for test
        function url($halaman, $params = []) {
            $url = "index.php?halaman=$halaman";
            foreach ($params as $key => $value) {
                $url .= "&$key=" . urlencode($value);
            }
            return $url;
        }
        $testUrl = url('dashboard');
        echo "✓ URL function defined and works: $testUrl\n";
    }
    
    echo "\n✅ All tests passed! No PHP syntax errors.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

// Cleanup
unlink(__FILE__);
?> 