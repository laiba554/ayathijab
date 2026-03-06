<?php
require_once 'config/config.php';
require_once 'config/db.php';

echo "<h1>Product Status Test</h1>";

// Check all products
$all = $conn->query("SELECT product_id, product_name, status FROM products");
echo "<h2>All Products:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Name</th><th>Status</th></tr>";
while ($p = $all->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $p['product_id'] . "</td>";
    echo "<td>" . $p['product_name'] . "</td>";
    echo "<td><strong>" . ($p['status'] ?? 'NULL') . "</strong></td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";

// Check active products
$active = $conn->query("SELECT product_id, product_name, status FROM products WHERE status = 'active'");
echo "<h2>Active Products (status = 'active'):</h2>";
echo "<p>Found: <strong>" . $active->num_rows . " products</strong></p>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Name</th><th>Status</th></tr>";
while ($p = $active->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $p['product_id'] . "</td>";
    echo "<td>" . $p['product_name'] . "</td>";
    echo "<td>" . $p['status'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>