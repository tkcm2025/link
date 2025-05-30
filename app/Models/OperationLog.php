<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class OperationLog extends Model {

    public function create($data) {
        $sql = "INSERT INTO operation_logs (user_id, module, action, ip, user_agent, data, created_at)
                VALUES (:user_id, :module, :action, :ip, :user_agent, :data, NOW())";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':module', $data['module']);
            $stmt->bindParam(':action', $data['action']);
            $stmt->bindParam(':ip', $data['ip']);
            $stmt->bindParam(':user_agent', $data['user_agent']);
            $stmt->bindParam(':data', $data['data']); // Can be JSON string or text
            
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Error creating operation log: " . $e->getMessage() . " Data: " . print_r($data, true));
            return false;
        }
    }

    public function findAllPaginated($page = 1, $limit = 20, $filters = []) {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT ol.*, u.username 
                FROM operation_logs ol 
                JOIN users u ON ol.user_id = u.id";
        
        // Add filters here later (e.g., WHERE module = :module)
        $sql .= " ORDER BY ol.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalCount($filters = []) {
        $sql = "SELECT COUNT(*) FROM operation_logs";
        // Add filters here later
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
