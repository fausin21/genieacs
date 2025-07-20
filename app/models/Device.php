<?php

class Device {
    private $http;
    
    public function __construct() {
        $this->http = new HttpClient();
    }
    
    public function all($filters = [], $limit = null, $skip = 0) {
        $params = [];
        
        if (!empty($filters)) {
            $params['query'] = json_encode($filters);
        }
        
        if ($limit) {
            $params['limit'] = $limit;
        }
        
        if ($skip > 0) {
            $params['skip'] = $skip;
        }
        
        $response = $this->http->get('/devices', $params);
        return $response['data'] ?? [];
    }
    
    public function show($deviceId) {
        $response = $this->http->get("/devices/" . urlencode($deviceId));
        return $response['data'] ?? null;
    }
    
    public function count($filters = []) {
        $params = ['projection' => 'null'];
        
        if (!empty($filters)) {
            $params['query'] = json_encode($filters);
        }
        
        $response = $this->http->get('/devices', $params);
        return count($response['data'] ?? []);
    }
    
    public function getParameters($deviceId, $parameters = []) {
        $params = [];
        if (!empty($parameters)) {
            $params['parameters'] = implode(',', $parameters);
        }
        
        $response = $this->http->get("/devices/" . urlencode($deviceId) . "/parameters", $params);
        return $response['data'] ?? [];
    }
    
    public function setParameters($deviceId, $parameters) {
        $task = [
            'name' => 'setParameterValues',
            'parameterValues' => []
        ];
        
        foreach ($parameters as $param => $value) {
            $task['parameterValues'][] = [$param, $value, 'xsd:string'];
        }
        
        return $this->createTask($deviceId, $task);
    }
    
    public function createTask($deviceId, $task) {
        // GenieACS API endpoint untuk tasks
        $endpoint = "/tasks?connection_request";
        
        // Tambahkan device ID ke task
        $taskData = array_merge($task, [
            'device' => $deviceId
        ]);
        
        try {
            $response = $this->http->post($endpoint, $taskData);
            return $response['data'] ?? null;
        } catch (Exception $e) {
            // Coba dengan endpoint alternatif jika gagal
            try {
                $endpoint = "/tasks/" . urlencode($deviceId);
                $response = $this->http->post($endpoint, $task);
                return $response['data'] ?? null;
            } catch (Exception $e2) {
                // Coba dengan format GenieACS yang berbeda
                $endpoint = "/devices/" . urlencode($deviceId) . "/tasks";
                $response = $this->http->post($endpoint, $task);
                return $response['data'] ?? null;
            }
        }
    }
    
    public function refreshDevice($deviceId) {
        $task = [
            'name' => 'refreshObject',
            'objectName' => ''
        ];
        
        return $this->createTask($deviceId, $task);
    }
    
    public function rebootDevice($deviceId) {
        $task = [
            'name' => 'reboot'
        ];
        
        return $this->createTask($deviceId, $task);
    }
    
    public function factoryResetDevice($deviceId) {
        $task = [
            'name' => 'factoryReset'
        ];
        
        return $this->createTask($deviceId, $task);
    }
    
