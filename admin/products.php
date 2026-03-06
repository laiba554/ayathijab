<?php
define('PAGE_TITLE', 'Products');
require_once 'includes/header.php';

// Fetch all products with category names
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>

<style>
/* Products Page Responsive Styles */
.products-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.products-table {
    display: block;
}

.products-cards {
    display: none;
}

/* Tablet - iPad Pro, Surface Pro */
@media (max-width: 1024px) {
    .table {
        font-size: 0.85rem;
    }
    
    .table th,
    .table td {
        padding: 8px;
    }
    
    .table img {
        width: 40px !important;
        height: 40px !important;
    }
    
    .btn {
        padding: 4px 8px !important;
        font-size: 0.75rem !important;
    }
}

/* Tablet Portrait & Mobile - Card View */
@media (max-width: 900px) {
    .products-table {
        display: none;
    }
    
    .products-cards {
        display: block;
    }
    
    .products-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .products-header h2 {
        font-size: 1.5rem;
    }
    
    .products-header .btn {
        width: 100%;
        text-align: center;
        padding: 12px !important;
    }
    
    .product-card {
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .product-card-header {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .product-image-wrapper {
        width: 80px;
        height: 80px;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        background: #f9f9f9;
        flex-shrink: 0;
    }
    
    .product-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-main-info {
        flex: 1;
    }
    
    .product-name {
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 5px;
        color: #333;
    }
    
    .product-category {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }
    
    .product-status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .product-card-body {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 15px;
    }
    
    .product-info-item {
        font-size: 0.9rem;
    }
    
    .product-info-label {
        color: #666;
        display: block;
        margin-bottom: 3px;
        font-size: 0.85rem;
    }
    
    .product-info-value {
        font-weight: 600;
        color: #333;
        font-size: 1rem;
    }
    
    .product-card-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    
    .product-card-actions .btn {
        width: 100%;
        text-align: center;
        padding: 10px 8px !important;
        font-size: 0.85rem !important;
    }
}

@media (max-width: 768px) {
    .products-header h2 {
        font-size: 1.3rem;
    }
    
    .product-card {
        padding: 12px;
    }
    
    .product-image-wrapper {
        width: 70px;
        height: 70px;
    }
    
    .product-name {
        font-size: 1rem;
    }
    
    .product-card-body {
        font-size: 0.85rem;
    }
}

@media (max-width: 480px) {
    .products-header h2 {
        font-size: 1.2rem;
    }
    
    .product-image-wrapper {
        width: 60px;
        height: 60px;
    }
    
    .product-name {
        font-size: 0.95rem;
    }
    
    .product-card-actions {
        grid-template-columns: 1fr;
    }
    
    .product-card-actions .btn {
        padding: 8px !important;
    }
}
</style>

<div class="products-header">
    <h2>Product Management</h2>
    <a href="product_form.php" class="btn btn-success">Add New Product</a>
</div>

<!-- Desktop Table View -->
<div class="products-table">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['product_id']; ?></td>
                            <td>
                                <?php if ($row['image']): ?>
                                    <img src="<?php echo BASE_URL . $row['image']; ?>" alt="Product Image"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td><?php echo formatPricePKR($row['price']); ?></td>
                            <td><?php echo $row['stock_quantity']; ?></td>
                            <td>
                                <span class="badge <?php echo $row['status'] == 'active' ? 'success-msg' : 'error-msg'; ?>"
                                    style="padding: 2px 5px; font-size: 0.8em;">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="product_images.php?id=<?php echo $row['product_id']; ?>" class="btn"
                                    style="padding: 5px 10px; font-size: 0.8rem; background: #17a2b8;">Images</a>
                                <a href="product_variants.php?id=<?php echo $row['product_id']; ?>" class="btn"
                                    style="padding: 5px 10px; font-size: 0.8rem; background: #6f42c1;">Variants</a>
                                <a href="product_form.php?id=<?php echo $row['product_id']; ?>" class="btn action-btn">Edit</a>
                                <a href="product_delete.php?id=<?php echo $row['product_id']; ?>" class="btn btn-danger action-btn"
                                    onclick="return confirmDelete();">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No products found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile/Tablet Card View -->
<div class="products-cards">
    <?php
    // Reset result pointer for card view
    $result->data_seek(0);
    
    if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
        <div class="product-card">
            <div class="product-card-header">
                <div class="product-image-wrapper">
                    <?php if ($row['image']): ?>
                        <img src="<?php echo BASE_URL . $row['image']; ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                    <?php else: ?>
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #ccc;">
                            <i class="fas fa-image" style="font-size: 30px;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="product-main-info">
                    <div class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></div>
                    <div class="product-category"><?php echo htmlspecialchars($row['category_name']); ?></div>
                    <span class="product-status-badge <?php echo $row['status'] == 'active' ? 'success-msg' : 'error-msg'; ?>">
                        <?php echo ucfirst($row['status']); ?>
                    </span>
                </div>
            </div>
            
            <div class="product-card-body">
                <div class="product-info-item">
                    <span class="product-info-label">ID:</span>
                    <span class="product-info-value">#<?php echo $row['product_id']; ?></span>
                </div>
                <div class="product-info-item">
                    <span class="product-info-label">Price:</span>
                    <span class="product-info-value"><?php echo formatPricePKR($row['price']); ?></span>
                </div>
                <div class="product-info-item">
                    <span class="product-info-label">Stock:</span>
                    <span class="product-info-value"><?php echo $row['stock_quantity']; ?></span>
                </div>
            </div>
            
            <div class="product-card-actions">
                <a href="product_images.php?id=<?php echo $row['product_id']; ?>" class="btn" style="background: #17a2b8; color: white;">
                    <i class="fas fa-images"></i> Images
                </a>
                <a href="product_variants.php?id=<?php echo $row['product_id']; ?>" class="btn" style="background: #6f42c1; color: white;">
                    <i class="fas fa-layer-group"></i> Variants
                </a>
                <a href="product_form.php?id=<?php echo $row['product_id']; ?>" class="btn action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="product_delete.php?id=<?php echo $row['product_id']; ?>" class="btn btn-danger" onclick="return confirmDelete();">
                    <i class="fas fa-trash"></i> Delete
                </a>
            </div>
        </div>
    <?php
        endwhile;
    else:
    ?>
        <div class="product-card">
            <p style="text-align: center; margin: 0;">No products found.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>