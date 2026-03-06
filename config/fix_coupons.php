<?php
require_once 'config.php';
require_once 'db.php';

// Fix Coupons Table
$sql = "ALTER TABLE coupons 
        ADD COLUMN min_order_amount DECIMAL(10,2) DEFAULT 0.00,
        ADD COLUMN usage_limit INT DEFAULT 0,
        ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";

if ($conn->query($sql) === TRUE) {
    echo "Coupons table updated successfully.\n";
}
else {
    echo "Error updating coupons table: " . $conn->error . "\n";
}
?>
