<?php
session_start();
require_once 'config/config.php';
require_once 'config/db.php';

echo "<h2>Direct Cart Insert Test</h2>";

$customer_id = 3; // Aapka customer ID
$product_id = 8;  // Product ID
$quantity = 1;

// Check cart table columns
$desc = $conn->query("DESCRIBE cart");
echo "<h3>Cart Table Columns:</h3>";
echo "<ul>";
while ($col = $desc->fetch_assoc()) {
    echo "<li><strong>" . $col['Field'] . "</strong> - " . $col['Type'] . " (Null: " . $col['Null'] . ", Default: " . $col['Default'] . ")</li>";
}
echo "</ul>";

// Try insert
$sql = "INSERT INTO cart (customer_id, product_id, quantity) VALUES ($customer_id, $product_id, $quantity)";
echo "<h3>SQL Query:</h3>";
echo "<code>$sql</code><br><br>";

if ($conn->query($sql)) {
    echo "✅ <strong>INSERT SUCCESSFUL!</strong><br>";
} else {
    echo "❌ <strong>INSERT FAILED!</strong><br>";
    echo "Error: " . $conn->error . "<br>";
}

// Show current cart
echo "<h3>Current Cart Data:</h3>";
$result = $conn->query("SELECT * FROM cart WHERE customer_id = $customer_id");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>cart_id</th><th>customer_id</th><th>product_id</th><th>quantity</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $key => $val) {
            echo "<td>$val</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Cart is empty or query failed: " . $conn->error;
}
?>