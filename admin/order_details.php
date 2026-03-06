<?php
ob_start(); // ✅ Ye sabse pehle
session_start();
define('PAGE_TITLE', 'Order Details');
require_once 'includes/header.php';

$order_id = isset($_GET['id']) ? sanitize_input($_GET['id']) : null;

if (!$order_id) {
    redirect('admin/orders.php');
}

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = sanitize_input($_POST['status']);

    // Get Current Status
    $status_stmt = $conn->prepare("SELECT status FROM orders WHERE order_id = ?");
    $status_stmt->bind_param("i", $order_id);
    $status_stmt->execute();
    $curr_res = $status_stmt->get_result();
    $curr_row = $curr_res->fetch_assoc();
    $old_status = $curr_row['status'];

    // Rule: Delivered order cannot be changed
    if ($old_status == 'delivered') {
        set_flash_message('error', 'Delivered orders cannot be modified.');
    } elseif ($new_status == $old_status) {
        set_flash_message('warning', 'Order is already in this status.');
    } else {
        // Update order status
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $new_status, $order_id);

        if ($stmt->execute()) {
            // Log history
            $hist_stmt = $conn->prepare("INSERT INTO order_status_history (order_id, old_status, new_status) VALUES (?, ?, ?)");
            $hist_stmt->bind_param("iss", $order_id, $old_status, $new_status);
            $hist_stmt->execute();

            set_flash_message('success', "Order status updated from " . ucfirst($old_status) . " to " . ucfirst($new_status) . ".");
        } else {
            set_flash_message('error', 'Failed to update status.');
        }
    }
    // Refresh to show changes
    header("Location: order_details.php?id=" . $order_id);
    exit();
}

// Fetch Order Info
$sql = "SELECT o.*, c.name, c.email, c.cell_phone, c.address as customer_address 
        FROM orders o 
        JOIN customers c ON o.customer_id = c.customer_id 
        WHERE o.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_res = $stmt->get_result();

if ($order_res->num_rows == 0) {
    set_flash_message('error', 'Order not found.');
    redirect('admin/orders.php');
}

$order = $order_res->fetch_assoc();

// Ensure all fields have default values
$order['name'] = $order['name'] ?? 'Unknown Customer';
$order['email'] = $order['email'] ?? 'No email';
$order['cell_phone'] = $order['cell_phone'] ?? 'No phone';
$order['shipping_address'] = $order['shipping_address'] ?? $order['customer_address'] ?? 'No address provided';
$order['payment_method'] = $order['payment_method'] ?? 'Cash on Delivery';
$order['order_status'] = $order['status'] ?? 'pending';
$order['order_date'] = $order['order_date'] ?? date('Y-m-d H:i:s');
$order['total_amount'] = $order['total_amount'] ?? 0;

// Fetch Items
$sql_items = "SELECT oi.*, p.product_name, p.image 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.product_id 
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items_res = $stmt_items->get_result();
?>

