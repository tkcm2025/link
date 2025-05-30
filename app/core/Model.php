<?php

namespace App\Core;

use App\Classes\Database;
use PDO;

abstract class Model {
    protected $db;
    protected $config;

    public function __construct($config = null) {
        $this->config = $config ?: $GLOBALS['config']; // Use global config if not passed
        $this->db = Database::getInstance()->getConnection();
    }

    // Common database operations can be added here later if needed
    // For example, a method to find by ID, etc.
}
