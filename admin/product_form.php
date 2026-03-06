<?php
ob_start();
session_start();
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../helpers/functions.php';
require_admin_login();

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $product_id > 0;

// Fetch categories
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");

// Initialize variables
$product = [
    'product_name' => '',
    'category_id' => '',
    'description' => '',
    'price' => '',
    'stock_quantity' => '',
    'status' => 1,  // ✅ Default to 1 (active)
    'image' => ''
];

if ($is_edit) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        // ✅ Ensure status is always 1 or 0
        $product['status'] = intval($product['status']);
        if ($product['status'] !== 0 && $product['status'] !== 1) {
            $product['status'] = 1;
        }
    } else {
        set_flash_message('error', 'Product not found.');
        header("Location: products.php");
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = sanitize_input($_POST['product_name']);
    $category_id = intval($_POST['category_id']);
    $description = sanitize_input($_POST['description']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    
    // ✅ CRITICAL FIX: Convert status to integer (1 or 0)
    $status = isset($_POST['status']) ? intval($_POST['status']) : 1;
    if ($status !== 0 && $status !== 1) {
        $status = 1; // Default to active
    }
    
    // Handle image upload
    $image_path = $product['image'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../public/uploads/products/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = 'public/uploads/products/' . $new_filename;
                
                if ($is_edit && !empty($product['image']) && file_exists('../' . $product['image'])) {
                    unlink('../' . $product['image']);
                }
            }
        }
    }
    
    // Validate
    if (empty($product_name) || empty($category_id) || empty($price)) {
        set_flash_message('error', 'Product name, category, and price are required.');
    } else {
        if ($is_edit) {
            // ✅ Use integer for status
            $stmt = $conn->prepare("UPDATE products SET product_name = ?, category_id = ?, description = ?, price = ?, stock_quantity = ?, status = ?, image = ? WHERE product_id = ?");
            $stmt->bind_param("sisdiisi", $product_name, $category_id, $description, $price, $stock_quantity, $status, $image_path, $product_id);
        } else {
            // ✅ Use integer for status
            $stmt = $conn->prepare("INSERT INTO products (product_name, category_id, description, price, stock_quantity, status, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sisdisi", $product_name, $category_id, $description, $price, $stock_quantity, $status, $image_path);
        }
        
        if ($stmt->execute()) {
            set_flash_message('success', $is_edit ? 'Product updated successfully!' : 'Product added successfully!');
            header("Location: products.php");
            exit;
        } else {
            set_flash_message('error', 'Failed to save product: ' . $conn->error);
        }
    }
}

define('PAGE_TITLE', $is_edit ? 'Edit Product' : 'Add Product');
require_once 'includes/header.php';
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4"><?php echo $is_edit ? 'Edit Product' : 'Add Product'; ?></h1>
        <a href="products.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Products
        </a>
    </div>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['flash_message']['type'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
            <?php echo htmlspecialchars($_SESSION['flash_message']['text']); unset($_SESSION['flash_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-box"></i> Product Information
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="product_name" class="form-control" 
                               value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php 
                            $categories->data_seek(0);
                            while ($cat = $categories->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $cat['category_id']; ?>" 
                                        <?php echo $product['category_id'] == $cat['category_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Price (PKR) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" step="0.01" 
                               value="<?php echo $product['price']; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="stock_quantity" class="form-control" 
                               value="<?php echo $product['stock_quantity']; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <!-- ✅ Use integer values 1 and 0 -->
                        <select name="status" class="form-select" required>
                            <option value="1" <?php echo $product['status'] == 1 ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo $product['status'] == 0 ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <?php if ($is_edit && !empty($product['image'])): ?>
                        <div class="mb-2">
                            <img src="<?php echo BASE_URL . $product['image']; ?>" 
                                 alt="Current Image" 
                                 style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; padding: 5px;"
                                 onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                            <p class="text-muted small mt-1">Current image (upload new to replace)</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">Allowed: JPG, PNG, GIF, WEBP</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $is_edit ? 'Update Product' : 'Add Product'; ?>
                    </button>
                    <a href="products.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <?php if ($is_edit): ?>
                        <a href="product_images.php?id=<?php echo $product_id; ?>" class="btn btn-info ms-auto">
                            <i class="fas fa-images"></i> Manage Additional Images
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
require_once 'includes/footer.php';
ob_end_flush();
?>