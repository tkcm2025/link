<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Role extends Model {

    public function findAll() {
        $stmt = $this->db->prepare("SELECT id, name, description, created_at FROM roles ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT id, name, description, created_at FROM roles WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Later, add methods to get permissions associated with a role
    public function getPermissions($roleId) {
        $stmt = $this->db->prepare("
            SELECT p.id, p.name, p.code 
            FROM permissions p
            INNER JOIN role_permission rp ON p.id = rp.permission_id
            WHERE rp.role_id = :role_id
            ORDER BY p.name
        ");
        $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
