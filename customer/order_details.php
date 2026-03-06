<?php
session_start();
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../helpers/functions.php';
require_customer_login();

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$customer_id = $_SESSION['customer_id'];

// Fetch order with customer info
$sql = "SELECT o.*, c.name as customer_name, c.email as customer_email, c.cell_phone, c.address as customer_address 
        FROM orders o 
        JOIN customers c ON o.customer_id = c.customer_id 
        WHERE o.order_id = ? AND o.customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $customer_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    set_flash_message('error', 'Order not found.');
    redirect('customer/dashboard.php');
}

// Set default values for all fields
$order['shipping_name'] = $order['shipping_name'] ?? $order['customer_name'] ?? 'N/A';
$order['shipping_address'] = $order['shipping_address'] ?? $order['customer_address'] ?? 'No address provided';
$order['shipping_phone'] = $order['shipping_phone'] ?? $order['cell_phone'] ?? 'N/A';
$order['payment_method'] = $order['payment_method'] ?? 'Cash on Delivery';
$order['payment_status'] = $order['payment_status'] ?? 'Pending';
$order['order_status'] = $order['order_status'] ?? 'pending';

// Fetch order items
$items_sql = "SELECT oi.*, p.product_name, p.image 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.product_id 
              WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items = $items_stmt->get_result();

// Calculate dynamic order number for this customer
$count_sql = "SELECT COUNT(*) as order_count FROM orders WHERE customer_id = ? AND order_id <= ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("ii", $customer_id, $order_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result()->fetch_assoc();
$customer_order_number = $count_result['order_count'];

define('PAGE_TITLE', 'Order #AW-' . $customer_order_number);
require_once '../includes/header.php';
?>

