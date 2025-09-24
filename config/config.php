<?php
// Database configuration - MySQL InfinityFree
define('DB_HOST', 'sql212.infinityfree.com');
define('DB_PORT', '3306');
define('DB_NAME', 'epiz_32157541_marklokpos');
define('DB_USER', 'epiz_32157541');
define('DB_PASS', 'BcTj52WsZE1yIT');
define('DB_TYPE', 'mysql');

// Application configuration
define('APP_NAME', 'SPBU Management System');
define('APP_VERSION', '1.0.0');
define('DEBUG_MODE', true);

// Security configuration
define('SESSION_LIFETIME', 3600 * 24); // 24 hours
define('PASSWORD_MIN_LENGTH', 6);
define('BCRYPT_COST', 12);

// Default timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session configuration
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_set_cookie_params(SESSION_LIFETIME);
?>