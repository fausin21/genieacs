<?php

class HttpClient {
    private $baseUrl;
    private $username;
    private $password;
    private $timeout;
    
    public function __construct() {
        $this->baseUrl = rtrim(Config::get('GENIE_BASE'), '/');
        $this->username = Config::get('GENIE_USERNAME');
        $this->password = Config::get('GENIE_PASSWORD');
        $this->timeout = 30;
    }
    
    public function get($endpoint, $params = []) {
        $url = $this->baseUrl . $endpoint;
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return $this->makeRequest('GET', $url);
    }
    
    public function post($endpoint, $data = []) {
        $url = $this->baseUrl . $endpoint;
        return $this->makeRequest('POST', $url, $data);
    }
    
    public function put($endpoint, $data = []) {
        $url = $this->baseUrl . $endpoint;
        return $this->makeRequest('PUT', $url, $data);
    }
    
    public function delete($endpoint) {
        $url = $this->baseUrl . $endpoint;
        return $this->makeRequest('DELETE', $url);
    }
    
    private function makeRequest($method, $url, $data = null) {
        $ch = curl_init();
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        
        // Add authentication if credentials are provided
        if (!empty($this->username) && !empty($this->password)) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        }
        
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
                
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
                
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }
        
        $result = [
            'status_code' => $httpCode,
            'data' => json_decode($response, true),
            'raw' => $response
        ];
        
        if ($httpCode >= 400) {
            $errorMessage = "HTTP Error {$httpCode}";
            
            // Parse JSON error message if available
            $errorData = json_decode($response, true);
            if ($errorData && isset($errorData['message'])) {
                $errorMessage .= ": " . $errorData['message'];
            } elseif ($errorData && isset($errorData['error'])) {
                $errorMessage .= ": " . $errorData['error'];
            } else {
                $errorMessage .= ": " . $response;
            }
            
            // Add specific handling for common errors
            switch ($httpCode) {
                case 405:
                    $errorMessage = "Method Not Allowed - Periksa endpoint API dan method HTTP yang digunakan";
                    break;
                case 404:
                    $errorMessage = "API Endpoint tidak ditemukan - Periksa URL GenieACS";
                    break;
                case 401:
                    $errorMessage = "Unauthorized - Periksa username/password GenieACS";
                    break;
                case 500:
                    $errorMessage = "Server Error - GenieACS mengalami masalah internal";
                    break;
            }
            
            throw new Exception($errorMessage);
        }
        
        return $result;
    }
    
    public function setTimeout($timeout) {
        $this->timeout = $timeout;
        return $this;
    }
} 