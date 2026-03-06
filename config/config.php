<?php

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ayyatulhijab_ecommerce');

// App Configuration
define('APP_NAME', 'AyyatulHijab');
define('BASE_URL', 'http://localhost/ayathijab/');

// Path Constants
define('ROOT_PATH', dirname(__DIR__) . '/');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('ADMIN_PATH', ROOT_PATH . 'admin/');
define('CUSTOMER_PATH', ROOT_PATH . 'customer/');

// Error Reporting (Enable for development, disable for production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start Session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
