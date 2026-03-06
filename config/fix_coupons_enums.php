<?php
require_once 'config.php';
require_once 'db.php';

// Fix ENUMs to match code (lowercase)
$sql = "ALTER TABLE coupons 
        MODIFY COLUMN discount_type ENUM('percentage', 'flat') NOT NULL,
        MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'";

if ($conn->query($sql) === TRUE) {
    echo "Coupons table ENUMs updated successfully.\n";
}
else {
    echo "Error updating ENUMs: " . $conn->error . "\n";
}
?>
