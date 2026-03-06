<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
define('PAGE_TITLE', 'Shopping Bag');
require_once 'includes/header.php';

$cart_items = [];
$total_price = 0;

if (is_customer_logged_in()) {
    $customer_id = $_SESSION['customer_id'];
    $sql = "SELECT c.quantity, p.product_id, p.product_name, p.price, p.image, p.stock_quantity 
            FROM cart c 
            JOIN products p ON c.product_id = p.product_id 
            WHERE c.customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
} else {
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
        if ($ids) {
            $sql = "SELECT product_id, product_name, price, image, stock_quantity FROM products WHERE product_id IN ($ids)";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $row['quantity'] = $_SESSION['cart'][$row['product_id']];
                $cart_items[] = $row;
            }
        }
    }
}
?>

<style>
@media (max-width: 768px) {
    div[style*="padding: 80px 0"] {
        padding: 50px 0 !important;
    }
    h1[style*="font-size: 3rem"] {
        font-size: 2rem !important;
        margin-bottom: 30px !important;
    }
}

@media (max-width: 576px) {
    div[style*="padding: 80px 0"] {
        padding: 40px 0 !important;
    }
    h1[style*="font-size: 3rem"] {
        font-size: 1.75rem !important;
        margin-bottom: 25px !important;
    }
    div[style*="padding: 60px"] {
        padding: 40px 20px !important;
    }
}

@media (max-width: 991px) {
    .col-lg-8 { margin-bottom: 30px; }
}

@media (max-width: 768px) {
    div[style*="padding: 40px"] { padding: 25px !important; }
}

@media (max-width: 576px) {
    div[style*="padding: 40px"] { padding: 20px !important; }
}

.cart-table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 768px) {
    .cart-table-wrapper table {
        display: block;
        min-width: 0 !important;
    }
    .cart-table-wrapper thead { display: none; }
    .cart-table-wrapper tbody { display: block; }
    .cart-table-wrapper tr {
        display: block;
        margin-bottom: 20px;
        padding: 20px;
        background: white;
        border: 1px solid rgba(154, 123, 100, 0.1) !important;
        border-radius: 8px;
    }
    .cart-table-wrapper td {
        display: block;
        padding: 10px 0 !important;
        text-align: left !important;
        border: none !important;
    }
    .cart-table-wrapper td[style*="display: flex"] {
        flex-direction: row !important;
        gap: 15px !important;
    }
    .cart-table-wrapper td::before {
        content: attr(data-label);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        color: var(--brand-latte);
        display: block;
        margin-bottom: 5px;
        letter-spacing: 1px;
    }
    .cart-table-wrapper td:first-child::before { display: none; }
}

@media (max-width: 576px) {
    .cart-table-wrapper tr {
        padding: 15px;
        margin-bottom: 15px;
    }
    .cart-table-wrapper td[style*="display: flex"] { gap: 12px !important; }
    .cart-table-wrapper td[style*="display: flex"] > div:first-child {
        width: 70px !important;
        height: 90px !important;
    }
    .cart-table-wrapper td[style*="display: flex"] h4 { font-size: 0.95rem !important; }
    td form { width: 100% !important; }
    td form input[type="number"] {
        width: 100% !important;
        padding: 12px !important;
        font-size: 1rem !important;
    }
    div[style*="margin-top: 30px"] a { font-size: 0.75rem !important; }
}

.order-summary-sticky {
    position: sticky;
    top: 120px;
}

@media (max-width: 991px) {
    .order-summary-sticky { position: relative !important; top: 0 !important; }
}

@media (max-width: 768px) {
    .order-summary-sticky { padding: 30px !important; }
    .order-summary-sticky h3 { font-size: 1.3rem !important; }
}

@media (max-width: 576px) {
    .order-summary-sticky { padding: 25px !important; }
    .order-summary-sticky h3 { font-size: 1.2rem !important; }
}

/* ✅ FLASH MESSAGE STYLES */
.flash-success {
    background: #d4edda;
    color: #155724;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #c3e6cb;
    display: flex;
    align-items: center;
    gap: 10px;
}

.flash-error {
    background: #f8d7da;
    color: #721c24;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
    display: flex;
    align-items: center;
    gap: 10px;
}
</style>

