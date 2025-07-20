<?php

class Config {
    private static $config = [
        'GENIE_BASE' => 'http://192.168.99.134:7557',
        'GENIE_USERNAME' => '',
        'GENIE_PASSWORD' => '',
        
        'APP_NAME' => 'GenieACS Portal',
        'APP_VERSION' => '1.0.0',
        'APP_DESCRIPTION' => 'Professional GenieACS Management Portal',
        
        'TIMEZONE' => 'Asia/Jakarta',
        'DATE_FORMAT' => 'Y-m-d H:i:s',
        
        'PAGINATION_LIMIT' => 20,
        'SESSION_TIMEOUT' => 3600, // 1 hour
        
        'DEBUG' => true
    ];
    
    public static function get($key = null) {
        if ($key === null) {
            return self::$config;
        }
        
        return isset(self::$config[$key]) ? self::$config[$key] : null;
    }
    
    public static function set($key, $value) {
        self::$config[$key] = $value;
    }
    
    public static function init() {
        date_default_timezone_set(self::get('TIMEZONE'));
        
        if (self::get('DEBUG')) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
} 