<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class DetectionRecord extends Model {

    public function create($data) {
        $sql = "INSERT INTO detection_records (link_id, type, url, error_code, api_response, created_at) 
                VALUES (:link_id, :type, :url, :error_code, :api_response, NOW())";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':link_id', $data['link_id'], PDO::PARAM_INT);
            $stmt->bindParam(':type', $data['type'], PDO::PARAM_INT);
            $stmt->bindParam(':url', $data['url']);
            $stmt->bindParam(':error_code', $data['error_code'], PDO::PARAM_INT);
            $stmt->bindParam(':api_response', $data['api_response']);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            // Log error
            error_log("Error creating detection record: " . $e->getMessage());
            return false;
        }
    }

    public function findByLinkId($linkId, $limit = 20) {
        $stmt = $this->db->prepare("
            SELECT id, type, url, error_code, api_response, created_at 
            FROM detection_records
            WHERE link_id = :link_id
            ORDER BY created_at DESC
            LIMIT :limit
        ");
        $stmt->bindParam(':link_id', $linkId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
