<?php

class Devices {
    private $deviceModel;
    
    public function __construct() {
        $this->deviceModel = new Device();
    }
    
    public function index() {
        try {
            $page = max(1, intval($_GET['page'] ?? 1));
            $search = $_GET['search'] ?? '';
            $status = $_GET['status'] ?? '';
            $limit = Config::get('PAGINATION_LIMIT');
            $skip = ($page - 1) * $limit;
            
            $filters = [];
            
            // Apply search filter
            if (!empty($search)) {
                $filters['$or'] = [
                    ['_id' => ['$regex' => $search, '$options' => 'i']],
                    ['summary.manufacturer' => ['$regex' => $search, '$options' => 'i']],
                    ['summary.modelName' => ['$regex' => $search, '$options' => 'i']]
                ];
            }
            
            // Apply status filter
            if ($status === 'online') {
                $fiveMinutesAgo = new DateTime();
                $fiveMinutesAgo->sub(new DateInterval('PT5M'));
                $filters['_lastInform'] = ['$gte' => $fiveMinutesAgo->format('c')];
            } elseif ($status === 'offline') {
                $fiveMinutesAgo = new DateTime();
                $fiveMinutesAgo->sub(new DateInterval('PT5M'));
                $filters['_lastInform'] = ['$lt' => $fiveMinutesAgo->format('c')];
            }
            
            $devices = $this->deviceModel->all($filters, $limit, $skip);
            $totalDevices = $this->deviceModel->count($filters);
            $totalPages = ceil($totalDevices / $limit);
            
            // Process devices data
            $processedDevices = [];
            foreach ($devices as $device) {
                $deviceId = $device['_id'] ?? 'unknown';
                $lastInform = isset($device['_lastInform']) ? date('Y-m-d H:i:s', strtotime($device['_lastInform'])) : null;
                
                // Determine status
                $status = 'offline';
                if ($lastInform) {
                    $lastInformTime = strtotime($device['_lastInform']);
                    $now = time();
                    $timeDiff = $now - $lastInformTime;
                    
                    if ($timeDiff <= 300) { // 5 minutes
                        $status = 'online';
                    } elseif ($timeDiff <= 3600) { // 1 hour
                        $status = 'recently_online';
                    }
                }
                
                $processedDevices[] = [
                    'id' => $deviceId,
                    'serialNumber' => $device['_deviceId']['_SerialNumber'] ?? 'Unknown',
                    'manufacturer' => $device['_deviceId']['_Manufacturer'] ?? 'Unknown',
                    'model' => $device['_deviceId']['_ProductClass'] ?? 'Unknown',
                    'lastInform' => $lastInform,
                    'status' => $status,
                    'virtualParams' => $device['VirtualParameters'] ?? []
                ];
            }
            
            $data = [
                'devices' => $processedDevices,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_devices' => $totalDevices,
                    'per_page' => $limit,
                    'has_prev' => $page > 1,
                    'has_next' => $page < $totalPages,
                    'prev_page' => $page - 1,
                    'next_page' => $page + 1
                ],
                'filters' => [
                    'search' => $search,
                    'status' => $status
                ],
                'pageTitle' => 'Perangkat',
                'error' => null
            ];
            
        } catch (Exception $e) {
            $data = [
                'devices' => [],
                'pagination' => [
                    'current_page' => 1,
                    'total_pages' => 0,
                    'total_devices' => 0,
                    'per_page' => $limit ?? 20,
                    'has_prev' => false,
                    'has_next' => false,
                    'prev_page' => 0,
                    'next_page' => 0
                ],
                'filters' => [
                    'search' => $search ?? '',
                    'status' => $status ?? ''
                ],
                'pageTitle' => 'Perangkat',
                'error' => $e->getMessage()
            ];
        }
        
