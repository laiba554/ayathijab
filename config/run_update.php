<?php
require_once 'config.php';
require_once 'db.php';

$sql_file = __DIR__ . '/schema_update.sql';

if (file_exists($sql_file)) {
    $sql_content = file_get_contents($sql_file);

    // Split by semicolon to run multiple queries
    // This is a simple splitter, might break on complex stored procs but fine for this schema
    $queries = explode(';', $sql_content);

    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if ($conn->query($query) === TRUE) {
                echo "Query executed successfully.\n";
            }
            else {
                echo "Error executing query: " . $conn->error . "\n";
            // Don't stop on error (e.g. duplicate column), just continue
            }
        }
    }
    echo "Database update completed.";
}
else {
    echo "Schema update file not found.";
}
?>