<style>
    /* Order Details Responsive Styles */
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .order-content {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .order-items-section {
        flex: 2;
        min-width: 300px;
    }

    .order-info-section {
        flex: 1;
        min-width: 280px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .order-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .order-header h2 {
            font-size: 1.3rem;
        }

        .order-header .btn {
            width: 100%;
            text-align: center;
        }

        .order-content {
            flex-direction: column;
        }

        .order-items-section,
        .order-info-section {
            width: 100%;
            min-width: 100%;
        }

        /* Items Table - Mobile Card View */
        .order-items-section table {
            display: none;
        }

        .order-items-mobile {
            display: block !important;
        }

        .item-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .item-card-header {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .item-image {
            width: 60px;
            height: 60px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: #f9f9f9;
            flex-shrink: 0;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .item-variant {
            font-size: 0.85rem;
            color: #666;
        }

        .item-card-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .item-detail {
            font-size: 0.9rem;
        }

        .item-detail-label {
            color: #666;
            display: block;
            margin-bottom: 3px;
        }

        .item-detail-value {
            font-weight: 600;
            color: #333;
        }

        .item-total {
            grid-column: 1 / -1;
            text-align: right;
            padding-top: 10px;
            border-top: 1px solid #eee;
            margin-top: 10px;
        }

        .grand-total-mobile {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 700;
            font-size: 1.1rem;
            margin-top: 15px;
        }

        .grand-total-mobile .label {
            color: #333;
        }

        .grand-total-mobile .value {
            color: var(--accent-gold);
            font-size: 1.2rem;
        }

        /* Form adjustments */
        .form-container {
            margin-bottom: 20px;
        }

        .form-container h3 {
            font-size: 1.1rem;
        }

        .form-container p {
            font-size: 0.9rem;
            line-height: 1.6;
        }
    }

    .order-items-mobile {
        display: none;
    }

    @media (max-width: 480px) {
        .order-header h2 {
            font-size: 1.2rem;
        }

        .item-card {
            padding: 12px;
        }

        .item-image {
            width: 50px;
            height: 50px;
        }

        .item-name {
            font-size: 0.9rem;
        }

        .item-card-body {
            font-size: 0.85rem;
        }
    }
</style>

<div class="order-header">
    <h2>Order #<?php echo $order['order_id']; ?> Details</h2>
    <a href="orders.php" class="btn" style="background: #666;">Back to Orders</a>
</div>

<div class="order-content">

    <!-- Left Column: Items -->
    <div class="order-items-section">
        <div class="form-container" style="max-width: 100%; margin: 0;">
            <h3>Items Ordered</h3>

            <!-- Desktop Table View -->
            <table style="width: 100%; margin-top: 10px; border-collapse: collapse;">
                <thead>
                    <tr style="background: #eee;">
                        <th style="padding: 10px; text-align: left;">Product</th>
                        <th style="padding: 10px; text-align: center;">Qty</th>
                        <th style="padding: 10px; text-align: right;">Price</th>
                        <th style="padding: 10px; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $items_res->fetch_assoc()):
                        // Simple image path fix - no file_exists check
                        $image_name = $item['image'] ?? '';

                        // Clean any existing path prefixes
                        $image_name = str_replace('public/uploads/products/', '', $image_name);
                        $image_name = str_replace('public/uploads/', '', $image_name);

                        // Build final path
                        if (!empty($image_name)) {
                            $image_url = BASE_URL . 'public/uploads/products/' . $image_name;
                        } else {
                            $image_url = BASE_URL . 'public/images/placeholder.jpg';
                        }

                        $product_name = htmlspecialchars($item['product_name'] ?? 'Unknown Product');
                        ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px;">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div
                                        style="width: 60px; height: 60px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; background: #f9f9f9; flex-shrink: 0;">
                                        <img src="<?php echo $image_url; ?>" alt="<?php echo $product_name; ?>"
                                            style="width: 100%; height: 100%; object-fit: cover;"
                                            onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ccc;\'><i class=\'fas fa-image\' style=\'font-size:24px;\'></i></div>'">
                                    </div>
                                    <div>
                                        <strong><?php echo $product_name; ?></strong>
                                        <?php if (isset($item['variant_name']) && $item['variant_name']): ?>
                                            <br><small style="color: #666;">Variant:
                                                <?php echo htmlspecialchars($item['variant_name']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 10px; text-align: center; font-weight: 600;">
                                <?php echo $item['quantity']; ?>
                            </td>
                            <td style="padding: 10px; text-align: right;">
                                <?php echo formatPricePKR($item['price']); ?>
                            </td>
                            <td style="padding: 10px; text-align: right; font-weight: 600;">
                                <?php echo formatPricePKR($item['price'] * $item['quantity']); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <tr style="background: #f9f9f9;">
                        <td colspan="3" style="text-align: right; padding: 15px; font-weight: bold; font-size: 1.1em;">
                            Grand Total
                        </td>
                        <td
                            style="text-align: right; padding: 15px; font-weight: bold; font-size: 1.2em; color: var(--accent-gold);">
                            <?php echo formatPricePKR($order['total_amount']); ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Mobile Card View -->
            <div class="order-items-mobile">
                <?php
                // Reset result pointer for mobile view
                $items_res->data_seek(0);
                while ($item = $items_res->fetch_assoc()):
                    // Image path fix
                    $image_name = $item['image'] ?? '';
                    $image_name = str_replace('public/uploads/products/', '', $image_name);
                    $image_name = str_replace('public/uploads/', '', $image_name);

                    if (!empty($image_name)) {
                        $image_url = BASE_URL . 'public/uploads/products/' . $image_name;
                    } else {
                        $image_url = BASE_URL . 'public/images/placeholder.jpg';
                    }

                    $product_name = htmlspecialchars($item['product_name'] ?? 'Unknown Product');
                    ?>
                    <div class="item-card">
                        <div class="item-card-header">
                            <div class="item-image">
                                <img src="<?php echo $image_url; ?>" alt="<?php echo $product_name; ?>">
                            </div>
                            <div class="item-info">
                                <div class="item-name"><?php echo $product_name; ?></div>
                                <?php if (isset($item['variant_name']) && $item['variant_name']): ?>
                                    <div class="item-variant">Variant: <?php echo htmlspecialchars($item['variant_name']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="item-card-body">
                            <div class="item-detail">
                                <span class="item-detail-label">Quantity:</span>
                                <span class="item-detail-value"><?php echo $item['quantity']; ?></span>
                            </div>
                            <div class="item-detail">
                                <span class="item-detail-label">Price:</span>
                                <span class="item-detail-value"><?php echo formatPricePKR($item['price']); ?></span>
                            </div>
                            <div class="item-total">
                                <span style="color: #666;">Total: </span>
                                <span style="font-weight: 700; color: var(--accent-gold);">
                                    <?php echo formatPricePKR($item['price'] * $item['quantity']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>

                <div class="grand-total-mobile">
                    <span class="label">Grand Total</span>
                    <span class="value"><?php echo formatPricePKR($order['total_amount']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Info & Status -->
    <div class="order-info-section">

        <!-- Status Update -->
        <div class="form-container" style="max-width: 100%; margin: 0 0 20px 0;">
            <h3>Update Status</h3>
            <form action="" method="POST">
                <input type="hidden" name="update_status" value="1">
                <div class="form-group">
                    <label>Current Status</label>
                    <select name="status"
                        style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;" <?php echo ($order['order_status'] == 'delivered') ? 'disabled' : ''; ?>>
                        <option value="pending" <?php echo ($order['order_status'] == 'pending') ? 'selected' : ''; ?>>
                            Pending</option>
                        <option value="processing" <?php echo ($order['order_status'] == 'processing') ? 'selected' : ''; ?>>
                            Processing</option>
                        <option value="shipped" <?php echo ($order['order_status'] == 'shipped') ? 'selected' : ''; ?>>
                            Dispatched</option>
                        <option value="delivered" <?php echo ($order['order_status'] == 'delivered') ? 'selected' : ''; ?>>
                            Delivered</option>
                        <option value="cancelled" <?php echo ($order['order_status'] == 'cancelled') ? 'selected' : ''; ?>>
                            Cancelled</option>
                    </select>
                    <?php if ($order['order_status'] == 'delivered'): ?>
                        <small style="color: red; display: block; margin-top: 5px;">
                            <i class="fas fa-info-circle"></i> Delivered orders cannot be changed.
                        </small>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 10px;">
                    <i class="fas fa-save"></i> Update Status
                </button>
            </form>
        </div>

        <!-- Customer Info -->
        <div class="form-container" style="max-width: 100%; margin: 0;">
            <h3><i class="fas fa-user"></i> Customer Info</h3>
            <div style="line-height: 2;">
                <p><strong><i class="fas fa-user-circle" style="width: 20px;"></i> Name:</strong><br>
                    <span style="margin-left: 25px;"><?php echo htmlspecialchars($order['name']); ?></span>
                </p>

                <p><strong><i class="fas fa-envelope" style="width: 20px;"></i> Email:</strong><br>
                    <span style="margin-left: 25px;"><?php echo htmlspecialchars($order['email']); ?></span>
                </p>

                <p><strong><i class="fas fa-phone" style="width: 20px;"></i> Phone:</strong><br>
                    <span style="margin-left: 25px;"><?php echo htmlspecialchars($order['cell_phone']); ?></span>
                </p>

                <p><strong><i class="fas fa-map-marker-alt" style="width: 20px;"></i> Shipping Address:</strong><br>
                    <span
                        style="margin-left: 25px;"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></span>
                </p>

                <p><strong><i class="fas fa-calendar" style="width: 20px;"></i> Order Date:</strong><br>
                    <span
                        style="margin-left: 25px;"><?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></span>
                </p>

                <p><strong><i class="fas fa-credit-card" style="width: 20px;"></i> Payment Method:</strong><br>
                    <span style="margin-left: 25px;"><?php echo htmlspecialchars($order['payment_method']); ?></span>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; 
ob_end_flush(); // ✅ Ye sabse last mein
?>