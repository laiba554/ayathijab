<?php
define('PAGE_TITLE', 'Customers');
require_once 'includes/header.php';

// Fetch all customers
$sql = "SELECT * FROM customers ORDER BY registered_at DESC";
$result = $conn->query($sql);
?>

<style>
.page-header {
    margin-bottom: 20px;
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

/* Desktop - No changes */
@media (min-width: 769px) {
    .table {
        min-width: 100%;
    }
}

/* Tablet */
@media (max-width: 968px) {
    .table th:nth-child(1),
    .table td:nth-child(1) {
        display: none;
    }
}

/* Mobile - Card Layout */
@media (max-width: 768px) {
    .page-header h2 {
        font-size: 20px;
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
}

@media (max-width: 480px) {
    .page-header h2 {
        font-size: 18px;
    }
    
    .table td {
        font-size: 13px;
    }
    
    .table td:before {
        font-size: 13px;
    }
}
</style>

<div class="page-header">
    <h2>Customer Management</h2>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Registered At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="ID"><?php echo $row['customer_id']; ?></td>
                        <td data-label="Name"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                        <td data-label="Phone"><?php echo htmlspecialchars($row['cell_phone']); ?></td>
                        <td data-label="Registered At"><?php echo date('M d, Y', strtotime($row['registered_at'])); ?></td>
                        <td data-label="Actions">
                            <!-- Future: Edit Customer or View Orders -->
                            <span style="color: #999;">--</span>
                        </td>
                    </tr>
                    <?php
                endwhile; ?>
                <?php
            else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No customers found.</td>
                </tr>
                <?php
            endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>