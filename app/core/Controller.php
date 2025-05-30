<?php

namespace App\Core;

abstract class Controller {
    protected $config;

    public function __construct($config = null) {
        $this->config = $config ?: $GLOBALS['config']; // Use global config if not passed
    }

    protected function loadModel($modelName) {
        $modelClass = "App\Models\" . ucfirst($modelName);
        if (class_exists($modelClass)) {
            // Pass config to model constructor if needed, or rely on global config
            return new $modelClass($this->config); 
        } else {
            // Log error or throw exception
            error_log("Model not found: " . $modelClass);
            die("Error: Model {$modelName} could not be loaded."); // Simple error handling for now
        }
        return null;
    }

    protected function renderView($viewName, $data = []) {
        // Construct the path to the view file
        // Assumes views are in 'app/views/' and may have subdirectories
        $viewFile = BASE_PATH . '/app/views/' . str_replace('.', '/', $viewName) . '.php';

        if (file_exists($viewFile)) {
            // Extract data array to variables for use in the view
            extract($data);

            // Start output buffering
            ob_start();

            // Include the view file
            include $viewFile;

            // Get the content of the buffer
            $content = ob_get_clean();

            // For now, just echo the content. 
            // Later, this could return the content to be embedded in a layout.
            echo $content; 
        } else {
            // Log error or throw exception
            error_log("View not found: " . $viewFile);
            die("Error: View {$viewName} could not be rendered. File missing: {$viewFile}"); // Simple error handling
        }
    }
    
    protected function redirect($url, $statusCode = 302) {
        // Ensure the URL is absolute or correctly relative to BASE_URL
        if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
            $url = BASE_URL . '/' . ltrim($url, '/');
        }
        header('Location: ' . $url, true, $statusCode);
        exit;
    }
}
