<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class NotificationConfig extends Model {

    public function findByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM notification_configs WHERE user_id = :user_id LIMIT 1");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $config = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($config && !empty($config['scenarios'])) {
            $config['scenarios_decoded'] = json_decode($config['scenarios'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Failed to decode scenarios JSON for user_id: {$userId}. Error: " . json_last_error_msg());
                $config['scenarios_decoded'] = []; // Default to empty if JSON is invalid
            }
        } else if ($config) {
            $config['scenarios_decoded'] = []; // Default to empty if scenarios field is empty/null
        }
        return $config;
    }
    
    // Methods for CRUD to be added later for admin panel management
    // public function createOrUpdate($userId, $data) { ... }
}