        // Extract variables for view
        extract($data);
        include __DIR__ . '/../views/devices.php';
    }
    
    public function show($deviceId) {
        try {
            $device = $this->deviceModel->show($deviceId);
            
            if (!$device) {
                // 404 page
                http_response_code(404);
                $pageTitle = '404 - Perangkat Tidak Ditemukan';
                include __DIR__ . '/../views/errors/404.php';
                return;
            }
            
            $info = $this->deviceModel->getDeviceInfo($deviceId);
            
            // Get all parameters for advanced view
            $parameters = [];
            foreach ($device as $key => $value) {
                if (strpos($key, '_') !== 0) { // Skip internal fields
                    $parameters[$key] = [
                        'value' => is_array($value) ? ($value['_value'] ?? json_encode($value)) : $value,
                        'type' => is_array($value) ? ($value['_type'] ?? 'object') : gettype($value),
                        'writable' => is_array($value) ? ($value['_writable'] ?? false) : false,
                        'timestamp' => is_array($value) ? ($value['_timestamp'] ?? '') : ''
                    ];
                }
            }
            
            $data = [
                'device' => $device,
                'info' => $info,
                'parameters' => $parameters,
                'pageTitle' => 'Detail Perangkat - ' . $deviceId,
                'error' => null
            ];
            
        } catch (Exception $e) {
            $data = [
                'device' => null,
                'info' => null,
                'parameters' => [],
                'pageTitle' => 'Detail Perangkat',
                'error' => $e->getMessage()
            ];
        }
        
        // Extract variables for view
        extract($data);
        include __DIR__ . '/../views/device_detail.php';
    }
    
    public function update($deviceId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('devices', ['action' => 'detail', 'id' => $deviceId]));
            exit;
        }
        
        try {
            $parameters = $_POST['parameters'] ?? [];
            
            if (!empty($parameters)) {
                $result = $this->deviceModel->setParameters($deviceId, $parameters);
                
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'message' => 'Parameter berhasil diperbarui. Task telah dibuat.'
                ];
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'warning',
                    'message' => 'Tidak ada parameter yang diperbarui.'
                ];
            }
            
        } catch (Exception $e) {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
        
        header('Location: ' . url('devices', ['action' => 'detail', 'id' => $deviceId]));
        exit;
    }
    
    public function createTask($deviceId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('devices', ['action' => 'detail', 'id' => $deviceId]));
            exit;
        }
        
        try {
            $taskType = $_POST['task_type'] ?? '';
            
            switch ($taskType) {
                case 'refresh':
                    $this->deviceModel->refreshDevice($deviceId);
                    $message = 'Refresh task berhasil dibuat.';
                    break;
                    
                case 'reboot':
                    $this->deviceModel->rebootDevice($deviceId);
                    $message = 'Reboot task berhasil dibuat.';
                    break;
                    
                case 'factory_reset':
                    $this->deviceModel->factoryResetDevice($deviceId);
                    $message = 'Factory reset task berhasil dibuat.';
                    break;
                    
                default:
                    throw new Exception('Tipe task tidak valid.');
            }
            
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => $message
            ];
            
        } catch (Exception $e) {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
        
        header('Location: ' . url('devices', ['action' => 'detail', 'id' => $deviceId]));
        exit;
    }
    
    public function refreshDevice($deviceId) {
        try {
            $result = $this->deviceModel->refreshDevice($deviceId);
            return ['success' => true, 'message' => 'Refresh task berhasil dibuat.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function rebootDevice($deviceId) {
        try {
            $result = $this->deviceModel->rebootDevice($deviceId);
            return ['success' => true, 'message' => 'Reboot task berhasil dibuat.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function processDeviceInfo($device) {
        return [
            'id' => $device['_id'] ?? '',
            'manufacturer' => $device['summary']['manufacturer'] ?? 'Unknown',
            'model' => $device['summary']['modelName'] ?? 'Unknown',
            'softwareVersion' => $device['summary']['softwareVersion'] ?? 'Unknown',
            'lastInform' => $device['_lastInform'] ?? '',
            'status' => $this->determineStatus($device['_lastInform'] ?? ''),
            'ipAddress' => $this->extractIpAddress($device),
            'raw' => $device
        ];
    }
    
    private function determineStatus($lastInform) {
        if (empty($lastInform)) {
            return 'Unknown';
        }
        
        try {
            $lastInformTime = new DateTime($lastInform);
            $now = new DateTime();
            $diff = $now->getTimestamp() - $lastInformTime->getTimestamp();
            
            if ($diff < 300) { // 5 minutes
                return 'Online';
            } elseif ($diff < 3600) { // 1 hour
                return 'Recently Online';
            } else {
                return 'Offline';
            }
        } catch (Exception $e) {
            return 'Unknown';
        }
    }
    
    private function extractIpAddress($device) {
        // Try to extract IP from various possible fields
        $possibleFields = [
            'ManagementServer.ConnectionRequestURL',
            'DeviceInfo.X_D-Link_IPAddress',
            'LAN.IPAddress'
        ];
        
        foreach ($possibleFields as $field) {
            if (isset($device[$field])) {
                $value = is_array($device[$field]) ? ($device[$field]['_value'] ?? '') : $device[$field];
                
                if (preg_match('/(\d+\.\d+\.\d+\.\d+)/', $value, $matches)) {
                    return $matches[1];
                }
            }
        }
        
        return 'Unknown';
    }
} 