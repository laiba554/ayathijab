<?php
ob_start(); // Start output buffering
define('PAGE_TITLE', 'Manage Variants');
require_once 'includes/header.php';

$product_id = isset($_GET['id']) ? sanitize_input($_GET['id']) : null;

if (!$product_id) {
    redirect('admin/products.php');
}

// Fetch Product Info
$stmt = $conn->prepare("SELECT product_name FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) {
    redirect('admin/products.php');
}
$product = $res->fetch_assoc();

// Handle Add Variant
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $size = sanitize_input($_POST['size']);
    $color = sanitize_input($_POST['color']);
    $fabric = sanitize_input($_POST['fabric']);
    $price = sanitize_input($_POST['variant_price']);
    $stock = sanitize_input($_POST['variant_stock']);

    if (empty($stock) || empty($price)) {
        set_flash_message('error', 'Price and Stock are required.');
    } else {
        $sql = "INSERT INTO product_variants (product_id, size, color, fabric, variant_price, variant_stock) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssdi", $product_id, $size, $color, $fabric, $price, $stock);

        if ($stmt->execute()) {
            set_flash_message('success', 'Variant added successfully.');
        } else {
            set_flash_message('error', 'Error adding variant.');
        }
    }
    // Redirect to prevent resubmission
    redirect("admin/product_variants.php?id=$product_id");
}

// Handle Delete
if (isset($_GET['delete'])) {
    $variant_id = sanitize_input($_GET['delete']);
    $del = $conn->prepare("DELETE FROM product_variants WHERE variant_id = ?");
    $del->bind_param("i", $variant_id);
    $del->execute();

    set_flash_message('success', "Variant deleted.");
    redirect("admin/product_variants.php?id=$product_id");
}

// Fetch Variants
$variants = $conn->query("SELECT * FROM product_variants WHERE product_id = $product_id");
?>

<style>
/* Force Responsive - Product Variants */
* {
    box-sizing: border-box !important;
}

.variants-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.variants-row {
    display: flex;
    gap: 30px;
}

.variants-form {
    flex: 1;
    min-width: 300px;
}

.variants-list {
    flex: 2;
    min-width: 400px;
}

.variants-cards {
    display: none;
}

/* iPad Pro, Surface Pro - 1024px */
@media screen and (max-width: 1024px) {
    .variants-row {
        gap: 20px;
    }
    
    .variants-form {
        min-width: 250px;
    }
    
    .variants-list {
        min-width: 350px;
    }
    
    table {
        font-size: 0.85rem !important;
    }
}

/* iPad Portrait, Surface Portrait - 900px */
@media screen and (max-width: 900px) {
    .variants-row {
        flex-direction: column !important;
        gap: 20px;
    }
    
    .variants-form,
    .variants-list {
        width: 100% !important;
        min-width: 100% !important;
    }
    
    .variants-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .variants-header h2 {
        font-size: 1.4rem;
    }
    
    .variants-header .btn {
        width: 100%;
        text-align: center;
    }
    
    /* Hide table, show cards */
    .variants-list table {
        display: none !important;
    }
    
    .variants-cards {
        display: block !important;
    }
}

/* Mobile - 768px */
@media screen and (max-width: 768px) {
    .variants-header h2 {
        font-size: 1.3rem !important;
    }
    
    .variants-row {
        gap: 15px;
    }
}

/* Small Mobile - 480px */
@media screen and (max-width: 480px) {
    .variants-header h2 {
        font-size: 1.1rem !important;
    }
}

/* Variant Card Styles */
.variant-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.variant-card-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 15px;
}

.variant-info {
    font-size: 0.9rem;
}

.variant-label {
    color: #666;
    display: block;
    margin-bottom: 3px;
    font-size: 0.85rem;
    font-weight: 500;
}

.variant-value {
    font-weight: 600;
    color: #333;
    font-size: 1rem;
}

.variant-actions {
    text-align: center;
    padding-top: 12px;
    border-top: 1px solid #eee;
}

