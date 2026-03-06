<?php
define('PAGE_TITLE', 'Order Confirmed');
require_once 'includes/header.php';
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
?>

<div style="background: var(--bg-sand); padding: 120px 0; min-height: 85vh; display: flex; align-items: center;">
    <div class="container" style="max-width: 600px; text-align: center;">
        <div
            style="background: var(--bg-nude); padding: 80px 60px; border: 1px solid rgba(154, 123, 100, 0.1); box-shadow: 0 10px 40px rgba(0,0,0,0.05);">
            <div
                style="width: 100px; height: 100px; background: rgba(154, 123, 100, 0.1); color: var(--brand-latte); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 40px;">
                <i class="fas fa-check"></i>
            </div>

            <span
                style="color: var(--brand-latte); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 3px;">Excellent
                Choice</span>
            <h1 style="font-size: 2.8rem; margin: 15px 0 25px; color: var(--brand-espresso);">Thank You For <br>Your
                Order</h1>

            <p style="font-size: 1.1rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 40px;">
                Your order <strong style="color: var(--brand-espresso);">#AW-<?php echo $order_id; ?></strong> has been
                successfully placed. We've sent a confirmation email to you and our concierge team will contact you
                shortly for shipment updates.
            </p>

            <div style="display: flex; flex-direction: column; gap: 15px;">
                <a href="index.php" class="btn btn-gold" style="letter-spacing: 2px;">Continue Shopping</a>
                <a href="customer/dashboard.php"
                    style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted);">View
                    Order History</a>
            </div>

            <div
                style="margin-top: 60px; border-top: 1px solid rgba(154, 123, 100, 0.1); padding-top: 40px; display: flex; justify-content: space-around;">
                <div style="text-align: center;">
                    <i class="fas fa-truck-loading"
                        style="color: var(--brand-latte); font-size: 1.2rem; margin-bottom: 10px;"></i>
                    <p style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; margin: 0;">Inspected
                    </p>
                </div>
                <div style="text-align: center;">
                    <i class="fas fa-box-open"
                        style="color: var(--brand-latte); font-size: 1.2rem; margin-bottom: 10px;"></i>
                    <p style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; margin: 0;">Secured</p>
                </div>
                <div style="text-align: center;">
                    <i class="fas fa-shipping-fast"
                        style="color: var(--brand-latte); font-size: 1.2rem; margin-bottom: 10px;"></i>
                    <p style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; margin: 0;">Shipped</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>