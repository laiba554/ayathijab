<?php
session_start();
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../helpers/functions.php';

// Check Admin Login
require_admin_login();

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? sanitize_input($_GET['id']) : 0;

if ($id > 0) {
    if ($action == 'delete') {
        $stmt = $conn->prepare("DELETE FROM coupons WHERE coupon_id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            set_flash_message('success', 'Coupon deleted.');
        } else {
            set_flash_message('error', 'Error deleting coupon.');
        }
    } elseif ($action == 'toggle') {
        // Get current status
        $q = $conn->prepare("SELECT status FROM coupons WHERE coupon_id = ?");
        $q->bind_param("i", $id);
        $q->execute();
        $res = $q->get_result();
        if ($row = $res->fetch_assoc()) {
            $new_status = ($row['status'] == 'active') ? 'inactive' : 'active';
            $update = $conn->prepare("UPDATE coupons SET status = ? WHERE coupon_id = ?");
            $update->bind_param("si", $new_status, $id);
            if ($update->execute()) {
                set_flash_message('success', "Coupon marked as $new_status.");
            } else {
                set_flash_message('error', 'Database error on status update.');
            }
        }
    }
}

redirect('admin/coupons.php');
?>