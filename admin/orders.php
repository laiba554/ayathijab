<?php
define('PAGE_TITLE', 'Manage Orders');
require_once 'includes/header.php';

// Fetch all orders
$sql = "SELECT o.*, c.name as customer_name 
        FROM orders o 
        JOIN customers c ON o.customer_id = c.customer_id 
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);
?>

<style>
/* Desktop Table */
.orders-table {
    display: block;
}

.orders-cards {
    display: none;
}

/* Mobile Card Layout */
@media (max-width: 768px) {
    .orders-table {
        display: none;
    }
    
    .orders-cards {
        display: block;
    }
    
    .order-card {
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .order-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .order-id {
        font-weight: 700;
        font-size: 1.1rem;
        color: #333;
    }
    
    .order-status {
        font-size: 0.85rem;
        padding: 5px 10px;
        border-radius: 4px;
        background: #f0f0f0;
        font-weight: 600;
    }
    
    .order-card-body {
        margin-bottom: 15px;
    }
    
    .order-info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }
    
    .order-info-label {
        color: #666;
        font-weight: 500;
    }
    
    .order-info-value {
        color: #333;
        font-weight: 600;
    }
    
    .order-card-footer {
        text-align: center;
    }
    
    .order-card-footer .btn {
        width: 100%;
        padding: 12px;
    }
    
    h2 {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    h2 {
        font-size: 1.3rem;
    }
    
    .order-card {
        padding: 15px;
    }
    
    .order-id {
        font-size: 1rem;
    }
    
    .order-info-row {
        font-size: 0.85rem;
    }
}
</style>

<h2>Order Management</h2>

<!-- Desktop Table View -->
<div class="orders-table">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['order_date'])); ?></td>
                            <td><?php echo formatPricePKR($row['total_amount']); ?></td>
                            <td>
                                <?php
                                $status = $row['status'] ?? 'pending';
                                $status_text = ($status === 'shipped') ? 'Dispatched' : ucfirst($status);
                                echo $status_text;
                                ?>
                            </td>
                            <td>
                                <a href="order_details.php?id=<?php echo $row['order_id']; ?>" class="btn action-btn">View Details</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Card View -->
<div class="orders-cards">
    <?php
    // Reset result pointer for mobile view
    $result->data_seek(0);
    
    if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
            $status = $row['status'] ?? 'pending';
            $status_text = ($status === 'shipped') ? 'Dispatched' : ucfirst($status);
    ?>
        <div class="order-card">
            <div class="order-card-header">
                <span class="order-id">#<?php echo $row['order_id']; ?></span>
                <span class="order-status"><?php echo $status_text; ?></span>
            </div>
            <div class="order-card-body">
                <div class="order-info-row">
                    <span class="order-info-label">Customer:</span>
                    <span class="order-info-value"><?php echo htmlspecialchars($row['customer_name']); ?></span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Date:</span>
                    <span class="order-info-value"><?php echo date('M d, Y', strtotime($row['order_date'])); ?></span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Total:</span>
                    <span class="order-info-value"><?php echo formatPricePKR($row['total_amount']); ?></span>
                </div>
            </div>
            <div class="order-card-footer">
                <a href="order_details.php?id=<?php echo $row['order_id']; ?>" class="btn action-btn">View Details</a>
            </div>
        </div>
    <?php
        endwhile;
    else:
    ?>
        <div class="order-card">
            <p style="text-align: center; margin: 0;">No orders found.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>