.variant-actions a {
    display: inline-block;
    width: 100%;
    padding: 10px;
    background: #dc3545;
    color: white !important;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 600;
    text-align: center;
}

.variant-actions a:hover {
    background: #c82333;
}
</style>

<div class="variants-header">
    <h2>Variants for: <?php echo htmlspecialchars($product['product_name']); ?></h2>
    <a href="products.php" class="btn" style="background: #666;">Back to Products</a>
</div>

<div class="variants-row">

    <!-- Add Form -->
    <div class="variants-form">
        <div class="form-container" style="margin: 0; max-width: 100%;">
            <h3>Add New Variant</h3>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Size (Optional)</label>
                    <input type="text" name="size" placeholder="e.g. S, M, L, XL">
                </div>
                <div class="form-group">
                    <label>Color (Optional)</label>
                    <input type="text" name="color" placeholder="e.g. Black, Navy">
                </div>
                <div class="form-group">
                    <label>Fabric (Optional)</label>
                    <input type="text" name="fabric" placeholder="e.g. Silk, Cotton">
                </div>
                <div class="form-group">
                    <label>Price *</label>
                    <input type="number" step="0.01" name="variant_price" required placeholder="Override base price">
                </div>
                <div class="form-group">
                    <label>Stock *</label>
                    <input type="number" name="variant_stock" required>
                </div>
                <button type="submit" class="btn btn-success">Add Variant</button>
            </form>
        </div>
    </div>

    <!-- List -->
    <div class="variants-list">
        <h3>Existing Variants</h3>
        
        <!-- Desktop Table -->
        <table>
            <thead>
                <tr>
                    <th>Size</th>
                    <th>Color</th>
                    <th>Fabric</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($v = $variants->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $v['size'] ? $v['size'] : '-'; ?></td>
                        <td><?php echo $v['color'] ? $v['color'] : '-'; ?></td>
                        <td><?php echo $v['fabric'] ? $v['fabric'] : '-'; ?></td>
                        <td><?php echo formatPricePKR($v['variant_price']); ?></td>
                        <td><?php echo $v['variant_stock']; ?></td>
                        <td>
                            <a href="product_variants.php?id=<?php echo $product_id; ?>&delete=<?php echo $v['variant_id']; ?>"
                                onclick="return confirm('Delete this variant?');" style="color: red;">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <!-- Mobile/Tablet Cards -->
        <div class="variants-cards">
            <?php
            $variants->data_seek(0);
            while ($v = $variants->fetch_assoc()):
            ?>
                <div class="variant-card">
                    <div class="variant-card-grid">
                        <div class="variant-info">
                            <span class="variant-label">Size:</span>
                            <span class="variant-value"><?php echo $v['size'] ? $v['size'] : '-'; ?></span>
                        </div>
                        <div class="variant-info">
                            <span class="variant-label">Color:</span>
                            <span class="variant-value"><?php echo $v['color'] ? $v['color'] : '-'; ?></span>
                        </div>
                        <div class="variant-info">
                            <span class="variant-label">Fabric:</span>
                            <span class="variant-value"><?php echo $v['fabric'] ? $v['fabric'] : '-'; ?></span>
                        </div>
                        <div class="variant-info">
                            <span class="variant-label">Price:</span>
                            <span class="variant-value"><?php echo formatPricePKR($v['variant_price']); ?></span>
                        </div>
                        <div class="variant-info">
                            <span class="variant-label">Stock:</span>
                            <span class="variant-value"><?php echo $v['variant_stock']; ?></span>
                        </div>
                    </div>
                    <div class="variant-actions">
                        <a href="product_variants.php?id=<?php echo $product_id; ?>&delete=<?php echo $v['variant_id']; ?>"
                            onclick="return confirm('Delete this variant?');">
                            <i class="fas fa-trash"></i> Delete Variant
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
<?php ob_end_flush(); // End output buffering ?>