<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Project extends Model {

    public function findAll() {
        // Fetches projects along with the username of the creator
        $stmt = $this->db->prepare("
            SELECT p.id, p.name, p.description, p.status, p.created_at, p.updated_at, u.username as created_by_username
            FROM projects p
            JOIN users u ON p.created_by = u.id
            ORDER BY p.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        // Fetches a single project along with the username of the creator
        $stmt = $this->db->prepare("
            SELECT p.id, p.name, p.description, p.status, p.created_by, p.created_at, p.updated_at, u.username as created_by_username
            FROM projects p
            JOIN users u ON p.created_by = u.id
            WHERE p.id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Add methods for creating, updating, deleting projects later
    // public function create($data) { ... }
    // public function update($id, $data) { ... }
    // public function delete($id) { ... }
}
