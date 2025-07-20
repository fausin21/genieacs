<?php

class Dashboard {
    private $deviceModel;
    
    public function __construct() {
        $this->deviceModel = new Device();
    }
    
    public function index() {
        try {
            // Get device statistics
            $stats = $this->deviceModel->getStats();
            
            // Get recent devices (last 10)
            $recentDevices = $this->deviceModel->all([], 10);
            
            // Get online devices
            $onlineDevices = $this->deviceModel->getOnlineDevices();
            
            // Prepare data for charts
            $statusData = [
                'labels' => ['Online', 'Offline'],
                'data' => [$stats['online'], $stats['offline']],
                'colors' => ['#28a745', '#dc3545']
            ];
            
            // Get manufacturer distribution
            $manufacturers = [];
            foreach ($recentDevices as $device) {
                $manufacturer = $device['summary']['manufacturer'] ?? 'Unknown';
                $manufacturers[$manufacturer] = ($manufacturers[$manufacturer] ?? 0) + 1;
            }
            
            $manufacturerData = [
                'labels' => array_keys($manufacturers),
                'data' => array_values($manufacturers),
                'colors' => ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14']
            ];
            
            // Monitor Virtual Parameters for alerts
            $alerts = [];
            foreach ($recentDevices as $device) {
                if (isset($device['VirtualParameters'])) {
                    $vp = $device['VirtualParameters'];
                    $deviceId = $device['_id'] ?? 'Unknown';
                    
                    // Check temperature
                    if (isset($vp['gettemp']['_value']) && intval($vp['gettemp']['_value']) > 60) {
                        $alerts[] = [
                            'type' => 'danger',
                            'message' => "Temperature tinggi pada device $deviceId: {$vp['gettemp']['_value']}°C",
                            'device_id' => $deviceId
                        ];
                    }
                    
                    // Check RX Power
                    if (isset($vp['RXPower']['_value']) && intval($vp['RXPower']['_value']) < -30) {
                        $alerts[] = [
                            'type' => 'warning',
                            'message' => "Signal lemah pada device $deviceId: {$vp['RXPower']['_value']} dBm",
                            'device_id' => $deviceId
                        ];
                    }
                    
                    // Check PPPoE connection
                    if (!isset($vp['pppoeIP']['_value']) || empty($vp['pppoeIP']['_value'])) {
                        $alerts[] = [
                            'type' => 'info',
                            'message' => "Device $deviceId tidak memiliki koneksi PPPoE",
                            'device_id' => $deviceId
                        ];
                    }
                    
                    // Check WiFi password
                    if (!isset($vp['WlanPassword']['_value']) || empty($vp['WlanPassword']['_value'])) {
                        $alerts[] = [
                            'type' => 'warning',
                            'message' => "WiFi password tidak diset pada device $deviceId",
                            'device_id' => $deviceId
                        ];
                    }
                }
            }
            
            $data = [
                'stats' => $stats,
                'recentDevices' => $recentDevices,
                'onlineDevices' => array_slice($onlineDevices, 0, 5), // Show only 5 online devices
                'statusData' => $statusData,
                'manufacturerData' => $manufacturerData,
                'alerts' => $alerts,
                'pageTitle' => 'Dashboard',
                'error' => null
            ];
            
        } catch (Exception $e) {
            $data = [
                'stats' => ['total' => 0, 'online' => 0, 'offline' => 0, 'percentage_online' => 0],
                'recentDevices' => [],
                'onlineDevices' => [],
                'statusData' => ['labels' => [], 'data' => [], 'colors' => []],
                'manufacturerData' => ['labels' => [], 'data' => [], 'colors' => []],
                'pageTitle' => 'Dashboard',
                'error' => $e->getMessage()
            ];
        }
        
        // Extract variables for view
        extract($data);
        include __DIR__ . '/../views/dashboard.php';
    }
    
    public function getSystemInfo() {
        return [
            'php_version' => PHP_VERSION,
            'server_time' => date('Y-m-d H:i:s'),
            'timezone' => date_default_timezone_get(),
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'memory_peak' => $this->formatBytes(memory_get_peak_usage(true)),
            'genieacs_url' => Config::get('GENIE_BASE'),
            'app_version' => Config::get('APP_VERSION')
        ];
    }
    
    private function formatBytes($size, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
} 