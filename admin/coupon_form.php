<?php
ob_start(); // OUTPUT BUFFERING START - YE LINE SABSE PEHLE
define('PAGE_TITLE', 'Coupon Form');
require_once 'includes/header.php';

$coupon_id = isset($_GET['id']) ? sanitize_input($_GET['id']) : null;
$coupon = null;
$error = '';
$success = '';

if ($coupon_id) {
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE coupon_id = ?");
    $stmt->bind_param("i", $coupon_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $coupon = $res->fetch_assoc();
    } else {
        redirect('admin/coupons.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = strtoupper(sanitize_input($_POST['coupon_code']));
    $type = sanitize_input($_POST['discount_type']);
    $value = sanitize_input($_POST['discount_value']);
    $min_order = sanitize_input($_POST['min_order_amount']);
    $expiry = sanitize_input($_POST['expiry_date']);
    $usage_limit = sanitize_input($_POST['usage_limit']);

    // Validation
    if (empty($code) || empty($value) || empty($expiry)) {
        $error = "Please fill all required fields.";
    } else {
        // Check uniqueness if new or changed
        $check_sql = "SELECT coupon_id FROM coupons WHERE coupon_code = ? AND coupon_id != ?";
        $check_id = $coupon_id ? $coupon_id : 0;
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("si", $code, $check_id);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $error = "Coupon code already exists.";
        } else {
            if ($coupon_id) {
                // Update
                $sql = "UPDATE coupons SET coupon_code=?, discount_type=?, discount_value=?, min_order_amount=?, expiry_date=?, usage_limit=? WHERE coupon_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssddsii", $code, $type, $value, $min_order, $expiry, $usage_limit, $coupon_id);
            } else {
                // Insert
                $sql = "INSERT INTO coupons (coupon_code, discount_type, discount_value, min_order_amount, expiry_date, usage_limit, status) VALUES (?, ?, ?, ?, ?, ?, 'active')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssddsi", $code, $type, $value, $min_order, $expiry, $usage_limit);
            }

            if ($stmt->execute()) {
                set_flash_message('success', 'Coupon saved successfully.');
                redirect('admin/coupons.php');
            } else {
                $error = "Database error: " . $conn->error;
            }
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

.form-container {
    max-width: 900px;
    margin: 0 auto;
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-container h2 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #333;
}

.error-msg {
    background: #f8d7da;
    color: #721c24;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
}

.row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.col-12 {
    grid-column: 1 / -1;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #555;
    font-size: 14px;
}

.form-control,
.form-select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    box-sizing: border-box;
    transition: border-color 0.3s;
}

.form-control:focus,
.form-select:focus {
    outline: none;
    border-color: #4CAF50;
}

.text-muted {
    font-size: 12px;
    color: #666;
    display: block;
    margin-top: 5px;
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
    margin-right: 10px;
}

.btn-success {
    background: #4CAF50;
    color: white;
}

.btn-success:hover {
    background: #45a049;
}

.btn-secondary {
    background: #666;
    color: white;
}

.btn-secondary:hover {
    background: #555;
}

.mt-4 {
    margin-top: 25px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-container {
        padding: 20px;
        border-radius: 0;
        margin: 0 -15px;
    }
    
    .form-container h2 {
        font-size: 20px;
    }
    
    .row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .btn {
        width: 100%;
        margin: 5px 0;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .form-container {
        padding: 15px;
    }
    
    .form-container h2 {
        font-size: 18px;
    }
    
    .form-control,
    .form-select {
        padding: 10px;
        font-size: 13px;
    }
    
    .form-label {
        font-size: 13px;
    }
}
</style>

<div class="form-container">
    <h2><?php echo $coupon_id ? 'Edit Coupon' : 'Create New Coupon'; ?></h2>

    <?php if ($error): ?>
        <p class="error-msg"><?php echo $error; ?></p>
        <?php
    endif; ?>

    <form method="POST">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Coupon Code</label>
                <input type="text" name="coupon_code" class="form-control"
                    value="<?php echo $coupon ? htmlspecialchars($coupon['coupon_code']) : ''; ?>" required
                    style="text-transform: uppercase;">
            </div>

            <div class="col-md-6">
                <label class="form-label">Discount Type</label>
                <select name="discount_type" class="form-select">
                    <option value="percentage" <?php echo ($coupon && $coupon['discount_type'] == 'percentage') ? 'selected' : ''; ?>>Percentage (%)</option>
                    <option value="flat" <?php echo ($coupon && $coupon['discount_type'] == 'flat') ? 'selected' : ''; ?>>
                        Flat
                        Amount (Rs.)</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Discount Value</label>
                <input type="number" step="0.01" name="discount_value" class="form-control"
                    value="<?php echo $coupon ? $coupon['discount_value'] : ''; ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Minimum Order Amount (Rs.)</label>
                <input type="number" step="0.01" name="min_order_amount" class="form-control"
                    value="<?php echo $coupon ? $coupon['min_order_amount'] : '0'; ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="expiry_date" class="form-control"
                    value="<?php echo $coupon ? $coupon['expiry_date'] : ''; ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Usage Limit</label>
                <input type="number" name="usage_limit" class="form-control"
                    value="<?php echo $coupon ? $coupon['usage_limit'] : '0'; ?>">
                <small class="text-muted">Enter 0 for unlimited usage</small>
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-success">Save Coupon</button>
                <a href="coupons.php" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; 
ob_end_flush(); // OUTPUT BUFFERING END//
?>