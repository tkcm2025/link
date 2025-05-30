<?php

// Database Configuration
define('DB_HOST', '127.0.0.1'); // Or your MySQL host, e.g., 'localhost'
define('DB_NAME', 'link_detection_system'); // Choose a database name
define('DB_USER', 'root');
define('DB_PASS', '987123');

// API Keys - Placeholders, to be configured in the admin panel later
define('BOCE_API_KEY', ''); // From system_configs table, but good to have a default/fallback if needed

// Notification Configurations - Placeholders or default values
// These will also be managed via the database (system_configs table)
// but can have initial defaults here if necessary for setup.

// Email (QQ)
define('EMAIL_HOST', 'smtp.qq.com');
define('EMAIL_PORT', 465);
define('EMAIL_USERNAME', '');
define('EMAIL_PASSWORD', '');
define('EMAIL_FROM_ADDRESS', '');
define('EMAIL_FROM_NAME', 'Link Detection System');

// SMS (Aliyun)
define('SMS_ACCESS_KEY_ID', '');
define('SMS_ACCESS_KEY_SECRET', '');
define('SMS_SIGN_NAME', '');
define('SMS_TEMPLATE_CODE', '');

// WeChat (Official Account)
define('WECHAT_APP_ID', '');
define('WECHAT_APP_SECRET', '');
define('WECHAT_TEMPLATE_ID', ''); // For specific message templates

// System Settings
define('DETECTION_FREQUENCY', 3600); // Default detection frequency in seconds (1 hour)
define('THEME_COLOR', 'blue'); // Default theme color
define('SITE_ROOT', __DIR__ . '/..'); // Project root directory
define('BASE_URL', 'http://localhost/link-detection-system'); // Change this to your actual base URL

// Error reporting - Recommended for development
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 in production
ini_set('log_errors', 1);
ini_set('error_log', SITE_ROOT . '/logs/php_errors.log'); // Ensure 'logs' directory is writable

// Return a configuration array for the Database class and other parts of the application
return [
    'db' => [
        'host' => DB_HOST,
        'name' => DB_NAME,
        'user' => DB_USER,
        'pass' => DB_PASS,
    ],
    'api' => [
        'boce_api_key' => BOCE_API_KEY,
    ],
    'email' => [
        'host' => EMAIL_HOST,
        'port' => EMAIL_PORT,
        'username' => EMAIL_USERNAME,
        'password' => EMAIL_PASSWORD,
        'from_address' => EMAIL_FROM_ADDRESS,
        'from_name' => EMAIL_FROM_NAME,
    ],
    'sms' => [
        'access_key_id' => SMS_ACCESS_KEY_ID,
        'access_key_secret' => SMS_ACCESS_KEY_SECRET,
        'sign_name' => SMS_SIGN_NAME,
        'template_code' => SMS_TEMPLATE_CODE,
    ],
    'wechat' => [
        'app_id' => WECHAT_APP_ID,
        'app_secret' => WECHAT_APP_SECRET,
        'template_id' => WECHAT_TEMPLATE_ID,
    ],
    'system' => [
        'detection_frequency' => DETECTION_FREQUENCY,
        'theme_color' => THEME_COLOR,
        'site_root' => SITE_ROOT,
        'base_url' => BASE_URL, // Important for generating correct URLs
    ],
];