<style>
    /* Responsive Page Container */
    @media (max-width: 768px) {
        div[style*="padding: 80px 0"] {
            padding: 50px 0 !important;
        }
    }

    @media (max-width: 576px) {
        div[style*="padding: 80px 0"] {
            padding: 40px 0 !important;
        }
    }

    /* Responsive Main Card */
    @media (max-width: 768px) {
        div[style*="padding: 60px"] {
            padding: 30px !important;
            border-radius: 0 !important;
        }
    }

    @media (max-width: 576px) {
        div[style*="padding: 60px"] {
            padding: 25px !important;
        }
    }

    /* Responsive Header Section */
    @media (max-width: 768px) {
        h1[style*="font-size: 2.5rem"] {
            font-size: 2rem !important;
        }

        .d-flex.flex-wrap {
            flex-direction: column !important;
            align-items: flex-start !important;
        }

        .text-lg-end {
            text-align: left !important;
            margin-top: 15px;
        }

        span[style*="padding: 8px 20px"] {
            padding: 6px 16px !important;
            font-size: 0.75rem !important;
        }
    }

    @media (max-width: 576px) {
        h1[style*="font-size: 2.5rem"] {
            font-size: 1.75rem !important;
        }

        span[style*="font-size: 0.85rem"] {
            font-size: 0.75rem !important;
        }
    }

    /* Responsive Grid */
    @media (max-width: 991px) {
        .row.g-5 {
            gap: 2rem !important;
        }

        .col-lg-8,
        .col-lg-4 {
            margin-bottom: 20px;
        }
    }

    @media (max-width: 576px) {
        .row.g-5 {
            gap: 1.5rem !important;
        }
    }

    /* Responsive Section Titles */
    @media (max-width: 768px) {
        h3[style*="font-size: 1.2rem"] {
            font-size: 1.1rem !important;
            margin-bottom: 20px !important;
        }
    }

    @media (max-width: 576px) {
        h3[style*="font-size: 1.2rem"] {
            font-size: 1rem !important;
        }
    }

    /* Responsive Table - Mobile Card View */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    @media (max-width: 768px) {
        .table-responsive table {
            display: block;
            min-width: 0 !important;
        }

        .table-responsive thead {
            display: none;
        }

        .table-responsive tbody,
        .table-responsive tfoot {
            display: block;
        }

        .table-responsive tr {
            display: block;
            margin-bottom: 20px;
            padding: 20px;
            background: #f9f9f9;
            border: 1px solid #eee !important;
            border-radius: 8px;
        }

        .table-responsive td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0 !important;
            text-align: left !important;
            border: none !important;
        }

        .table-responsive td:first-child {
            flex-direction: column;
            align-items: flex-start;
        }

        .table-responsive td:first-child>div {
            width: 100%;
        }

        .table-responsive td::before {
            content: attr(data-label);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            color: var(--accent-gold);
            letter-spacing: 1px;
            flex: 0 0 35%;
        }

        .table-responsive td:first-child::before {
            display: none;
        }

        .table-responsive tfoot tr {
            background: white !important;
            border: 2px solid var(--accent-gold) !important;
            padding: 20px !important;
        }

        .table-responsive tfoot td {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .table-responsive tfoot td::before {
            display: none;
        }
    }

    @media (max-width: 576px) {
        .table-responsive tr {
            padding: 15px;
        }

        .table-responsive td {
            padding: 8px 0 !important;
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }

        .table-responsive td::before {
            margin-bottom: 3px;
        }

        .table-responsive td:first-child>div {
            gap: 12px !important;
        }

        .table-responsive td:first-child>div>div {
            width: 50px !important;
            height: 60px !important;
        }

        .table-responsive tfoot td {
            padding: 15px !important;
        }

        .table-responsive tfoot td[style*="font-size: 1.1rem"] {
            font-size: 1rem !important;
        }

        .table-responsive tfoot td[style*="font-size: 1.5rem"] {
            font-size: 1.3rem !important;
        }
    }

    /* Responsive Sidebar */
    @media (max-width: 768px) {
        div[style*="padding: 30px"][style*="border-radius: 10px"] {
            padding: 25px !important;
            margin-bottom: 20px !important;
        }

        h4[style*="font-size: 1rem"] {
            font-size: 0.95rem !important;
            margin-bottom: 12px !important;
        }

        div[style*="padding: 30px"] p {
            font-size: 0.85rem !important;
        }
    }

    @media (max-width: 576px) {
        div[style*="padding: 30px"][style*="border-radius: 10px"] {
            padding: 20px !important;
        }

        h4[style*="font-size: 1rem"] {
            font-size: 0.9rem !important;
        }
    }

    /* Responsive Back Button */
    @media (max-width: 768px) {
        a[style*="margin-top: 40px"] {
            margin-top: 30px !important;
            padding: 14px !important;
            font-size: 0.95rem !important;
        }
    }

    @media (max-width: 576px) {
        a[style*="margin-top: 40px"] {
            margin-top: 20px !important;
            padding: 12px !important;
            font-size: 0.9rem !important;
        }
    }

    /* Image Responsive */
    img[style*="object-fit: cover"] {
        object-fit: cover;
        object-position: center;
    }
</style>

