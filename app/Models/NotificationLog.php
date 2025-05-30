<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class NotificationLog extends Model {

    public function create($data) {
        $sql = "INSERT INTO notification_logs (user_id, type, content, status, error_message, detection_record_id, created_at)
                VALUES (:user_id, :type, :content, :status, :error_message, :detection_record_id, NOW())";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':type', $data['type'], PDO::PARAM_INT); // 1 for email, 2 for SMS, 3 for WeChat
            $stmt->bindParam(':content', $data['content']);
            $stmt->bindParam(':status', $data['status'], PDO::PARAM_INT); // 1 for success, 0 for failure
            $stmt->bindParam(':error_message', $data['error_message']);
            $stmt->bindParam(':detection_record_id', $data['detection_record_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Error creating notification log: " . $e->getMessage());
            return false;
        }
    }
}
