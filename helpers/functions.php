<?php

require_once __DIR__ . '/../config/config.php';

/**
 * Sanitize input data
 * 
 * @param string $data
 * @return string
 */
function sanitize_input($data)
{
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

/**
 * Redirect to a specific URL
 * 
 * @param string $url
 */
function redirect($url)
{
    header("Location: " . BASE_URL . $url);
    exit();
}

/**
 * Set a flash message in session
 * 
 * @param string $type (success|error)
 * @param string $message
 */
function set_flash_message($type, $message)
{
    $_SESSION['flash_message'] = [
        'type' => $type,
        'text' => $message
    ];
}

/**
 * Display flash message if it exists
 */
function display_flash_message()
{
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        $class = ($msg['type'] == 'success') ? 'success-msg' : 'error-msg';
        echo '<div class="alert ' . $class . '">' . $msg['text'] . '</div>';
        unset($_SESSION['flash_message']);
    }
}

/**
 * Check if admin is logged in
 * 
 * @return boolean
 */
function is_admin_logged_in()
{
    return isset($_SESSION['admin_id']) && isset($_SESSION['role']) &&
        ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'super_admin');
}

/**
 * Check if customer is logged in
 * 
 * @return boolean
 */
function is_customer_logged_in()
{
    return isset($_SESSION['customer_id']);
}

/**
 * Enforce admin login
 */
function require_admin_login()
{
    if (!is_admin_logged_in()) {
        set_flash_message('error', 'Please login to access the admin panel.');
        redirect('admin/login.php');
    }
}

/**
 * Enforce customer login
 */
function require_customer_login()
{
    if (!is_customer_logged_in()) {
        set_flash_message('error', 'Please login to continue.');
        redirect('customer/login.php');
    }
}

/**
 * Format price to PKR currency
 * 
 * @param float $amount
 * @return string (e.g. Rs. 1,250.00)
 */
function formatPricePKR($amount)
{
    return 'Rs. ' . number_format((float) $amount, 2);
}
?>