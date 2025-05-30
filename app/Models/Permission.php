<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Permission extends Model {

    public function findAll() {
        $stmt = $this->db->prepare("SELECT id, name, code, description, created_at FROM permissions ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT id, name, code, description, created_at FROM permissions WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