<div style="background: var(--bg-cream); padding: 80px 0; min-height: 80vh;">
    <div class="container">
        <div style="background: white; padding: 60px; border: 1px solid #eee; border-radius: 10px;">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 border-bottom pb-4 mb-4"
                style="border-bottom-color: var(--primary-gold) !important;">
                <div>
                    <span
                        style="color: var(--primary-gold); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 2px;">
                        Order Details
                    </span>
                    <h1 style="font-size: 2.5rem; margin: 10px 0 0;">#AW-<?php echo $customer_order_number; ?></h1>
                </div>
                <div class="text-lg-end">
                    <p style="margin: 15px 0 0; color: var(--text-muted); font-size: 0.9rem;">
                        Placed on <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                    </p>
                </div>
            </div>

            <div class="row g-5">
                <!-- Order Items -->
                <div class="col-lg-8">
                    <h3 style="font-family: var(--font-heading); font-size: 1.2rem; margin-bottom: 25px;">
                        <i class="fas fa-shopping-bag" style="color: var(--accent-gold);"></i> Items Ordered
                    </h3>
                    <div class="table-responsive">
                        <table style="width: 100%; border-collapse: collapse; min-width: 500px;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 2px solid #eee;">
                                    <th
                                        style="padding: 15px 10px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">
                                        Product
                                    </th>
                                    <th
                                        style="padding: 15px 10px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">
                                        Price
                                    </th>
                                    <th
                                        style="padding: 15px 10px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">
                                        Qty
                                    </th>
                                    <th
                                        style="padding: 15px 10px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; text-align: right; font-weight: 700;">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = $items->fetch_assoc()):
                                    // Simple image path fix - no file_exists check
                                    $image_name = $item['image'] ?? '';

                                    // Clean any existing path prefixes
                                    $image_name = str_replace('public/uploads/products/', '', $image_name);
                                    $image_name = str_replace('public/uploads/', '', $image_name);

                                    // Build final path
                                    if (!empty($image_name)) {
                                        $image_path = BASE_URL . 'public/uploads/products/' . $image_name;
                                    } else {
                                        $image_path = BASE_URL . 'public/images/placeholder.jpg';
                                    }
                                    ?>
                                    <tr style="border-bottom: 1px solid #f9f9f9;">
                                        <td style="padding: 20px 10px;">
                                            <div style="display: flex; align-items: center; gap: 15px;">
                                                <div
                                                    style="width: 60px; height: 70px; border: 1px solid #eee; overflow: hidden; flex-shrink: 0; border-radius: 8px; background: #f9f9f9;">
                                                    <img src="<?php echo $image_path; ?>"
                                                        alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                        style="width: 100%; height: 100%; object-fit: cover;"
                                                        onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ccc;\'><i class=\'fas fa-image\' style=\'font-size:24px;\'></i></div>'">
                                                </div>
                                                <span style="font-weight: 600; font-size: 0.95rem;">
                                                    <?php echo htmlspecialchars($item['product_name']); ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td data-label="Price" style="padding: 20px 10px; font-size: 0.9rem;">
                                            <?php echo formatPricePKR($item['price']); ?>
                                        </td>
                                        <td data-label="Quantity"
                                            style="padding: 20px 10px; font-size: 0.9rem; font-weight: 600;">
                                            <?php echo $item['quantity']; ?>
                                        </td>
                                        <td data-label="Total"
                                            style="padding: 20px 10px; text-align: right; font-weight: 700; color: var(--accent-gold);">
                                            <?php echo formatPricePKR($item['price'] * $item['quantity']); ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr style="background: #fafafa;">
                                    <td colspan="3"
                                        style="padding: 25px 10px; text-align: right; font-family: var(--font-heading); text-transform: uppercase; letter-spacing: 2px; font-weight: 700; font-size: 1.1rem;">
                                        Grand Total
                                    </td>
                                    <td
                                        style="padding: 25px 10px; text-align: right; font-size: 1.5rem; font-weight: 700; color: var(--accent-gold);">
                                        <?php echo formatPricePKR($order['total_amount']); ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Info Sidebar -->
                <div class="col-lg-4">
                    <!-- Shipping Details -->
                    <div
                        style="background: #fdfaf5; padding: 30px; border: 1px solid #eee; margin-bottom: 30px; border-radius: 10px;">
                        <h4
                            style="font-family: var(--font-heading); font-size: 1rem; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                            <i class="fas fa-truck" style="color: var(--accent-gold);"></i> Shipping Details
                        </h4>
                        <p style="font-size: 0.9rem; line-height: 1.8; margin: 0;">
                            <strong><?php echo htmlspecialchars($order['shipping_name']); ?></strong><br>
                            <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?><br>
                            <i class="fas fa-phone" style="color: var(--accent-gold);"></i>
                            <?php echo htmlspecialchars($order['shipping_phone']); ?>
                        </p>
                    </div>



                    <a href="dashboard.php" class="btn btn-gold"
                        style="width: 100%; margin-top: 40px; text-align: center;">
                        <i class="fas fa-arrow-left"></i> Back to My Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>