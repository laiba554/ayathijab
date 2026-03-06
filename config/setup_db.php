<?php
// Script to initialize the database from schema.sql

// Load configuration
require_once 'config.php';

// Connect to MySQL server (without selecting DB yet)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read schema file
$schemaFile = __DIR__ . '/schema.sql';
if (!file_exists($schemaFile)) {
    die("Error: schema.sql file not found.");
}

$sql = file_get_contents($schemaFile);

// Execute multi-query
if ($conn->multi_query($sql)) {
    echo "Database and tables created successfully!\n";

    // Clear results to avoid sync errors
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
}
else {
    echo "Error creating database: " . $conn->error;
}

$conn->close();
?>
