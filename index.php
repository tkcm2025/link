<?php

// Define a base path for the application
define('BASE_PATH', __DIR__);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Autoloader
// This assumes composer install (or dump-autoload) has been run and vendor/autoload.php exists.
// If you are not using composer install yet, this will cause an error.
// For now, we will ensure it doesn't break if vendor/autoload.php is missing,
// but it will be required for class autoloading.
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
} else {
    // Fallback or error message if composer autoload is not available
    // For now, we'll try to load classes manually for core functionalities if needed,
    // but ideally, composer dump-autoload should be run.
    // This manual loading is a temporary measure if 'composer install' hasn't been run.
    spl_autoload_register(function ($class) {
        // Basic autoloader for App namespace
        $prefix = 'App\\';
        $base_dir = __DIR__ . '/app/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    });
    // Display a warning if vendor/autoload.php is missing, as it's the preferred way.
    // echo "<p><strong>Warning:</strong> <code>vendor/autoload.php</code> not found. PSR-4 autoloading might not work as expected for all dependencies. Please run <code>composer install</code> or <code>composer dump-autoload</code>.</p>";
}


// Load Configuration
// The Database class already defines CONFIG_PATH and loads it.
// We can load it here as well if other parts of index.php need it directly.
if (file_exists(BASE_PATH . '/config/config.php')) {
    $config = require BASE_PATH . '/config/config.php';
} else {
    die("FATAL ERROR: Configuration file missing. Please ensure config/config.php exists.");
}

// Define BASE_URL from config for convenience
define('BASE_URL', $config['system']['base_url']);

// Basic Routing
// Example: http://localhost/index.php?controller=auth&action=login

$controllerName = isset($_GET['controller']) ? ucfirst(strtolower($_GET['controller'])) . 'Controller' : 'HomeController';
$actionName = isset($_GET['action']) ? strtolower($_GET['action']) : 'index';

$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    // No need to require_once if PSR-4 autoloader is working correctly via composer.
    // If not, the spl_autoload_register above should handle it for App\Controllers namespace.
    
    $fullControllerName = "App\Controllers\" . $controllerName;

    if (class_exists($fullControllerName)) {
        $controllerInstance = new $fullControllerName($config); // Pass config to controller
        if (method_exists($controllerInstance, $actionName)) {
            try {
                $controllerInstance->$actionName();
            } catch (Exception $e) {
                // Log error
                error_log("Error in controller action: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
                // Display generic error page or message
                echo "An unexpected error occurred. Please try again later.";
                // Optionally, include a more detailed error page for development
                // include BASE_PATH . '/app/views/errors/500.php'; 
            }
        } else {
            echo "Error: Action '{$actionName}' not found in controller '{$controllerName}'.";
            // include BASE_PATH . '/app/views/errors/404.php'; // Or a specific error view
        }
    } else {
        echo "Error: Controller class '{$fullControllerName}' not found.";
        // include BASE_PATH . '/app/views/errors/404.php';
    }
} else {
    // Default fallback if no controller is specified or found (e.g., a welcome page)
    // For now, just a simple message. Later, this could be a dedicated HomeController.
    if ($controllerName === 'HomeController' && $actionName === 'index') {
        echo "<h1>Welcome to the Link Detection System!</h1>";
        echo "<p>Please set up your <code>composer.json</code> and run <code>composer install</code> or <code>composer dump-autoload -o</code> to ensure classes are loaded correctly.</p>";
        echo "<p>Try accessing <a href='".BASE_URL."/index.php?controller=auth&action=login'>Login Page (Placeholder)</a></p>";
         // Later, you would instantiate HomeController here if it exists
        // For example:
        // $homeController = new App\Controllers\HomeController($config);
        // $homeController->index();
    } else {
        echo "Error: Controller file '{$controllerFile}' not found.";
        // include BASE_PATH . '/app/views/errors/404.php';
    }
}

?>
