<?php
session_start();
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'helpers/functions.php';

// Enforce Login for Wishlist actions
if (!is_customer_logged_in()) {
    if (isset($_SERVER['HTTP_REFERER'])) {
        $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'];
    }
    else {
        $_SESSION['redirect_after_login'] = 'wishlist.php';
    }
    redirect('customer/login.php');
}

$customer_id = $_SESSION['customer_id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($action == 'add' && $product_id > 0) {
    // Check if exists
    $check = $conn->prepare("SELECT wishlist_id FROM wishlist WHERE customer_id = ? AND product_id = ?");
    $check->bind_param("ii", $customer_id, $product_id);
    $check->execute();
    if ($check->get_result()->num_rows == 0) {
        $add = $conn->prepare("INSERT INTO wishlist (customer_id, product_id) VALUES (?, ?)");
        $add->bind_param("ii", $customer_id, $product_id);
        if ($add->execute()) {
            set_flash_message('success', 'Product added to wishlist.');
        }
        else {
            set_flash_message('error', 'Error adding to wishlist.');
        }
    }
    else {
        set_flash_message('warning', 'Product already in wishlist.');
    }

    // Redirect back
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
    else {
        redirect('wishlist.php');
    }

}
elseif ($action == 'remove' && $product_id > 0) {
    $del = $conn->prepare("DELETE FROM wishlist WHERE customer_id = ? AND product_id = ?");
    $del->bind_param("ii", $customer_id, $product_id);
    $del->execute();
    set_flash_message('success', 'Product removed from wishlist.');
    redirect('wishlist.php');

}
elseif ($action == 'move_to_cart' && $product_id > 0) {
    // 1. Add to Cart (Logic similar to cart_action.php but simpler)
    // Check Stock first
    $stock_q = $conn->query("SELECT stock_quantity FROM products WHERE product_id = $product_id");
    $stock_row = $stock_q->fetch_assoc();

    if ($stock_row && $stock_row['stock_quantity'] > 0) {
        // Check if in cart
        $check_cart = $conn->prepare("SELECT quantity FROM cart WHERE customer_id = ? AND product_id = ?");
        $check_cart->bind_param("ii", $customer_id, $product_id);
        $check_cart->execute();
        $cart_res = $check_cart->get_result();

        if ($row = $cart_res->fetch_assoc()) {
            // Update qty
            $new_qty = $row['quantity'] + 1;
            $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE customer_id = ? AND product_id = ?");
            $update->bind_param("iii", $new_qty, $customer_id, $product_id);
            $update->execute();
        }
        else {
            // Insert
            $insert = $conn->prepare("INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, 1)");
            $insert->bind_param("ii", $customer_id, $product_id);
            $insert->execute();
        }

        // 2. Remove from Wishlist
        $del = $conn->prepare("DELETE FROM wishlist WHERE customer_id = ? AND product_id = ?");
        $del->bind_param("ii", $customer_id, $product_id);
        $del->execute();

        set_flash_message('success', 'Moved to cart.');
    }
    else {
        set_flash_message('error', 'Product out of stock.');
    }

    redirect('wishlist.php');

}
else {
    redirect('index.php');
}
?>
