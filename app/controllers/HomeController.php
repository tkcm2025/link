<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    
    public function __construct($config) {
        parent::__construct($config);
        // Check if user is logged in, otherwise redirect to login
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('index.php?controller=auth&action=login');
        }
    }

    public function index() {
        // Data to pass to the view
        $data = [
            'pageTitle' => '仪表盘',
            'welcomeMessage' => '欢迎回来, ' . htmlspecialchars($_SESSION['username']) . '!'
        ];
        $this->renderView('home.index', $data);
    }
}
