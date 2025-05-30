<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Link;

class LinkController extends Controller {
    private $linkModel;

    public function __construct($config) {
        parent::__construct($config);
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('index.php?controller=auth&action=login&error=unauthorized');
            exit;
        }
        // Add role/permission check here later, e.g., if (!user_can('link_manage')) { ... }
        $this->linkModel = $this->loadModel('Link');
    }

    public function index() {
        $projectId = isset($_GET['project_id']) ? (int)$_GET['project_id'] : null;
        $links = $this->linkModel->findAll($projectId);
        
        $pageTitle = '链接管理';
        if ($projectId) {
            // Optionally, load project details to display project name
            $projectModel = $this->loadModel('Project');
            $project = $projectModel->findById($projectId);
            if ($project) {
                $pageTitle = htmlspecialchars($project['name']) . ' 项目下的链接';
            } else {
                $pageTitle = '未知项目下的链接';
            }
        }

        $this->logger?->log('Link Management', 'Viewed Link List', ['project_id' => $projectId]);
        $this->renderView('links.index', [
            'pageTitle' => $pageTitle,
            'links' => $links,
            'projectId' => $projectId // Pass projectId to the view for context or breadcrumbs
        ]);
    }

    public function show($id = null) {
        if ($id === null && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
        }

        if (!$id || $id <= 0) {
            die("Error: Invalid link ID provided.");
        }

        $link = $this->linkModel->findById($id);

        if (!$link) {
            die("Error: Link not found.");
        }
        
        // Potentially load associated detection records later
        $detectionRecordModel = $this->loadModel('DetectionRecord');
        $records = $detectionRecordModel->findByLinkId($id);

        $this->logger?->log('Link Management', 'Viewed Link Details', ['link_id' => $id]);
        $this->renderView('links.show', [
            'pageTitle' => '查看链接: ' . htmlspecialchars($link['name']),
            'link' => $link,
            'records' => $records
        ]);
    }

    public function check($id = null) {
        if ($id === null && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
        }

        if (!$id || $id <= 0) {
            die("Error: Invalid link ID for check.");
        }

        $link = $this->linkModel->findById($id);
        if (!$link) {
            die("Error: Link not found for check.");
        }

        // Ensure boce_api_key is loaded in config for DetectionService
        // The DetectionService constructor now attempts to fetch it.
        // It might be better to fetch it here and pass to service or ensure config is fully loaded.
        global $config; // Access global config array (set in index.php)

        // Pass the database connection from the controller's model instance,
        // or ensure Database::getInstance()->getConnection() is used consistently.
        // For simplicity, we pass the connection from the LinkModel which is already initialized.
        $dbConnection = $this->linkModel->getDbConnection(); // Access the protected db property of the Model class.
                                            // This is a bit of a shortcut; ideally, DI or a service locator for DB access.

        $detectionService = new \App\Services\DetectionService($config, $dbConnection);
        $results = $detectionService->checkLink($link['id'], $link['review_url'], $link['landing_url'], $link['name'], $link['created_by']);

        // Store results in session flash message or pass to view
        $_SESSION['flash_message'] = "Link detection initiated. Review URL check: " . ($results['review_url_check']['status'] ?? 'N/A') . 
                                   ". Landing URL check: " . ($results['landing_url_check']['status'] ?? 'N/A');
        
        $this->logger?->log('Link Management', 'Triggered Manual Link Check', ['link_id' => $id]);
        // Redirect back to the link show page
        $this->redirect('index.php?controller=link&action=show&id=' . $id);
    }
}
