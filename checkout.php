<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
define('PAGE_TITLE', 'Checkout');
require_once 'includes/header.php';
require_customer_login();

$customer_id = $_SESSION['customer_id'];
$cart_items = [];
$total_price = 0;

$sql = "SELECT c.quantity, p.product_id, p.product_name, p.price, p.image 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

$delivery_charge = 200;
$delivery_result = $conn->query("SELECT * FROM delivery_settings WHERE setting_id = 1");
if ($delivery_result && $delivery_result->num_rows > 0) {
    $delivery_settings = $delivery_result->fetch_assoc();
    $delivery_charge = $delivery_settings['delivery_charge'];
    if ($delivery_settings['free_delivery_enabled'] && $total_price >= $delivery_settings['free_delivery_threshold']) {
        $delivery_charge = 0;
    }
}

$grand_total = $total_price + $delivery_charge;

$cust_stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$cust_stmt->bind_param("i", $customer_id);
$cust_stmt->execute();
$customer = $cust_stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'cod';

    if (empty($full_name)) {
        set_flash_message('error', 'Please enter your full name.');
    } elseif (strlen($full_name) < 3) {
        set_flash_message('error', 'Name must be at least 3 characters.');
    } elseif (strlen($full_name) > 100) {
        set_flash_message('error', 'Name is too long.');
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
        set_flash_message('error', 'Name should only contain letters.');
    } elseif (empty($phone)) {
        set_flash_message('error', 'Please enter your phone number.');
    } elseif (!preg_match("/^[0-9]{10,11}$/", $phone)) {
        set_flash_message('error', 'Please enter a valid 10-11 digit phone number.');
    } elseif (empty($address)) {
        set_flash_message('error', 'Please enter your delivery address.');
    } elseif (strlen($address) < 10) {
        set_flash_message('error', 'Please enter a complete address (minimum 10 characters).');
    } elseif (strlen($address) > 500) {
        set_flash_message('error', 'Address is too long.');
    } elseif (empty($city)) {
        set_flash_message('error', 'Please enter your city.');
    } elseif (strlen($city) < 2) {
        set_flash_message('error', 'Please enter a valid city name.');
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $city)) {
        set_flash_message('error', 'City name should only contain letters.');
    } elseif (strlen($notes) > 500) {
        set_flash_message('error', 'Order notes are too long (max 500 characters).');
    } else {
        $full_name = sanitize_input($full_name);
        $phone = sanitize_input($phone);
        $address = sanitize_input($address);
        $city = sanitize_input($city);
        $notes = sanitize_input($notes);
        
        $desc = $conn->query("DESCRIBE orders");
        $columns = [];
        while ($col = $desc->fetch_assoc()) {
            $columns[] = $col['Field'];
        }
        
        $has_status = in_array('status', $columns);
        $has_grand_total = in_array('grand_total', $columns);
        $has_shipping_name = in_array('shipping_name', $columns);
        
        $status_col = $has_status ? 'status' : 'order_status';
        
        if ($has_grand_total && $has_shipping_name) {
            $order_sql = "INSERT INTO orders (customer_id, total_amount, delivery_charge, grand_total, shipping_name, shipping_phone, shipping_address, shipping_city, notes, payment_method, $status_col) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $order_stmt = $conn->prepare($order_sql);
            $order_stmt->bind_param("idddssssss", $customer_id, $total_price, $delivery_charge, $grand_total, $full_name, $phone, $address, $city, $notes, $payment_method);
        } else {
            $order_sql = "INSERT INTO orders (customer_id, total_amount, delivery_charge, $status_col) VALUES (?, ?, ?, 'Pending')";
            $order_stmt = $conn->prepare($order_sql);
            $order_stmt->bind_param("idd", $customer_id, $total_price, $delivery_charge);
        }

        if ($order_stmt->execute()) {
            $order_id = $conn->insert_id;

            foreach ($cart_items as $item) {
                $item_desc = $conn->query("DESCRIBE order_items");
                $item_columns = [];
                while ($ic = $item_desc->fetch_assoc()) {
                    $item_columns[] = $ic['Field'];
                }
                
                $has_price = in_array('price', $item_columns);
                $price_col = $has_price ? 'price' : 'unit_price';
                
                $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, $price_col) VALUES (?, ?, ?, ?)");
                $item_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                $item_stmt->execute();

                $conn->query("UPDATE products SET stock_quantity = stock_quantity - " . intval($item['quantity']) . " WHERE product_id = " . intval($item['product_id']));
            }

            $conn->query("DELETE FROM cart WHERE customer_id = " . intval($customer_id));

            set_flash_message('success', 'Order placed successfully!');
            header("Location: order_success.php?order_id=" . $order_id);
            exit;
        } else {
            set_flash_message('error', 'Failed to place order. Please try again.');
        }
    }
}
?>

