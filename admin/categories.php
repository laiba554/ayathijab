<?php
define('PAGE_TITLE', 'Categories');
require_once 'includes/header.php';

// Fetch all categories
$sql = "SELECT * FROM categories ORDER BY created_at DESC";
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
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.table thead {
    background: #f5f5f5;
}

.table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #555;
    border-bottom: 2px solid #ddd;
    white-space: nowrap;
}

.table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.table-hover tbody tr:hover {
    background: #f9f9f9;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    transition: all 0.3s;
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
    padding: 6px 12px;
    font-size: 13px;
    margin-right: 5px;
}

.action-btn:hover {
    background: #e68900;
}

.btn-danger {
    background: #f44336;
    color: white;
    padding: 6px 12px;
    font-size: 13px;
}

.btn-danger:hover {
    background: #da190b;
}

.actions-cell {
    white-space: nowrap;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .page-header h2 {
        font-size: 20px;
    }
    
    .table-responsive {
        margin: 0 -15px;
        border-radius: 0;
    }
    
    .table {
        font-size: 14px;
    }
    
    .table th,
    .table td {
        padding: 10px 8px;
    }
    
    /* Hide less important columns on mobile */
    .table th:nth-child(1),
    .table td:nth-child(1),
    .table th:nth-child(4),
    .table td:nth-child(4) {
        display: none;
    }
    
    .action-btn,
    .btn-danger {
        display: block;
        width: 100%;
        margin: 5px 0;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .table {
        font-size: 13px;
    }
    
    .table th,
    .table td {
        padding: 8px 5px;
    }
    
    .table th:nth-child(3),
    .table td:nth-child(3) {
        display: none;
    }
}
</style>

<div class="page-header">
    <h2>Category Management</h2>
    <a href="category_form.php" class="btn btn-success">Add New Category</a>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['category_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td class="actions-cell">
                            <a href="category_form.php?id=<?php echo $row['category_id']; ?>" class="btn action-btn">Edit</a>
                            <a href="category_delete.php?id=<?php echo $row['category_id']; ?>"
                                class="btn btn-danger action-btn" onclick="return confirmDelete();">Delete</a>
                        </td>
                    </tr>
                    <?php
                endwhile; ?>
                <?php
            else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No categories found.</td>
                </tr>
                <?php
            endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>