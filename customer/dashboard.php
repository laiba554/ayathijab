<?php
session_start();
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../helpers/functions.php';
require_customer_login();

$customer_id = $_SESSION['customer_id'];
$sql = "SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$orders = $stmt->get_result();

define('PAGE_TITLE', 'My Account Dashboard');
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

/* Responsive Sidebar */
.sidebar-sticky {
    position: sticky;
    top: 120px;
}

@media (max-width: 991px) {
    .sidebar-sticky {
        position: relative !important;
        top: 0 !important;
        margin-bottom: 30px;
    }
}

@media (max-width: 768px) {
    aside[style*="padding: 40px"] {
        padding: 25px !important;
    }

    aside div[style*="margin-bottom: 40px"] {
        margin-bottom: 25px !important;
        padding-bottom: 20px !important;
    }

    aside div[style*="width: 80px"] {
        width: 60px !important;
        height: 60px !important;
        font-size: 1.5rem !important;
        margin-bottom: 12px !important;
    }

    aside h3 {
        font-size: 1.1rem !important;
    }

    aside p {
        font-size: 0.75rem !important;
    }

    aside nav {
        gap: 8px !important;
    }

    aside nav a {
        padding: 10px 0 !important;
        font-size: 0.85rem !important;
        gap: 12px !important;
    }

    aside nav a[style*="margin-top: 20px"] {
        margin-top: 15px !important;
        padding-top: 15px !important;
    }
}

@media (max-width: 576px) {
    aside[style*="padding: 40px"] {
        padding: 20px !important;
    }

    aside nav a {
        font-size: 0.8rem !important;
    }
}

/* Responsive Main Content */
@media (max-width: 768px) {
    main[style*="padding: 50px"] {
        padding: 30px !important;
    }

    h2[style*="font-size: 2.2rem"] {
        font-size: 1.8rem !important;
        margin-bottom: 30px !important;
        padding-bottom: 15px !important;
    }
}

@media (max-width: 576px) {
    main[style*="padding: 50px"] {
        padding: 25px !important;
    }

    h2[style*="font-size: 2.2rem"] {
        font-size: 1.5rem !important;
        margin-bottom: 25px !important;
    }
}

