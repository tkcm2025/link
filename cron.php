<?php

// Cron Job Script for Link Detection System

// Only allow CLI access
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

echo "Cron job started at: " . date('Y-m-d H:i:s') . "\n";

// Define BASE_PATH if not already defined (it should be by virtue of being in root)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

// Setup error logging specifically for this cron script
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/logs/cron_errors.log'); // Ensure 'logs' directory is writable
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to stdout, log them

// Autoloader
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
} else {
    echo "FATAL ERROR: vendor/autoload.php not found. Please run 'composer install'.\n";
    error_log("Cron Error: vendor/autoload.php not found.");
    exit(1);
}

// Load Configuration
if (file_exists(BASE_PATH . '/config/config.php')) {
    $config = require BASE_PATH . '/config/config.php';
} else {
    echo "FATAL ERROR: Configuration file missing (config/config.php).\n";
    error_log("Cron Error: Configuration file missing (config/config.php).");
    exit(1);
}

// Define BASE_URL from config for any services that might need it (though less common in CLI)
if (isset($config['system']['base_url'])){
    define('BASE_URL', $config['system']['base_url']);
} else {
    define('BASE_URL', 'http://localhost'); // Fallback, should be configured
}


// Get DB Connection (Models will get this via Database::getInstance()->getConnection())
// No need to explicitly pass it around if models handle their own instantiation correctly.
// However, DetectionService was designed to accept a dbConnection.
try {
    $dbConnection = App\Classes\Database::getInstance()->getConnection();
} catch (\PDOException $e) {
    echo "FATAL ERROR: Database connection failed: " . $e->getMessage() . "\n";
    error_log("Cron DB Connection Error: " . $e->getMessage());
    exit(1);
}


// Initialize necessary models and services
try {
    $linkModel = new App\Models\Link($config); // Config might be used by model constructor
    $linkModel->db = $dbConnection; // Ensure model uses the established connection

    // The DetectionService expects the $config array and a $dbConnection.
    $detectionService = new App\Services\DetectionService($config, $dbConnection);

} catch (\Exception $e) {
    echo "FATAL ERROR: Failed to initialize models/services: " . $e->getMessage() . "\n";
    error_log("Cron Initialization Error: " . $e->getMessage());
    exit(1);
}

// Fetch active links
$activeLinks = [];
try {
    $activeLinks = $linkModel->findAll(); // This now fetches only active links including l.created_by
} catch (\PDOException $e) {
    echo "ERROR: Could not fetch active links: " . $e->getMessage() . "\n";
    error_log("Cron DB Query Error (fetch links): " . $e->getMessage());
    exit(1); // Exit if we can't get links
}

if (empty($activeLinks)) {
    echo "No active links found to check.\n";
    exit(0);
}

echo "Found " . count($activeLinks) . " active links to check.\n";
$checkedCount = 0;
$errorCount = 0;

foreach ($activeLinks as $link) {
    echo "Checking link ID: {$link['id']} - Name: '" . htmlspecialchars($link['name']) . "' (Review: {$link['review_url']}, Landing: {$link['landing_url']})...\n";
    try {
        // Ensure all necessary parameters are passed to checkLink
        // $link array from LinkModel::findAll() now includes 'id', 'review_url', 'landing_url', 'name', 'created_by'.
        
        if (!isset($link['created_by'])) { // This check should ideally not be needed if findAll guarantees it
            echo "Warning: Missing created_by for link ID {$link['id']}. Notifications might be affected.\n";
            error_log("Cron Warning: Missing created_by for link ID {$link['id']}.");
        }

        $results = $detectionService->checkLink(
            $link['id'],
            $link['review_url'],
            $link['landing_url'],
            $link['name'],
            $link['created_by'] ?? null // Pass user_id of link creator for notifications
        );
        
        // Log results briefly
        echo "  Review URL check: " . ($results['review_url_check']['status_report']['status'] ?? 'N/A') . 
             " (DB Code: " . ($results['review_url_check']['status_report']['db_error_code'] ?? 'N/A') . ")\n";
        echo "  Landing URL check: " . ($results['landing_url_check']['status_report']['status'] ?? 'N/A') . 
             " (DB Code: " . ($results['landing_url_check']['status_report']['db_error_code'] ?? 'N/A') . ")\n";
        
        $checkedCount++;
        
        // Optional: Add a small delay to avoid overwhelming the API or server
        // sleep(1); 

    } catch (\Exception $e) {
        echo "ERROR checking link ID {$link['id']}: " . $e->getMessage() . "\n";
        error_log("Cron Error (checking link ID {$link['id']}): " . $e->getMessage());
        $errorCount++;
    }
}

echo "Cron job finished at: " . date('Y-m-d H:i:s') . "\n";
echo "Total links checked: $checkedCount. Errors during checks: $errorCount.\n";
exit(0);
