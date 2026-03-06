<?php
// category_form.php
ob_start(); // YE LINE SABSE PEHLE HONI CHAHIYE
define('PAGE_TITLE', 'Category Form');
require_once 'includes/header.php';

$category_id = isset($_GET['id']) ? sanitize_input($_GET['id']) : null;
$category = ['category_name' => '', 'description' => ''];

// Fetch category for editing
if ($category_id) {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
    } else {
        header("Location: categories.php");
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = sanitize_input($_POST['category_name']);
    $description = sanitize_input($_POST['description']);
    
    if (empty($category_name)) {
        set_flash_message('error', "Category name is required.");
    } else {
        if ($category_id) {
            // Update
            $stmt = $conn->prepare("UPDATE categories SET category_name = ?, description = ? WHERE category_id = ?");
            $stmt->bind_param("ssi", $category_name, $description, $category_id);
            $message = "Category updated successfully.";
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $category_name, $description);
            $message = "Category added successfully.";
        }
        
        if ($stmt->execute()) {
            set_flash_message('success', $message);
            header("Location: categories.php");
            exit;
        } else {
            set_flash_message('error', "Error saving category.");
        }
    }
}
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

.btn-secondary {
    background: #666;
    color: white;
}

.btn-secondary:hover {
    background: #555;
}

.btn-success {
    background: #4CAF50;
    color: white;
}

.btn-success:hover {
    background: #45a049;
}

.form-container {
    max-width: 700px;
    margin: 0 auto;
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

.form-group label .required {
    color: #f44336;
}

.form-group input[type="text"],
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    box-sizing: border-box;
    transition: border-color 0.3s;
}

.form-group input[type="text"]:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #4CAF50;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 25px;
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
    
    .form-container {
        padding: 20px;
        border-radius: 0;
        margin: 0 -15px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}
</style>

<div class="page-header">
    <h2><?php echo $category_id ? 'Edit Category' : 'Add New Category'; ?></h2>
    <a href="categories.php" class="btn btn-secondary">← Back to Categories</a>
</div>

<div class="form-container">
    <form method="POST" action="">
        <div class="form-group">
            <label>Category Name <span class="required">*</span></label>
            <input type="text" 
                   name="category_name" 
                   value="<?php echo htmlspecialchars($category['category_name']); ?>" 
                   required 
                   placeholder="e.g., Hijabs, Abayas, Accessories">
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" 
                      placeholder="Brief description about this category (optional)"><?php echo htmlspecialchars($category['description']); ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <?php echo $category_id ? 'Update Category' : 'Add Category'; ?>
            </button>
            <a href="categories.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php 
require_once 'includes/footer.php';
ob_end_flush(); // YE LINE SABSE END MAI
?>