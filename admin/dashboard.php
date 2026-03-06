<?php
define('PAGE_TITLE', 'Dashboard');
require_once 'includes/header.php';

// Fetch stats
$customer_count = 0;
$product_count = 0;
$order_count = 0;

$c_query = "SELECT COUNT(*) as total FROM customers";
if ($result = $conn->query($c_query)) {
    $row = $result->fetch_assoc();
    $customer_count = $row['total'];
}

$p_query = "SELECT COUNT(*) as total FROM products";
if ($result = $conn->query($p_query)) {
    $row = $result->fetch_assoc();
    $product_count = $row['total'];
}

$o_query = "SELECT COUNT(*) as total FROM orders";
if ($result = $conn->query($o_query)) {
    $row = $result->fetch_assoc();
    $order_count = $row['total'];
}
?>

<style>
/* Dashboard Responsive Styles */
.dashboard-header {
    margin-bottom: 30px;
}

.dashboard-header h2 {
    font-size: 2rem;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-box {
    background: #ffff;
    color: white;
    padding: 30px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.stat-box:hover {
    transform: translateY(-5px);
}

.stat-box h3 {
    font-size: 1rem;
    margin-bottom: 15px;
    opacity: 0.9;
    font-weight: 600;
    margin-top: 0;
}

.stat-box p {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.recent-activity {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.recent-activity h3 {
    margin-bottom: 20px;
    font-size: 1.5rem;
    margin-top: 0;
}

.quick-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

/* Tablet Responsive */
@media (max-width: 992px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .dashboard-header h2 {
        font-size: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .stat-box {
        padding: 25px;
    }
    
    .stat-box h3 {
        font-size: 0.9rem;
    }
    
    .stat-box p {
        font-size: 2rem;
    }
    
    .recent-activity {
        padding: 20px;
    }
    
    .quick-actions {
        flex-direction: column;
    }
    
    .quick-actions a {
        width: 100%;
        text-align: center;
        padding: 12px 20px !important;
    }
}

/* Small Mobile */
@media (max-width: 480px) {
    .dashboard-header h2 {
        font-size: 1.3rem;
    }
    
    .stat-box {
        padding: 20px;
    }
    
    .stat-box p {
        font-size: 1.8rem;
    }
}
</style>

<div class="dashboard-header">
    <h2>Welcome Back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h2>
</div>

<div class="stats-grid">
    <div class="stat-box">
        <h3>Total Customers</h3>
        <p><?php echo $customer_count; ?></p>
    </div>
    <div class="stat-box">
        <h3>Total Products</h3>
        <p><?php echo $product_count; ?></p>
    </div>
    <div class="stat-box">
        <h3>Total Orders</h3>
        <p><?php echo $order_count; ?></p>
    </div>
</div>

<div class="recent-activity">
    <h3>Quick Actions</h3>
    <div class="quick-actions">
        <a href="product_form.php" class="btn btn-success" style="padding: 15px 30px;background: #7A5440; text-decoration: none;">Add Product</a>
        <a href="orders.php" class="btn" style="background: #7A5440; color: white; padding: 15px 30px; text-decoration: none;">View Orders</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>