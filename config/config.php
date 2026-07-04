<?php
/**
 * Configuration File
 * Sistem Monitoring dan Pengelolaan Inventaris Barang MENWA
 */

// Database Configuration
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'db_menwa');
define('DB_USER', 'root');
define('DB_PASS', '');

// App Configuration
define('APP_NAME', 'Sistem Inventaris MENWA UPN "Veteran" Yogyakarta');
define('BASE_URL', '/'); // Set dynamically or leave as relative/root

// Security Configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes in seconds

// Timezone Setup
date_default_timezone_set('Asia/Jakarta');

// Error reporting config (for local dev, disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log path
define('ERROR_LOG_PATH', dirname(__DIR__) . '/logs/error.log');
define('CRON_LOG_PATH', dirname(__DIR__) . '/logs/cron.log');
ini_set('error_log', ERROR_LOG_PATH);
