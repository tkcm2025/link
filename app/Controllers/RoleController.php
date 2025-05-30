<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Role;

class RoleController extends Controller {
    private $roleModel;

    public function __construct($config) {
        parent::__construct($config);
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('index.php?controller=auth&action=login&error=unauthorized');
            exit;
        }
        // Add role/permission check here later, e.g., if (!user_can('role_manage')) { ... }
        $this->roleModel = $this->loadModel('Role');
    }

    public function index() {
        $roles = $this->roleModel->findAll();
            $this->logger?->log('Role Management', 'Viewed Role List');
        $this->renderView('roles.index', [
            'pageTitle' => '角色管理',
            'roles' => $roles
        ]);
    }

    public function show($id = null) {
        if ($id === null && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
        }

        if (!$id || $id <= 0) {
            die("Error: Invalid role ID provided.");
        }

        $role = $this->roleModel->findById($id);

        if (!$role) {
            die("Error: Role not found.");
        }
        
        $permissions = $this->roleModel->getPermissions($id);

            $this->logger?->log('Role Management', 'Viewed Role Details', ['role_id' => $id]);
        $this->renderView('roles.show', [
            'pageTitle' => '查看角色: ' . htmlspecialchars($role['name']),
            'role' => $role,
            'permissions' => $permissions
        ]);
    }
}
