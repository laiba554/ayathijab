<?php
session_start();
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'helpers/functions.php';

// Enforce Login
if (!is_customer_logged_in()) {
    redirect('customer/login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_SESSION['customer_id'];
    $address = sanitize_input($_POST['address']);
    $remarks = sanitize_input($_POST['remarks']);
    $payment_method = sanitize_input($_POST['payment_method']);

    if (empty($address)) {
        set_flash_message('error', 'Address is required to place an order.');
        redirect('checkout.php');
    }

    // 1. Calculate Total & Verify Stock
    $total_amount = 0;
    $cart_items = [];

    $cart_sql = "SELECT c.quantity, p.product_id, p.price, p.stock_quantity 
                 FROM cart c JOIN products p ON c.product_id = p.product_id 
                 WHERE c.customer_id = ?";
    $stmt = $conn->prepare($cart_sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 0) {
        set_flash_message('error', 'Your cart is empty.');
        redirect('products.php');
    }

    while ($row = $res->fetch_assoc()) {
        if ($row['quantity'] > $row['stock_quantity']) {
            set_flash_message('error', 'One or more items are out of stock. Please update your cart.');
            redirect('cart.php');
        }
        $total_amount += ($row['price'] * $row['quantity']);
        $cart_items[] = $row;
    }

    // 1.5 Calculate Discount
    $discount_amount = 0;
    $coupon_id = null;

    if (isset($_SESSION['coupon'])) {
        $coupon = $_SESSION['coupon'];

        // Final Validation
        $valid_coupon = true;
        // Check Status
        $stmt = $conn->prepare("SELECT * FROM coupons WHERE coupon_id = ? AND status = 'active'");
        $stmt->bind_param("i", $coupon['coupon_id']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows == 0)
            $valid_coupon = false;

        // Check Expiry
        if (strtotime($coupon['expiry_date']) < time())
            $valid_coupon = false;

        // Check Min Order
        if ($total_amount < $coupon['min_order_amount'])
            $valid_coupon = false;

        // Check Limit
        $usage_check = $conn->query("SELECT COUNT(*) as count FROM coupon_usage WHERE coupon_id = " . $coupon['coupon_id']);
        if ($coupon['usage_limit'] > 0 && $usage_check->fetch_assoc()['count'] >= $coupon['usage_limit'])
            $valid_coupon = false;

        if ($valid_coupon) {
            $coupon_id = $coupon['coupon_id'];
            if ($coupon['discount_type'] == 'percentage') {
                $discount_amount = ($total_amount * $coupon['discount_value']) / 100;
            } else {
                $discount_amount = $coupon['discount_value'];
            }
            if ($discount_amount > $total_amount)
                $discount_amount = $total_amount;
        } else {
            // Coupon became invalid during checkout
            unset($_SESSION['coupon']);
        }
    }

    // 1.6 Calculate Delivery Charges
    $delivery_result = $conn->query("SELECT * FROM delivery_settings WHERE setting_id = 1");
    $delivery_settings = $delivery_result->fetch_assoc();

    $delivery_charge = $delivery_settings['delivery_charge'];
    if ($delivery_settings['free_delivery_enabled'] && $total_amount >= $delivery_settings['free_delivery_threshold']) {
        $delivery_charge = 0;
    }

    // 2. Create Order
    $final_amount = $total_amount + $delivery_charge - $discount_amount;

    $conn->begin_transaction();
    try {
        $order_sql = "INSERT INTO orders (customer_id, total_amount, delivery_charge, discount_amount, coupon_id, remarks, order_status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($order_sql);
        $stmt->bind_param("idddis", $customer_id, $final_amount, $delivery_charge, $discount_amount, $coupon_id, $remarks);
        $stmt->execute();
        $order_id = $conn->insert_id;

        // 2.5 Record Coupon Usage
        if ($coupon_id) {
            $use_sql = "INSERT INTO coupon_usage (coupon_id, customer_id, order_id) VALUES (?, ?, ?)";
            $stmt_use = $conn->prepare($use_sql);
            $stmt_use->bind_param("iii", $coupon_id, $customer_id, $order_id);
            $stmt_use->execute();
            unset($_SESSION['coupon']); // Clear after use
        }

        // 3. Create Order Items & Update Stock
        $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stock_sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";

        $stmt_item = $conn->prepare($item_sql);
        $stmt_stock = $conn->prepare($stock_sql);

        foreach ($cart_items as $item) {
            $stmt_item->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt_item->execute();

            $stmt_stock->bind_param("ii", $item['quantity'], $item['product_id']);
            $stmt_stock->execute();
        }

        // 4. Record Payment (Pending)
        $pay_sql = "INSERT INTO payments (order_id, payment_method, payment_status) VALUES (?, ?, 'pending')";
        $stmt_pay = $conn->prepare($pay_sql);
        $stmt_pay->bind_param("is", $order_id, $payment_method);
        $stmt_pay->execute();

        // 5. Clear Cart
        $clear_sql = "DELETE FROM cart WHERE customer_id = ?";
        $stmt_clear = $conn->prepare($clear_sql);
        $stmt_clear->bind_param("i", $customer_id);
        $stmt_clear->execute();

        $conn->commit();

        // Success
        // Redirect to a simple success page or dashboard
        $_SESSION['order_success_id'] = $order_id;
        redirect('order_success.php');

    } catch (Exception $e) {
        $conn->rollback();
        set_flash_message('error', 'Failed to place order: ' . $e->getMessage());
        redirect('checkout.php');
    }

} else {
    redirect('index.php');
}
?>