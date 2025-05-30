<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SystemConfig;

class SettingController extends Controller {
    private $systemConfigModel;

    public function __construct($config) {
        parent::__construct($config);
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('index.php?controller=auth&action=login&error=unauthorized');
            exit;
        }
        // Basic permission check - ideally use a proper role/permission system
        // For now, let's assume only admin (user_id 1) can access.
        // This should be replaced with a call to a permission checking service based on 'system_config' permission code.
        if ($_SESSION['user_id'] != 1 && !$this->checkUserPermission('system_config')) {
             $this->logger?->log('System Settings', 'Access Denied', ['user_id' => $_SESSION['user_id']]);
             $_SESSION['flash_message'] = ['type' => 'danger', 'text' => '权限不足，无法访问系统配置。'];
             $this->redirect('index.php?controller=home&action=index');
             exit;
        }
        $this->systemConfigModel = $this->loadModel('SystemConfig');
    }
    
    // Dummy permission check function - replace with real one later
    private function checkUserPermission($permissionCode) {
        // Placeholder: In a real app, query user_role, role_permission tables.
        // For now, if user_id 1 (admin), always allow.
        if ($_SESSION['user_id'] == 1) return true;

        // This is a simplified check. A full check would involve:
        // 1. Get user's roles from user_role table.
        // 2. For each role, get permissions from role_permission table.
        // 3. Check if $permissionCode is among them.
        // Example:
        // $userRoles = $this->loadModel('UserRole')->findByUserId($_SESSION['user_id']);
        // foreach ($userRoles as $userRole) {
        //    $rolePermissions = $this->loadModel('RolePermission')->findByRoleId($userRole['role_id']);
        //    foreach ($rolePermissions as $rp) {
        //        $permission = $this->loadModel('Permission')->findById($rp['permission_id']);
        //        if ($permission && $permission['code'] === $permissionCode) return true;
        //    }
        // }
        return false; // Default to deny if not admin and no proper check implemented
    }


    public function index() {
        $settings = $this->systemConfigModel->getAllAsAssociativeArray();
        $this->logger?->log('System Settings', 'Viewed Settings Page');
        $this->renderView('settings.index', [
            'pageTitle' => '系统配置',
            'settings' => $settings
        ]);
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=setting&action=index');
            exit;
        }

        $formData = $_POST;
        $settingsToUpdate = [];

        // Specific handling for structured JSON settings like email_config
        if (isset($formData['email_config_host'])) { // Check if individual fields were submitted
            $settingsToUpdate['email_config'] = [
                'host' => $formData['email_config_host'] ?? '',
                'port' => (int)($formData['email_config_port'] ?? 0),
                'username' => $formData['email_config_username'] ?? '',
                'password' => $formData['email_config_password'] ?? '', // Password might need special handling (e.g. don't display, only update if changed)
                'from' => $formData['email_config_from'] ?? '', // 'from' was in original SQL, map to 'from_address' if needed
                'from_address' => $formData['email_config_from'] ?? '', // Consistent with config.php
                'from_name' => $formData['email_config_from_name'] ?? 'Link Detection System',
                'encryption' => $formData['email_config_encryption'] ?? 'ssl', // PHPMailer::ENCRYPTION_SMTPS or PHPMailer::ENCRYPTION_STARTTLS
            ];
            // Remove individual fields from formData to avoid treating them as separate keys
            unset($formData['email_config_host'], $formData['email_config_port'], $formData['email_config_username'], 
                  $formData['email_config_password'], $formData['email_config_from'], $formData['email_config_from_name'], $formData['email_config_encryption']);
        }


        // Generic handling for other settings
        foreach ($formData as $key => $value) {
            // Skip CSRF token or other non-setting fields if any
            if ($key === 'csrf_token') continue; 
            
            // If the key corresponds to a known JSON structure but wasn't handled above (e.g. sms_config, wechat_config as textarea)
            // ensure it's stored as a string, and the model will handle JSON decoding on retrieval.
            // The model's updateValueByKey will re-encode if it's an array, so direct string is fine here.
            $settingsToUpdate[$key] = $value;
        }
        
        // Handle password separately: if email_config_password is empty, don't update it.
        // This is a common pattern to avoid overwriting a password with an empty string if the field was left blank.
        if (isset($settingsToUpdate['email_config']) && empty($settingsToUpdate['email_config']['password'])) {
            $currentEmailConfig = $this->systemConfigModel->getValueByKey('email_config');
            if (isset($currentEmailConfig['password'])) {
                $settingsToUpdate['email_config']['password'] = $currentEmailConfig['password'];
            }
        }


        if ($this->systemConfigModel->updateMultiple($settingsToUpdate)) {
             $this->logger?->log('System Settings', 'Settings Updated', ['updated_keys' => array_keys($settingsToUpdate)]);
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => '系统配置已成功更新。'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => '更新系统配置失败。'];
        }
        
        // Re-populate $config global after update (important!)
        // This requires modifying how $config is loaded in index.php or having a reload mechanism.
        // For now, a page reload will effectively do this if $config is loaded on each request.

        $this->redirect('index.php?controller=setting&action=index');
    }
}
