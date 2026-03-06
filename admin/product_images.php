<?php
ob_start();
define('PAGE_TITLE', 'Manage Images');
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

// Handle Image Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['images'])) {
    $target_dir = "../public/uploads/products/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $count = count($_FILES['images']['name']);
    $success_count = 0;

    for ($i = 0; $i < $count; $i++) {
        if ($_FILES['images']['error'][$i] == 0) {
            $file_extension = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array(strtolower($file_extension), $allowed_extensions)) {
                $new_filename = uniqid() . '_' . $i . '.' . $file_extension;
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_file)) {
                    $image_path = "public/uploads/products/" . $new_filename;

                    $sql = "INSERT INTO product_images (product_id, image_path) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $product_id, $image_path);
                    $stmt->execute();
                    $success_count++;
                }
            }
        }
    }

    if ($success_count > 0) {
        set_flash_message('success', "$success_count images uploaded successfully.");
    } else {
        set_flash_message('error', "No images uploaded.");
    }
    redirect("admin/product_images.php?id=$product_id");
}

// Handle Delete
if (isset($_GET['delete'])) {
    $image_id = sanitize_input($_GET['delete']);
    // Get path to delete file
    $dq = $conn->prepare("SELECT image_path FROM product_images WHERE image_id = ?");
    $dq->bind_param("i", $image_id);
    $dq->execute();
    $dres = $dq->get_result();
    if ($drow = $dres->fetch_assoc()) {
        $path = "../" . $drow['image_path'];
        if (file_exists($path)) {
            unlink($path);
        }
    }

    $del = $conn->prepare("DELETE FROM product_images WHERE image_id = ?");
    $del->bind_param("i", $image_id);
    $del->execute();

    set_flash_message('success', "Image deleted.");
    redirect("admin/product_images.php?id=$product_id");
}

// Fetch Images
$images = $conn->query("SELECT * FROM product_images WHERE product_id = $product_id");
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}

.page-header h2 {
    margin: 0;
    font-size: 24px;
}

.content-wrapper {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.upload-section {
    flex: 1;
    min-width: 280px;
}

.images-section {
    flex: 2;
    min-width: 280px;
}

.images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.image-card {
    position: relative;
    border: 1px solid #ddd;
    padding: 5px;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.image-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.image-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 4px;
}

.delete-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #e74c3c;
    color: white;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.delete-btn:hover {
    background: #c0392b;
}

.form-container {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

.form-container h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #333;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #555;
}

.form-group input[type="file"] {
    width: 100%;
    padding: 10px;
    border: 2px dashed #ccc;
    border-radius: 6px;
    background: white;
    cursor: pointer;
    transition: border-color 0.2s;
}

.form-group input[type="file"]:hover {
    border-color: #4CAF50;
}

.btn {
    padding: 10px 20px;
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

.no-images {
    text-align: center;
    padding: 40px 20px;
    color: #999;
    font-size: 16px;
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
    
    .content-wrapper {
        flex-direction: column;
        gap: 20px;
    }
    
    .upload-section,
    .images-section {
        min-width: 100%;
    }
    
    .images-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px;
    }
    
    .image-card img {
        height: 120px;
    }
    
    .form-container {
        padding: 15px;
    }
}

@media (max-width: 480px) {
    .page-header h2 {
        font-size: 18px;
    }
    
    .images-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .btn {
        width: 100%;
        text-align: center;
    }
}
</style>

<div class="page-header">
    <h2>Images for: <?php echo htmlspecialchars($product['product_name']); ?></h2>
    <a href="products.php" class="btn" style="background: #666; color: white;">Back to Products</a>
</div>

<div class="content-wrapper">
    <!-- Upload Form -->
    <div class="upload-section">
        <div class="form-container">
            <h3>Upload New Images</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Select Images (Multiple allowed)</label>
                    <input type="file" name="images[]" multiple accept="image/*" required>
                    <small style="display: block; margin-top: 5px; color: #666;">Allowed: JPG, JPEG, PNG, GIF, WEBP</small>
                </div>
                <button type="submit" class="btn btn-success">Upload Images</button>
            </form>
        </div>
    </div>

    <!-- Existing Images -->
    <div class="images-section">
        <h3>Existing Images (<?php echo $images->num_rows; ?>)</h3>
        
        <?php if ($images->num_rows > 0): ?>
            <div class="images-grid">
                <?php while ($img = $images->fetch_assoc()): ?>
                    <div class="image-card">
                        <img src="<?php echo BASE_URL . $img['image_path']; ?>" 
                             alt="Product Image"
                             loading="lazy">
                        <a href="product_images.php?id=<?php echo $product_id; ?>&delete=<?php echo $img['image_id']; ?>"
                           onclick="return confirm('Are you sure you want to delete this image?');"
                           class="delete-btn"
                           title="Delete Image">&times;</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-images">
                <p>📷 No images uploaded yet. Upload your first image above!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php';
ob_end_flush(); // OUTPUT BUFFERING END
?>