<div style="background: var(--bg-sand); padding: 80px 0;">
    <div class="container">

        <!-- ✅ FIXED FLASH MESSAGE - set_flash_message() function k sath match karta hai -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="<?php echo $_SESSION['flash_message']['type'] == 'success' ? 'flash-success' : 'flash-error'; ?>">
                <i class="fas <?php echo $_SESSION['flash_message']['type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($_SESSION['flash_message']['text']); unset($_SESSION['flash_message']); ?>
            </div>
        <?php endif; ?>

        <h1 style="font-size: 3rem; text-align: center; margin-bottom: 50px; color: var(--brand-espresso);">
            Your Shopping Bag
        </h1>

        <?php if (empty($cart_items)): ?>
            <div style="text-align: center; padding: 60px; background: var(--bg-nude); border: 1px solid rgba(154, 123, 100, 0.1);">
                <i class="fas fa-shopping-bag" style="font-size: 3rem; color: var(--brand-latte); opacity: 0.3; margin-bottom: 20px; display: block;"></i>
                <p style="font-size: 1.1rem; color: var(--text-muted); margin-bottom: 30px;">Your bag is currently empty.</p>
                <a href="products.php" class="btn btn-gold">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="row g-5">

                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div style="background: var(--bg-nude); padding: 40px; border: 1px solid rgba(154, 123, 100, 0.1);">
                        <div class="cart-table-wrapper">
                            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                                <thead>
                                    <tr style="border-bottom: 2px solid var(--brand-latte); text-align: left;">
                                        <th style="padding-bottom: 20px; font-family: var(--font-heading); text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Product</th>
                                        <th style="padding-bottom: 20px; font-family: var(--font-heading); text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Price</th>
                                        <th style="padding-bottom: 20px; font-family: var(--font-heading); text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Quantity</th>
                                        <th style="padding-bottom: 20px; font-family: var(--font-heading); text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item):
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total_price += $subtotal;
                                    ?>
                                        <tr style="border-bottom: 1px solid rgba(154, 123, 100, 0.05);">
                                            <td style="padding: 30px 0; display: flex; gap: 20px; align-items: center;">
                                                <div style="width: 80px; height: 100px; overflow: hidden; border: 1px solid rgba(154, 123, 100, 0.1); flex-shrink: 0;">
                                                    <img src="<?php echo BASE_URL . $item['image']; ?>"
                                                        style="width: 100%; height: 100%; object-fit: cover;"
                                                        onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                                                </div>
                                                <div>
                                                    <h4 style="margin: 0 0 5px; font-size: 1rem; color: var(--brand-espresso);">
                                                        <?php echo htmlspecialchars($item['product_name']); ?>
                                                    </h4>
                                                    <a href="cart_action.php?action=remove&id=<?php echo $item['product_id']; ?>"
                                                        style="font-size: 0.75rem; color: #e74c3c; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;"
                                                        onclick="return confirm('Remove this item?')">
                                                        Remove
                                                    </a>
                                                </div>
                                            </td>
                                            <td data-label="Price" style="font-weight: 600; font-size: 0.95rem; color: var(--text-main);">
                                                <?php echo formatPricePKR($item['price']); ?>
                                            </td>
                                            <td data-label="Quantity">
                                                <form action="cart_action.php" method="POST"
                                                    style="display:flex; align-items: center; border: 1px solid var(--brand-latte); width: fit-content;">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                    <input type="number" name="quantity"
                                                        value="<?php echo $item['quantity']; ?>"
                                                        min="1"
                                                        max="<?php echo $item['stock_quantity']; ?>"
                                                        onchange="this.form.submit()"
                                                        style="width: 50px; border: none; text-align: center; padding: 10px; font-family: var(--font-body); font-weight: 700;">
                                                </form>
                                            </td>
                                            <td data-label="Total" style="text-align: right; font-weight: 700; color: var(--brand-espresso); font-size: 1rem;">
                                                <?php echo formatPricePKR($subtotal); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div style="margin-top: 30px;">
                        <a href="products.php"
                            style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; color: var(--brand-espresso); border-bottom: 1px solid var(--brand-latte); padding-bottom: 5px;">
                            <i class="fas fa-arrow-left" style="margin-right: 10px;"></i> Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="order-summary-sticky"
                        style="background: white; padding: 40px; border: 1px solid rgba(154, 123, 100, 0.1);">
                        <h3 style="font-family: var(--font-heading); font-size: 1.5rem; margin-bottom: 30px; border-bottom: 2px solid var(--brand-latte); padding-bottom: 15px; color: var(--brand-espresso);">
                            Order Summary
                        </h3>

                        <div style="margin-bottom: 30px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 0.95rem; color: var(--text-muted);">
                                <span>Subtotal</span>
                                <span><?php echo formatPricePKR($total_price); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 0.9rem; color: var(--text-muted);">
                                <span>Shipping</span>
                                <span>Calculated at checkout</span>
                            </div>
                        </div>

                        <div style="border-top: 1px solid rgba(154, 123, 100, 0.1); padding-top: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; color: var(--brand-espresso); font-size: 1.1rem;">Total</span>
                            <span style="font-weight: 700; color: var(--brand-espresso); font-size: 1.5rem;">
                                <?php echo formatPricePKR($total_price); ?>
                            </span>
                        </div>

                        <?php if (is_customer_logged_in()): ?>
                            <a href="checkout.php" class="btn btn-gold"
                                style="width: 100%; text-align: center; padding: 15px 0; font-size: 1rem; display: block;">
                                Proceed to Checkout
                            </a>
                        <?php else: ?>
                            <a href="customer/login.php" class="btn btn-gold"
                                style="width: 100%; text-align: center; padding: 15px 0; font-size: 1rem; display: block;">
                                Login to Checkout
                            </a>
                        <?php endif; ?>

                        <!-- Available Coupons -->
                        <?php
                        $today = date('Y-m-d');
                        $coupon_sql = "SELECT coupon_code, discount_type, discount_value FROM coupons WHERE status = 'active' AND expiry_date >= ? ORDER BY created_at DESC LIMIT 3";
                        $coupon_stmt = $conn->prepare($coupon_sql);
                        $coupon_stmt->bind_param("s", $today);
                        $coupon_stmt->execute();
                        $coupons_res = $coupon_stmt->get_result();
                        if ($coupons_res->num_rows > 0):
                        ?>
                            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px dashed rgba(154, 123, 100, 0.2);">
                                <h4 style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--brand-espresso); margin-bottom: 15px;">
                                    Available Coupons
                                </h4>
                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    <?php while ($coupon = $coupons_res->fetch_assoc()): ?>
                                        <div style="background: rgba(201, 162, 39, 0.05); border: 1px dashed var(--accent-gold); padding: 10px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-weight: 700; color: var(--brand-espresso); font-size: 0.85rem;">
                                                <?php echo htmlspecialchars($coupon['coupon_code']); ?>
                                            </span>
                                            <span style="font-size: 0.75rem; color: var(--brand-latte); font-weight: 600;">
                                                <?php echo $coupon['discount_type'] == 'percentage' ? $coupon['discount_value'] . '%' : formatPricePKR($coupon['discount_value']); ?> OFF
                                            </span>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>