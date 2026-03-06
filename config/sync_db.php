<?php
require_once 'config.php';
require_once 'db.php';

// 1. Fix Coupons Table Schema
$queries = [
    // Ensure all columns exist in coupons table
    "ALTER TABLE coupons ADD COLUMN IF NOT EXISTS min_order_amount DECIMAL(10,2) DEFAULT 0.00",
    "ALTER TABLE coupons ADD COLUMN IF NOT EXISTS usage_limit INT DEFAULT 0",
    "ALTER TABLE coupons ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",

    // Modify ENUMs to match code expectations (lowercase)
    "ALTER TABLE coupons MODIFY COLUMN discount_type ENUM('percentage', 'flat') NOT NULL",
    "ALTER TABLE coupons MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'",

    // Ensure Orders table has coupon tracking columns
    "ALTER TABLE orders ADD COLUMN IF NOT EXISTS coupon_id INT DEFAULT NULL",
    "ALTER TABLE orders ADD COLUMN IF NOT EXISTS discount_amount DECIMAL(10,2) DEFAULT 0.00 AFTER total_amount"
];

foreach ($queries as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Executed: " . substr($sql, 0, 50) . "...\n";
    } else {
        echo "Error: " . $conn->error . " (on query: " . substr($sql, 0, 50) . "...)\n";
    }
}

echo "Database sync completed.\n";
?>