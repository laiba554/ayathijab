<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../helpers/functions.php';

// Enforce Admin Login
require_admin_login();

if (isset($_GET['id'])) {
    $product_id = sanitize_input($_GET['id']);

    // Prepared statement for security
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        set_flash_message('success', 'Product deleted successfully.');
    }
    else {
        set_flash_message('error', 'Error deleting product: ' . $stmt->error);
    }
    $stmt->close();
}
else {
    set_flash_message('error', 'Invalid request.');
}

redirect('admin/products.php');
?>
