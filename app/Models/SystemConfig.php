<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class SystemConfig extends Model {

    public function getAllAsAssociativeArray() {
        $configs = [];
        $stmt = $this->db->prepare("SELECT `key`, `value` FROM system_configs");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            // Attempt to decode JSON values, otherwise keep as string
            $decodedValue = json_decode($row['value'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $configs[$row['key']] = $decodedValue;
            } else {
                $configs[$row['key']] = $row['value'];
            }
        }
        return $configs;
    }

    public function getValueByKey($key) {
        $stmt = $this->db->prepare("SELECT `value` FROM system_configs WHERE `key` = :key LIMIT 1");
        $stmt->bindParam(':key', $key);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $decodedValue = json_decode($result['value'], true);
            return (json_last_error() === JSON_ERROR_NONE) ? $decodedValue : $result['value'];
        }
        return null;
    }

    public function updateValueByKey($key, $value) {
        // If value is an array or object, encode it as JSON
        if (is_array($value) || is_object($value)) {
            $valueToStore = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            $valueToStore = $value;
        }

        try {
            $stmt = $this->db->prepare("UPDATE system_configs SET `value` = :value, `updated_at` = NOW() WHERE `key` = :key");
            $stmt->bindParam(':value', $valueToStore);
            $stmt->bindParam(':key', $key);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error updating system config key {$key}: " . $e->getMessage());
            return false;
        }
    }
    
    // Helper to update multiple values, potentially from form submission
    public function updateMultiple($settingsArray) {
        $allSuccess = true;
        foreach ($settingsArray as $key => $value) {
            if (!$this->updateValueByKey($key, $value)) {
                $allSuccess = false;
                error_log("Failed to update system config for key: {$key}");
            }
        }
        return $allSuccess;
    }
}
