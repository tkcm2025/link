<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Permission;

class PermissionController extends Controller {
    private $permissionModel;

    public function __construct($config) {
        parent::__construct($config);
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('index.php?controller=auth&action=login&error=unauthorized');
            exit;
        }
        // Add role/permission check here later, e.g., if (!user_can('permission_manage')) { ... } 
        // For now, only logged-in users can see.
        $this->permissionModel = $this->loadModel('Permission');
    }

    public function index() {
        $permissions = $this->permissionModel->findAll();
            $this->logger?->log('Permission Management', 'Viewed Permission List');
        $this->renderView('permissions.index', [
            'pageTitle' => '权限管理',
            'permissions' => $permissions
        ]);
    }

    // Optional: A show method if detailed view is needed.
    // For permissions, a detailed view might be simple, just showing the same info as the list.
    public function show($id = null) {
        if ($id === null && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
        }

        if (!$id || $id <= 0) {
            die("Error: Invalid permission ID provided.");
        }

        $permission = $this->permissionModel->findById($id);

        if (!$permission) {
            die("Error: Permission not found.");
        }
        
        $this->renderView('permissions.show', [
            'pageTitle' => '查看权限: ' . htmlspecialchars($permission['name']),
            'permission' => $permission
        ]);
    }
}
