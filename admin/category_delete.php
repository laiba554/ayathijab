<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../helpers/functions.php';

// Enforce Admin Login
require_admin_login();

if (isset($_GET['id'])) {
    $category_id = sanitize_input($_GET['id']);

    // Prepared statement for security
    $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);

    if ($stmt->execute()) {
        set_flash_message('success', 'Category deleted successfully.');
    }
    else {
        set_flash_message('error', 'Error deleting category: ' . $stmt->error);
    }
    $stmt->close();
}
else {
    set_flash_message('error', 'Invalid request.');
}

redirect('admin/categories.php');
?>
