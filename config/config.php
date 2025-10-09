<?php
// Debug Mode
define('DEBUG_MODE', true); // Set to false in production

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'military_logistics');

// App Root
define('APP_ROOT', dirname(dirname(__FILE__)));
define('URL_ROOT', 'http://localhost/military_logistics');
define('SITE_NAME', 'Military Logistics System');

// Version
define('APP_VERSION', '1.0.0');

// Security
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('ENCRYPTION_KEY', 'your_secure_encryption_key_here');

// Logging
define('LOG_ENABLED', true);
define('LOG_PATH', APP_ROOT . '/logs');
define('LOG_LEVEL', 'DEBUG'); // DEBUG, INFO, WARNING, ERROR, CRITICAL

// File Upload
define('UPLOAD_PATH', APP_ROOT . '/uploads');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv');

// Images
define('IMAGES_PATH', APP_ROOT . '/assets/images');
define('DEFAULT_PROFILE_IMAGE', 'default.jpg');

// API Keys (for external services)
define('GOOGLE_MAPS_API_KEY', 'your_google_maps_api_key');
define('WEATHER_API_KEY', 'your_weather_api_key');

// Create required directories if they don't exist
$requiredDirs = [
    LOG_PATH,
    UPLOAD_PATH,
    IMAGES_PATH
];

foreach ($requiredDirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}
?>