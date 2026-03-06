<?php
require_once 'config.php';
require_once 'db.php';

$result = $conn->query("DESCRIBE coupons");
if ($result) {
    echo "Columns in coupons table:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}
else {
    echo "Error describing table: " . $conn->error;
}
?>
