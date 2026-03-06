<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/config.php';
require_once 'config/db.php';
require_once 'helpers/functions.php';

$action = '';
if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
} elseif (isset($_GET['action']) && !empty($_GET['action'])) {
    $action = $_GET['action'];
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ADD TO CART
if ($action == 'add') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity   = isset($_POST['quantity'])   ? intval($_POST['quantity'])   : 1;
    if ($quantity < 1) $quantity = 1;

    if ($product_id > 0) {
        if (isset($_SESSION['customer_id']) && intval($_SESSION['customer_id']) > 0) {
            $customer_id = intval($_SESSION['customer_id']);

            $result = $conn->query("SELECT cart_id, quantity FROM cart WHERE customer_id = $customer_id AND product_id = $product_id");

            if ($result && $result->num_rows > 0) {
                $row     = $result->fetch_assoc();
                $new_qty = $row['quantity'] + $quantity;
                $conn->query("UPDATE cart SET quantity = $new_qty WHERE cart_id = " . $row['cart_id']);
            } else {
                $conn->query("INSERT INTO cart (customer_id, product_id, quantity) VALUES ($customer_id, $product_id, $quantity)");
            }
            set_flash_message('success', 'Product added to cart!');
        } else {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            set_flash_message('success', 'Product added to cart!');
        }
    } else {
        set_flash_message('error', 'Invalid product.');
    }

    // ✅ Add ke baad wapas usi page par - ya cart par
    $back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cart.php';
    header("Location: " . $back);
    exit;

// REMOVE FROM CART
} elseif ($action == 'remove') {
    $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($product_id > 0) {
        if (isset($_SESSION['customer_id']) && intval($_SESSION['customer_id']) > 0) {
            $customer_id = intval($_SESSION['customer_id']);
            $conn->query("DELETE FROM cart WHERE customer_id = $customer_id AND product_id = $product_id");
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        set_flash_message('success', 'Item removed from cart.');
    }

    header("Location: cart.php");
    exit;

// UPDATE CART
} elseif ($action == 'update') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity   = isset($_POST['quantity'])   ? intval($_POST['quantity'])   : 0;

    if ($product_id > 0) {
        if (isset($_SESSION['customer_id']) && intval($_SESSION['customer_id']) > 0) {
            $customer_id = intval($_SESSION['customer_id']);
            if ($quantity > 0) {
                $conn->query("UPDATE cart SET quantity = $quantity WHERE customer_id = $customer_id AND product_id = $product_id");
            } else {
                $conn->query("DELETE FROM cart WHERE customer_id = $customer_id AND product_id = $product_id");
            }
        } else {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
        set_flash_message('success', 'Cart updated.');
    }

    header("Location: cart.php");
    exit;

} else {
    header("Location: index.php");
    exit;
}
?>