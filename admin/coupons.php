<?php
define('PAGE_TITLE', 'Manage Coupons');
require_once 'includes/header.php';

// Fetch Coupons
$sql = "SELECT * FROM coupons ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.page-header h2 {
    margin: 0;
    font-size: 24px;
}

.table-responsive {
    overflow-x: auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    -webkit-overflow-scrolling: touch;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    min-width: 100%;
}

.table thead {
    background: #f5f5f5;
}

.table th {
    padding: 12px 10px;
    text-align: left;
    font-weight: 600;
    color: #555;
    border-bottom: 2px solid #ddd;
    white-space: nowrap;
    font-size: 14px;
}

.table td {
    padding: 12px 10px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.table-hover tbody tr:hover {
    background: #f9f9f9;
}

.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 13px;
    transition: all 0.3s;
    white-space: nowrap;
}

.btn-success {
    background: #4CAF50;
    color: white;
}

.btn-success:hover {
    background: #45a049;
}

.action-btn {
    background: #ff9800;
    color: white;
    padding: 5px 10px;
    font-size: 12px;
    margin: 2px;
}

.action-btn:hover {
    background: #7A5440;
}

.btn-danger {
    background: #7A5440;
    color: white;
}

.btn-danger:hover {
    background: #7A5440;;
}

.badge {
    padding: 4px 8px;
    font-size: 12px;
    border-radius: 3px;
    display: inline-block;
    white-space: nowrap;
}

.success-msg {
    background: #d4edda;
    color: #7A5440;
}

.error-msg {
    background: #f8d7da;
    color: #721c24;
}

.actions-cell {
    white-space: nowrap;
}

.mobile-only {
    display: none;
}

/* Desktop - No changes */
@media (min-width: 769px) {
    .table {
        min-width: 100%;
    }
}

/* Tablet */
@media (max-width: 968px) {
    .table th:nth-child(4),
    .table td:nth-child(4) {
        display: none;
    }
}

/* Mobile - Card Layout */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .page-header h2 {
        font-size: 20px;
    }
    
    .btn-success {
        width: 100%;
        text-align: center;
        padding: 12px;
    }
    
    .table-responsive {
        margin: 0 -15px;
        border-radius: 0;
        overflow-x: visible;
    }
    
    .table,
    .table thead,
    .table tbody,
    .table th,
    .table td,
    .table tr {
        display: block;
    }
    
    .table thead {
        display: none;
    }
    
    .table tr {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        background: white;
    }
    
    .table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border: none;
        text-align: right;
    }
    
    .table td:before {
        content: attr(data-label);
        font-weight: 600;
        text-align: left;
        flex: 0 0 40%;
        color: #555;
    }
    
    .table td:first-child {
        padding-top: 0;
    }
    
    .table td:last-child {
        padding-bottom: 0;
    }
    
    .actions-cell {
        flex-direction: column;
        align-items: stretch !important;
    }
    
    .actions-cell:before {
        display: none;
    }
    
    .btn,
    .action-btn {
        width: 100%;
        text-align: center;
        margin: 3px 0;
        padding: 10px;
        font-size: 13px;
    }
    
    .badge {
        margin-left: auto;
    }
}

@media (max-width: 480px) {
    .page-header h2 {
        font-size: 18px;
    }
    
    .table td {
        font-size: 13px;
    }
}
</style>

<div class="page-header">
    <h2>Coupon Management</h2>
    <a href="coupon_form.php" class="btn btn-success">Add New Coupon</a>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Min Order</th>
                <th>Expiry</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr style="opacity: <?php echo $row['status'] == 'inactive' ? '0.6' : '1'; ?>;">
                        <td data-label="Code"><strong><?php echo htmlspecialchars($row['coupon_code']); ?></strong></td>
                        <td data-label="Type"><?php echo ucfirst($row['discount_type']); ?></td>
                        <td data-label="Value">
                            <?php echo $row['discount_type'] == 'percentage' ? $row['discount_value'] . '%' : formatPricePKR($row['discount_value']); ?>
                        </td>
                        <td data-label="Min Order"><?php echo formatPricePKR($row['min_order_amount']); ?></td>
                        <td data-label="Expiry">
                            <?php
                            $expiry = strtotime($row['expiry_date']);
                            if ($expiry < time()) {
                                echo "<span style='color:red;'>Expired (" . date('M d, Y', $expiry) . ")</span>";
                            } else {
                                echo date('M d, Y', $expiry);
                            }
                            ?>
                        </td>
                        <td data-label="Status">
                            <span class="badge <?php echo $row['status'] == 'active' ? 'success-msg' : 'error-msg'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td class="actions-cell">
                            <a href="coupon_actions.php?action=toggle&id=<?php echo $row['coupon_id']; ?>" class="btn"
                                style="padding: 5px 10px; font-size: 0.8rem; background: #7A5440; color: white;">
                                <?php echo $row['status'] == 'active' ? 'Disable' : 'Enable'; ?>
                            </a>
                            <a href="coupon_form.php?id=<?php echo $row['coupon_id']; ?>" class="btn action-btn"
                            style="background: #7A5440;">Edit</a>
                            <a href="coupon_actions.php?action=delete&id=<?php echo $row['coupon_id']; ?>"
                                class="btn btn-danger action-btn" onclick="return confirm('Delete this coupon?');">Delete</a>
                        </td>
                    </tr>
                    <?php
                endwhile; ?>
                <?php
            else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No coupons found.</td>
                </tr>
                <?php
            endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>