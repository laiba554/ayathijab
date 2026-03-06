<?php
require_once 'config.php';
require_once 'db.php';

// Add old_status column
$sql = "ALTER TABLE order_status_history 
        ADD COLUMN old_status VARCHAR(50) AFTER order_id";

if ($conn->query($sql) === TRUE) {
    echo "Table updated successfully.\n";
}
else {
    // Likely already exists or error, verify
    echo "Error updating table: " . $conn->error . "\n";
}

// Rename 'status' to 'new_status' for clarity if preferred, but user said "new status" logic.
// The table usually has 'status' meaning the resulting status.
// I will keep 'status' as the new status column, but conceptually used as new_status.
// Or I can rename it to match the request "old status, new status" more explicitly.
// Let's standardise: old_status, new_status.
$sql2 = "ALTER TABLE order_status_history CHANGE COLUMN status new_status VARCHAR(50)";
if ($conn->query($sql2) === TRUE) {
    echo "Column renamed successfully.\n";
}
else {
    echo "Error renaming column: " . $conn->error . "\n";
}
?>
