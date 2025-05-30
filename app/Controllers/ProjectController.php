<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Project;

class ProjectController extends Controller {
    private $projectModel;

    public function __construct($config) {
        parent::__construct($config);
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('index.php?controller=auth&action=login&error=unauthorized');
            exit;
        }
        // Add role/permission check here later, e.g., if (!user_can('project_manage')) { ... }
        $this->projectModel = $this->loadModel('Project');
    }

    public function index() {
        $projects = $this->projectModel->findAll();
            $this->logger?->log('Project Management', 'Viewed Project List');
        $this->renderView('projects.index', [
            'pageTitle' => '项目管理',
            'projects' => $projects
        ]);
    }

    public function show($id = null) {
        if ($id === null && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
        }

        if (!$id || $id <= 0) {
            die("Error: Invalid project ID provided.");
        }

        $project = $this->projectModel->findById($id);

        if (!$project) {
            die("Error: Project not found.");
        }
        
        // Potentially load associated links for this project later
            $linkModel = $this->loadModel('Link');
            $links = $linkModel->findByProjectId($id);

            $this->logger?->log('Project Management', 'Viewed Project Details', ['project_id' => $id]);
            $this->renderView('projects.show', [
                'pageTitle' => '查看项目: ' . htmlspecialchars($project['name']),
                'project' => $project,
                'links' => $links 
        ]);
    }
}
