<?php

namespace App\Services;

use App\Models\OperationLog;

class ActivityLogger {
    private $operationLogModel;
    private $config; // App config
    private $dbConnection;

    public function __construct($config, $dbConnection) {
        $this->config = $config;
        $this->dbConnection = $dbConnection;
        $this->operationLogModel = new OperationLog($this->config);
        $this->operationLogModel->db = $this->dbConnection; // Ensure it uses the passed connection
    }

    public function log($module, $action, $data = null) {
        $userId = $_SESSION['user_id'] ?? null; // Get user ID from session
        
        // If no user_id (e.g., system action or pre-login), attribute to a system user or handle as anonymous.
        // For now, if no user_id, we might skip logging or log with a special user_id if one exists (e.g., 0 for system)
        // However, most actions we're logging now are user-initiated post-login.
        if (!$userId && $module !== 'Authentication' && $action !== 'Login Failed') { 
            // Avoid logging if user is not set, unless it's a login failure
            // For login success, user_id will be set in the AuthController *before* logging.
            error_log("ActivityLogger: Attempted to log action '{$action}' in module '{$module}' without a user_id.");
            return;
        }


        $logData = [
            'user_id'    => $userId,
            'module'     => $module,
            'action'     => $action,
            'ip'         => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
            'data'       => $data ? json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR) : null
        ];
        
        // Special handling for login failures where user_id might not be available yet.
        // The prompt mentions "Log successful login, failed login attempts".
        // For failed login, we might not have a user_id.
        // We can either set a placeholder user_id (e.g., 0 or a guest ID if we have one) or log it without one if the table allows NULL.
        // The table `operation_logs` has `user_id` as NOT NULL. So we need a valid user_id.
        // This means for "Login Failed", we need to decide how to capture it.
        // Option 1: Log it against the 'admin' user (ID 1) if the attempt was for admin.
        // Option 2: Add a guest user_id (e.g. 0) to users table and set operation_logs.user_id to allow null or default to 0.
        // For now, I'll assume login failures are logged *after* identifying the attempted user if possible,
        // or this logger won't log "Login Failed" if user_id cannot be determined.
        // Let's refine this in AuthController.

        $this->operationLogModel->create($logData);
    }
}
