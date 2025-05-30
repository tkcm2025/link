<?php

namespace App\Classes;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $conn;

    private $host;
    private $db_name;
    private $username;
    private $password;

    private function __construct() {
        // Define CONFIG_PATH if not already defined (e.g., by a bootstrap file)
        if (!defined('CONFIG_PATH')) {
            // Adjust the path as necessary if this class is located elsewhere
            // This assumes 'app/classes' is the current directory structure
            define('CONFIG_PATH', __DIR__ . '/../../config/config.php');
        }
        
        if (!file_exists(CONFIG_PATH)) {
            die("Configuration file not found. Please create config/config.php.");
        }
        
        $config = require(CONFIG_PATH);

        $this->host = $config['db']['host'];
        $this->db_name = $config['db']['name'];
        $this->username = $config['db']['user'];
        $this->password = $config['db']['pass'];

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // In a real application, you might want to log this error instead of dying
            die("Connection error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup() {}
}