<style>
@media (max-width: 991px) {
    .order-summary-sticky {
        position: relative !important;
        top: 0 !important;
        margin-top: 30px;
    }
}

@media (max-width: 768px) {
    div[style*="padding: 80px 0"] {
        padding: 50px 0 !important;
    }
    h1[style*="font-size: 3rem"] {
        font-size: 2rem !important;
    }
    .checkout-form-card {
        padding: 25px !important;
    }
}

@media (max-width: 576px) {
    div[style*="padding: 80px 0"] {
        padding: 35px 0 !important;
    }
    h1[style*="font-size: 3rem"] {
        font-size: 1.75rem !important;
    }
    .checkout-form-card {
        padding: 20px !important;
    }
}

.form-control-custom {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid rgba(154, 123, 100, 0.3);
    border-radius: 4px;
    font-family: var(--font-body);
    font-size: 0.95rem;
    color: var(--brand-espresso);
    background: white;
    transition: border-color 0.2s;
    box-sizing: border-box;
}

.form-control-custom:focus {
    outline: none;
    border-color: var(--brand-latte);
    box-shadow: 0 0 0 3px rgba(154, 123, 100, 0.1);
}

.form-label-custom {
    display: block;
    margin-bottom: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--brand-espresso);
}

.payment-option {
    border: 1px solid rgba(154, 123, 100, 0.2);
    border-radius: 8px;
    padding: 15px 20px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
}

.payment-option:hover {
    border-color: var(--brand-latte);
    background: rgba(154, 123, 100, 0.05);
}

.payment-option.selected {
    border-color: var(--brand-latte);
    background: rgba(154, 123, 100, 0.05);
}

.order-summary-sticky {
    position: sticky;
    top: 120px;
}

.flash-success {
    background: #d4edda;
    color: #155724;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #c3e6cb;
}

.flash-error {
    background: #f8d7da;
    color: #721c24;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
}
</style>

