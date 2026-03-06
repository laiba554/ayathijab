<?php
require_once 'config.php';
require_once 'db.php';

$result = $conn->query("SHOW TABLES");
if ($result) {
    echo "Existing Tables:\n";
    while ($row = $result->fetch_array()) {
        echo $row[0] . "\n";
    }
}
else {
    echo "Error: " . $conn->error;
}
?>
