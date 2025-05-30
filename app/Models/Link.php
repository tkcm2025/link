<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Link extends Model {

    public function findAll($projectId = null) {
        $sql = "
            SELECT 
                l.id, l.name, l.review_url, l.landing_url, l.last_check_time, l.status,
                p.name as project_name, u.username as created_by_username, l.project_id, l.created_at
            FROM links l
            JOIN projects p ON l.project_id = p.id
            JOIN users u ON l.created_by = u.id
        ";
        if ($projectId) {
            $sql .= " WHERE l.project_id = :project_id";
        }
        $sql .= " ORDER BY l.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($projectId) {
            $stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT 
                l.id, l.project_id, l.name, l.review_url, l.landing_url, 
                l.last_check_time, l.status, l.created_by, l.created_at, l.updated_at,
                p.name as project_name, u.username as created_by_username
            FROM links l
            JOIN projects p ON l.project_id = p.id
            JOIN users u ON l.created_by = u.id
            WHERE l.id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findByProjectId($projectId) {
         $stmt = $this->db->prepare("
            SELECT 
                l.id, l.name, l.review_url, l.landing_url, l.last_check_time, l.status,
                u.username as created_by_username, l.created_at
            FROM links l
            JOIN users u ON l.created_by = u.id
            WHERE l.project_id = :project_id
            ORDER BY l.created_at DESC
        ");
        $stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateLastCheckTime($linkId) {
        try {
            $stmt = $this->db->prepare("UPDATE links SET last_check_time = NOW() WHERE id = :id");
            $stmt->bindParam(':id', $linkId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Error updating link last_check_time: " . $e->getMessage());
            return false;
        }
    }
    // Add methods for creating, updating, deleting links later
}