    public function getDeviceInfo($deviceId) {
        $device = $this->show($deviceId);
        
        if (!$device) {
            return null;
        }
        
        // Extract common device information
        $info = [
            'id' => $device['_id'] ?? '',
            'serial' => $device['_id'] ?? '',
            'manufacturer' => '',
            'model' => '',
            'softwareVersion' => '',
            'hardwareVersion' => '',
            'connectionTime' => $device['_lastInform'] ?? '',
            'uptime' => '',
            'ipAddress' => '',
            'macAddress' => '',
            'status' => 'Unknown'
        ];
        
        // Try to extract info from different parameter structures
        foreach ($device as $param => $value) {
            if (strpos($param, 'DeviceInfo.Manufacturer') !== false) {
                $info['manufacturer'] = $value['_value'] ?? $value;
            } elseif (strpos($param, 'DeviceInfo.ModelName') !== false) {
                $info['model'] = $value['_value'] ?? $value;
            } elseif (strpos($param, 'DeviceInfo.SoftwareVersion') !== false) {
                $info['softwareVersion'] = $value['_value'] ?? $value;
            } elseif (strpos($param, 'DeviceInfo.HardwareVersion') !== false) {
                $info['hardwareVersion'] = $value['_value'] ?? $value;
            } elseif (strpos($param, 'DeviceInfo.UpTime') !== false) {
                $info['uptime'] = $value['_value'] ?? $value;
            } elseif (strpos($param, 'ManagementServer.ConnectionRequestURL') !== false) {
                $url = $value['_value'] ?? $value;
                if (preg_match('/\/\/([^:\/]+)/', $url, $matches)) {
                    $info['ipAddress'] = $matches[1];
                }
            }
        }
        
        // Determine status based on last inform time
        if (isset($device['_lastInform'])) {
            $lastInform = new DateTime($device['_lastInform']);
            $now = new DateTime();
            $diff = $now->getTimestamp() - $lastInform->getTimestamp();
            
            if ($diff < 300) { // 5 minutes
                $info['status'] = 'Online';
            } elseif ($diff < 3600) { // 1 hour
                $info['status'] = 'Recently Online';
            } else {
                $info['status'] = 'Offline';
            }
        }
        
        return $info;
    }
    
    public function search($query, $limit = 20) {
        $filters = [
            '$or' => [
                ['_id' => ['$regex' => $query, '$options' => 'i']],
                ['summary.manufacturer' => ['$regex' => $query, '$options' => 'i']],
                ['summary.modelName' => ['$regex' => $query, '$options' => 'i']]
            ]
        ];
        
        return $this->all($filters, $limit);
    }
    
    public function getOnlineDevices() {
        $fiveMinutesAgo = new DateTime();
        $fiveMinutesAgo->sub(new DateInterval('PT5M'));
        
        $filters = [
            '_lastInform' => ['$gte' => $fiveMinutesAgo->format('c')]
        ];
        
        return $this->all($filters);
    }
    
    public function getStats() {
        try {
            $totalDevices = $this->count();
            $onlineDevices = count($this->getOnlineDevices());
            
            return [
                'total' => $totalDevices,
                'online' => $onlineDevices,
                'offline' => $totalDevices - $onlineDevices,
                'percentage_online' => $totalDevices > 0 ? round(($onlineDevices / $totalDevices) * 100, 1) : 0
            ];
        } catch (Exception $e) {
            return [
                'total' => 0,
                'online' => 0,
                'offline' => 0,
                'percentage_online' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    public function testConnection() {
        try {
            // Test sederhana dengan GET ke endpoint devices
            $response = $this->http->get('/devices', ['limit' => 1]);
            return [
                'success' => true,
                'message' => 'Koneksi ke GenieACS berhasil',
                'data' => $response
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Koneksi ke GenieACS gagal: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function createTaskAlternative($deviceId, $task) {
        // Method alternatif untuk membuat task dengan format berbeda
        
        // Format 1: GenieACS standard
        try {
            $response = $this->http->post("/tasks?device=" . urlencode($deviceId), $task);
            return ['success' => true, 'data' => $response['data'] ?? null];
        } catch (Exception $e1) {
            
            // Format 2: With connection request
            try {
                $response = $this->http->post("/tasks?connection_request", array_merge($task, ['device' => $deviceId]));
                return ['success' => true, 'data' => $response['data'] ?? null];
            } catch (Exception $e2) {
                
                // Format 3: Direct to device
                try {
                    $response = $this->http->post("/devices/" . urlencode($deviceId) . "/tasks", $task);
                    return ['success' => true, 'data' => $response['data'] ?? null];
                } catch (Exception $e3) {
                    
                    // Semua gagal, return error
                    return [
                        'success' => false,
                        'message' => 'Semua format API gagal',
                        'errors' => [
                            'format1' => $e1->getMessage(),
                            'format2' => $e2->getMessage(),
                            'format3' => $e3->getMessage()
                        ]
                    ];
                }
            }
        }
    }
} 