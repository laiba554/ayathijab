<?php
ob_start(); // OUTPUT BUFFERING START - YE LINE SABSE PEHLE
define('PAGE_TITLE', 'Delivery Settings');
require_once 'includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $delivery_charge = floatval($_POST['delivery_charge']);
    $free_delivery_enabled = isset($_POST['free_delivery_enabled']) ? 1 : 0;
    $free_delivery_threshold = floatval($_POST['free_delivery_threshold']);
    
    $stmt = $conn->prepare("UPDATE delivery_settings SET delivery_charge = ?, free_delivery_enabled = ?, free_delivery_threshold = ? WHERE setting_id = 1");
    $stmt->bind_param("did", $delivery_charge, $free_delivery_enabled, $free_delivery_threshold);
    
    if ($stmt->execute()) {
        set_flash_message('success', 'Delivery settings updated successfully.');
    } else {
        set_flash_message('error', 'Failed to update delivery settings.');
    }
    $stmt->close();
    
    header("Location: delivery_settings.php");
    exit();
}

// Fetch current settings
$result = $conn->query("SELECT * FROM delivery_settings WHERE setting_id = 1");
$settings = $result->fetch_assoc();

if (!$settings) {
    // Create default settings if not exist
    $conn->query("INSERT INTO delivery_settings (delivery_charge, free_delivery_enabled, free_delivery_threshold) VALUES (200.00, 0, 5000.00)");
    $result = $conn->query("SELECT * FROM delivery_settings WHERE setting_id = 1");
    $settings = $result->fetch_assoc();
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Delivery Charges Settings</h2>
</div>

<div class="form-container" style="max-width: 600px;">
    <form action="" method="POST">
        <div class="form-group">
            <label>Delivery Charge Amount (PKR)</label>
            <input type="number" name="delivery_charge" step="0.01" min="0" 
                   value="<?php echo $settings['delivery_charge']; ?>" required
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            <small style="color: #666; display: block; margin-top: 5px;">Standard delivery charge for all orders</small>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="free_delivery_enabled" value="1" 
                       <?php echo $settings['free_delivery_enabled'] ? 'checked' : ''; ?>
                       style="width: 18px; height: 18px; cursor: pointer;">
                <span>Enable Free Delivery</span>
            </label>
            <small style="color: #666; display: block; margin-top: 5px;">Allow free delivery for orders above threshold</small>
        </div>

        <div class="form-group">
            <label>Free Delivery Threshold (PKR)</label>
            <input type="number" name="free_delivery_threshold" step="0.01" min="0" 
                   value="<?php echo $settings['free_delivery_threshold']; ?>" required
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            <small style="color: #666; display: block; margin-top: 5px;">Minimum order amount for free delivery</small>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <h4 style="margin-bottom: 15px;">Current Configuration:</h4>
            <ul style="list-style: none; padding: 0; background: #f9f9f9; padding: 15px; border-radius: 4px;">
                <li style="margin-bottom: 8px;">
                    <strong>Standard Delivery:</strong> Rs <?php echo number_format($settings['delivery_charge'], 2); ?>
                </li>
                <li style="margin-bottom: 8px;">
                    <strong>Free Delivery:</strong> 
                    <?php if ($settings['free_delivery_enabled']): ?>
                        <span style="color: green;">Enabled</span> (Orders above Rs <?php echo number_format($settings['free_delivery_threshold'], 2); ?>)
                    <?php else: ?>
                        <span style="color: #999;">Disabled</span>
                    <?php endif; ?>
                </li>
            </ul>
        </div>

        <button type="submit" class="btn btn-success" style="margin-top: 20px;">Update Settings</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; 
ob_end_flush(); // OUTPUT BUFFERING END
?>