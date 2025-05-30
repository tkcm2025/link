<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller {
    private $userModel;

    public function __construct($config) {
        parent::__construct($config);
        // Basic authentication check for all user actions
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('index.php?controller=auth&action=login&error=unauthorized');
            exit; // Ensure no further code execution
        }
        // Add role/permission check here later, e.g., if (!user_can('user_manage')) { ... }
        $this->userModel = $this->loadModel('User');
    }

    public function index() {
        $users = $this->userModel->findAll();
        $this->renderView('users.index', [
            'pageTitle' => '用户管理',
            'users' => $users
        ]);
    }

    public function show($id = null) {
        if ($id === null && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
        }

        if (!$id || $id <= 0) {
            // Or redirect to index with an error message
            die("Error: Invalid user ID provided."); 
        }

        $user = $this->userModel->findById($id);

        if (!$user) {
            // Or redirect to index with an error message
            die("Error: User not found.");
        }
        
        $this->renderView('users.show', [
            'pageTitle' => '查看用户: ' . htmlspecialchars($user['username']),
            'user' => $user
        ]);
    }
}