/* Responsive Empty State */
@media (max-width: 576px) {
    div[style*="padding: 60px"] {
        padding: 40px 20px !important;
    }

    div[style*="padding: 60px"] p {
        font-size: 0.95rem !important;
        margin-bottom: 20px !important;
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

    .table-responsive tbody {
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

    .table-responsive td::before {
        content: attr(data-label);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        color: var(--brand-latte);
        letter-spacing: 1px;
        flex: 0 0 40%;
    }

    .table-responsive td:last-child {
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
    }

    .table-responsive td:last-child::before {
        margin-bottom: 5px;
    }

    .table-responsive td:last-child a {
        width: 100%;
        text-align: center;
    }
}

@media (max-width: 576px) {
    .table-responsive tr {
        padding: 15px;
        margin-bottom: 15px;
    }

    .table-responsive td {
        padding: 8px 0 !important;
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }

    .table-responsive td::before {
        flex: none;
        margin-bottom: 3px;
    }

    .table-responsive td span {
        font-size: 0.7rem !important;
        padding: 4px 10px !important;
    }
}

/* Responsive Grid */
@media (max-width: 991px) {
    .row.g-5 {
        gap: 2rem !important;
    }
}

@media (max-width: 576px) {
    .row.g-5 {
        gap: 1.5rem !important;
    }
}

/* Responsive Button */
@media (max-width: 576px) {
    .btn {
        padding: 10px 16px !important;
        font-size: 0.85rem !important;
    }

    .btn-outline-gold {
        padding: 8px 16px !important;
        font-size: 0.75rem !important;
    }
}
</style>

<div style="background: var(--bg-cream); padding: 80px 0; min-height: 80vh;">
    <div class="container">
        <div class="row g-5">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3">
                <aside class="sidebar-sticky"
                    style="background: white; padding: 40px; border: 1px solid #eee; z-index: 1;">
                    <div
                        style="text-align: center; margin-bottom: 40px; border-bottom: 1px solid #eee; padding-bottom: 30px;">
                        <div
                            style="width: 80px; height: 80px; background: var(--brand-latte); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 15px; font-family: var(--font-heading);">
                            <?php echo substr($_SESSION['customer_name'], 0, 1); ?>
                        </div>
                        <h3 style="margin: 0; font-size: 1.2rem;">
                            <?php echo htmlspecialchars($_SESSION['customer_name']); ?>
                        </h3>
                        <p
                            style="margin: 5px 0 0; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">
                            Customer</p>
                    </div>

                    <nav style="display: flex; flex-direction: column; gap: 10px;">
                        <a href="dashboard.php"
                            style="display: flex; align-items: center; gap: 15px; padding: 12px 0; color: var(--accent-gold); font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; text-decoration: none;">
                            <i class="fas fa-shopping-bag"></i> My Orders
                        </a>
                        <a href="../wishlist.php"
                            style="display: flex; align-items: center; gap: 15px; padding: 12px 0; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; text-decoration: none;">
                            <i class="far fa-heart"></i> Wishlist
                        </a>
                        <a href="profile.php"
                            style="display: flex; align-items: center; gap: 15px; padding: 12px 0; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; text-decoration: none;">
                            <i class="far fa-user"></i> Profile Settings
                        </a>
                        <a href="../logout.php"
                            style="display: flex; align-items: center; gap: 15px; padding: 12px 0; color: #e74c3c; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px; text-decoration: none;">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </nav>
                </aside>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <main style="background: white; padding: 50px; border: 1px solid #eee;">
                    <h2
                        style="font-size: 2.2rem; margin-bottom: 40px; border-bottom: 2px solid var(--primary-gold); padding-bottom: 20px;">
                        Order History</h2>

                    <?php if ($orders->num_rows == 0): ?>
                        <div style="text-align: center; padding: 60px;">
                            <p style="color: var(--text-muted); margin-bottom: 30px;">You haven't placed any orders yet.
                            </p>
                            <a href="../products.php" class="btn btn-gold">Browse Collections</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table" style="width: 100%; border-collapse: collapse; min-width: 600px;">
                                <thead>
                                    <tr style="text-align: left; border-bottom: 1px solid #eee;">
                                        <th
                                            style="padding: 15px 0; font-family: var(--font-heading); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
                                            Order #</th>
                                        <th
                                            style="padding: 15px 0; font-family: var(--font-heading); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
                                            Date</th>
                                        <th
                                            style="padding: 15px 0; font-family: var(--font-heading); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
                                            Status</th>
                                        <th
                                            style="padding: 15px 0; font-family: var(--font-heading); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
                                            Total</th>
                                        <th
                                            style="padding: 15px 0; font-family: var(--font-heading); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; text-align: right;">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $orders->fetch_assoc()): ?>
                                        <tr style="border-bottom: 1px solid #f9f9f9;">
                                            <td data-label="Order #" style="padding: 20px 0; font-weight: 700; color: var(--primary-gold);">
                                                #AW-<?php echo $row['order_id']; ?></td>
                                            <td data-label="Date" style="padding: 20px 0; font-size: 0.9rem; color: var(--text-muted);">
                                                <?php echo date('M d, Y', strtotime($row['order_date'])); ?>
                                            </td>
                                           <td data-label="Status" style="padding: 20px 0;">
                                                <?php 
                                                // Check if status exists, otherwise use default
                                                $status = isset($row['status']) ? $row['status'] : 'Pending';
                                                
                                                // Determine status color
                                                $statusStyle = '';
                                                switch ($status) {
                                                    case 'Pending':
                                                        $statusStyle = 'background: #fff3cd; color: #856404;';
                                                        break;
                                                    case 'Processing':
                                                        $statusStyle = 'background: #cce5ff; color: #004085;';
                                                        break;
                                                    case 'Shipped':
                                                        $statusStyle = 'background: #d4edda; color: #155724;';
                                                        break;
                                                    case 'Completed':
                                                    case 'Delivered':
                                                        $statusStyle = 'background: #d1ecf1; color: #0c5460;';
                                                        break;
                                                    case 'Cancelled':
                                                        $statusStyle = 'background: #f8d7da; color: #721c24;';
                                                        break;
                                                    default:
                                                        $statusStyle = 'background: #e2e3e5; color: #383d41;';
                                                }
                                                ?>
                                                <span style="display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; <?php echo $statusStyle; ?>">
                                                    <?php echo htmlspecialchars($status); ?>
                                                </span>
                                            </td>
                                            <td data-label="Total" style="padding: 20px 0; font-weight: 700; color: var(--text-main);">
                                                <?php echo formatPricePKR($row['total_amount']); ?>
                                            </td>
                                            <td data-label="Action" style="padding: 20px 0; text-align: right;">
                                                <a href="order_details.php?id=<?php echo $row['order_id']; ?>"
                                                    class="btn btn-outline-gold"
                                                    style="padding: 8px 20px; font-size: 0.8rem;">View Details</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </main>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>