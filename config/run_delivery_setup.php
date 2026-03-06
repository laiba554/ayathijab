<?php
require_once 'config.php';
require_once 'db.php';

echo "Setting up delivery charges system...\n\n";

// Read and execute SQL file
$sql_file = __DIR__ . '/delivery_charges_setup.sql';
$sql = file_get_contents($sql_file);

// Split by semicolon and execute each statement
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $statement) {
    if (!empty($statement)) {
        if ($conn->query($statement) === TRUE) {
            echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
        } else {
            echo "✗ Error: " . $conn->error . "\n";
            echo "  Statement: " . substr($statement, 0, 100) . "...\n";
        }
    }
}

echo "\n✓ Delivery charges system setup completed!\n";
?>