<div style="background: var(--bg-sand); padding: 80px 0;">
    <div class="container">

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="<?php echo $_SESSION['flash_message']['type'] == 'success' ? 'flash-success' : 'flash-error'; ?>">
                <i class="fas <?php echo $_SESSION['flash_message']['type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($_SESSION['flash_message']['text']); unset($_SESSION['flash_message']); ?>
            </div>
        <?php endif; ?>

        <h1 style="font-size: 3rem; text-align: center; margin-bottom: 50px; color: var(--brand-espresso);">
            Checkout
        </h1>

        <form method="POST" action="checkout.php">
            <div class="row g-5">

                <div class="col-lg-7">
                    <div class="checkout-form-card" style="background: white; padding: 40px; border: 1px solid rgba(154, 123, 100, 0.1); border-radius: 8px; margin-bottom: 30px;">
                        <h3 style="font-size: 1.2rem; margin-bottom: 25px; color: var(--brand-espresso); border-bottom: 2px solid var(--brand-latte); padding-bottom: 10px;">
                            <i class="fas fa-map-marker-alt" style="color: var(--brand-latte); margin-right: 8px;"></i>
                            Shipping Information
                        </h3>

                        <div style="margin-bottom: 20px;">
                            <label class="form-label-custom">Full Name <span style="color: red;">*</span></label>
                            <input type="text" name="full_name" class="form-control-custom" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : htmlspecialchars($customer['name'] ?? ''); ?>" placeholder="Enter your full name" required minlength="5" maxlength="100" pattern="[a-zA-Z\s]+" title="Only letters and spaces">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label class="form-label-custom">Phone Number <span style="color: red;">*</span></label>
                            <input type="tel" name="phone" class="form-control-custom" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : htmlspecialchars($customer['phone'] ?? ''); ?>" placeholder="03001234567" required pattern="[0-9]{10,11}" title="10-11 digit phone number" maxlength="11">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label class="form-label-custom">Delivery Address <span style="color: red;">*</span></label>
                            <textarea name="address" class="form-control-custom" rows="3" placeholder="House/Flat No, Street, Area" required minlength="10" maxlength="500"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : htmlspecialchars($customer['address'] ?? ''); ?></textarea>
                            <small style="color: #999; font-size: 0.85rem;">Minimum 10 characters</small>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label class="form-label-custom">City <span style="color: red;">*</span></label>
                            <input type="text" name="city" class="form-control-custom" value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : htmlspecialchars($customer['city'] ?? ''); ?>" placeholder="Karachi, Lahore, Islamabad..." required minlength="2" pattern="[a-zA-Z\s]+" title="Only letters and spaces">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label class="form-label-custom">Order Notes (Optional)</label>
                            <textarea name="notes" class="form-control-custom" rows="2" placeholder="Any special instructions..." maxlength="500"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                        </div>
                    </div>

                    <div class="checkout-form-card" style="background: white; padding: 40px; border: 1px solid rgba(154, 123, 100, 0.1); border-radius: 8px;">
                        <h3 style="font-size: 1.2rem; margin-bottom: 25px; color: var(--brand-espresso); border-bottom: 2px solid var(--brand-latte); padding-bottom: 10px;">
                            <i class="fas fa-credit-card" style="color: var(--brand-latte); margin-right: 8px;"></i>
                            Payment Method
                        </h3>

                        <label class="payment-option selected">
                            <input type="radio" name="payment_method" value="cod" checked onchange="selectPayment(this)">
                            <i class="fas fa-money-bill-wave" style="color: #2ecc71; font-size: 1.3rem;"></i>
                            <div>
                                <strong>Cash on Delivery</strong>
                                <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Pay when you receive your order</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="order-summary-sticky" style="background: white; padding: 40px; border: 1px solid rgba(154, 123, 100, 0.1); border-radius: 8px;">
                        <h3 style="font-size: 1.2rem; margin-bottom: 25px; color: var(--brand-espresso); border-bottom: 2px solid var(--brand-latte); padding-bottom: 10px;">
                            <i class="fas fa-shopping-bag" style="color: var(--brand-latte); margin-right: 8px;"></i>
                            Order Summary
                        </h3>

                        <?php foreach ($cart_items as $item): ?>
                            <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid rgba(154, 123, 100, 0.1);">
                                <div style="width: 60px; height: 75px; overflow: hidden; border: 1px solid rgba(154, 123, 100, 0.1); flex-shrink: 0; border-radius: 4px;">
                                    <img src="<?php echo BASE_URL . $item['image']; ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                                </div>
                                <div style="flex: 1;">
                                    <p style="margin: 0 0 5px; font-size: 0.9rem; font-weight: 600; color: var(--brand-espresso);">
                                        <?php echo htmlspecialchars($item['product_name']); ?>
                                    </p>
                                    <p style="margin: 0; font-size: 0.8rem; color: var(--text-muted);">
                                        Qty: <?php echo $item['quantity']; ?>
                                    </p>
                                </div>
                                <div style="font-weight: 700; color: var(--brand-espresso); font-size: 0.95rem;">
                                    <?php echo formatPricePKR($item['price'] * $item['quantity']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div style="margin-top: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.9rem; color: var(--text-muted);">
                                <span>Subtotal</span>
                                <span><?php echo formatPricePKR($total_price); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.9rem; color: var(--text-muted);">
                                <span>Delivery Charge</span>
                                <span>
                                    <?php if ($delivery_charge == 0): ?>
                                        <span style="color: #2ecc71; font-weight: 600;">FREE</span>
                                    <?php else: ?>
                                        <?php echo formatPricePKR($delivery_charge); ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>

                        <div style="border-top: 2px solid var(--brand-latte); padding-top: 15px; margin-top: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                            <span style="font-weight: 700; font-size: 1.1rem; color: var(--brand-espresso);">Grand Total</span>
                            <span style="font-weight: 700; font-size: 1.5rem; color: var(--brand-espresso);">
                                <?php echo formatPricePKR($grand_total); ?>
                            </span>
                        </div>

                        <button type="submit" class="btn btn-gold" style="width: 100%; padding: 15px; font-size: 1rem; border: none; cursor: pointer;">
                            <i class="fas fa-check-circle"></i> Place Order
                        </button>

                        <a href="cart.php" style="display: block; text-align: center; margin-top: 15px; font-size: 0.85rem; color: var(--text-muted); text-decoration: none;">
                            <i class="fas fa-arrow-left"></i> Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function selectPayment(radio) {
        document.querySelectorAll('.payment-option').forEach(el => {
            el.classList.remove('selected');
        });
        radio.closest('.payment-option').classList.add('selected');
    }
</script>

<?php
require_once 'includes/footer.php';
ob_end_flush();
?>