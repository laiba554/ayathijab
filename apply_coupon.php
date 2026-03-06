<?php
session_start();
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['coupon_code'])) {
    $code = strtoupper(sanitize_input($_POST['coupon_code']));
    $customer_id = $_SESSION['customer_id'] ?? 0;

    if (empty($code)) {
        set_flash_message('error', 'Please enter a coupon code.');
        redirect('checkout.php');
    }

    // Fetch Coupon
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE coupon_code = ? AND status = 'active' AND expiry_date >= CURDATE()");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Calculate current cart total
        $total_price = 0;
        $cart_sql = "SELECT c.quantity, p.price FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.customer_id = ?";
        $cart_stmt = $conn->prepare($cart_sql);
        $cart_stmt->bind_param("i", $customer_id);
        $cart_stmt->execute();
        $cart_res = $cart_stmt->get_result();
        while ($cart_row = $cart_res->fetch_assoc()) {
            $total_price += $cart_row['price'] * $cart_row['quantity'];
        }

        // Check Min Order Amount
        if ($total_price < $row['min_order_amount']) {
            set_flash_message('error', 'Minimum order amount for this coupon is ' . formatPricePKR($row['min_order_amount']));
        } else {
            // Check Usage Limit
            if ($row['usage_limit'] > 0) {
                $usage_stmt = $conn->prepare("SELECT COUNT(*) as count FROM coupon_usage WHERE coupon_id = ?");
                $usage_stmt->bind_param("i", $row['coupon_id']);
                $usage_stmt->execute();
                $usage_count = $usage_stmt->get_result()->fetch_assoc()['count'];

                if ($usage_count >= $row['usage_limit']) {
                    set_flash_message('error', 'This coupon has reached its usage limit.');
                    redirect('checkout.php');
                }
            }

            // Valid Coupon!
            $_SESSION['coupon'] = [
                'coupon_id' => $row['coupon_id'],
                'code' => $row['coupon_code'],
                'discount_type' => $row['discount_type'],
                'discount_value' => $row['discount_value'],
                'min_order_amount' => $row['min_order_amount'],
                'expiry_date' => $row['expiry_date']
            ];
            set_flash_message('success', 'Coupon applied successfully!');
        }
    } else {
        set_flash_message('error', 'Invalid or expired coupon code.');
    }
}

redirect('checkout.php');
?>