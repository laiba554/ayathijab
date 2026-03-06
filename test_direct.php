<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/config.php';
require_once 'config/db.php';

echo "<h2>Direct Test</h2>";
echo "Customer ID: " . (isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 'NOT SET') . "<br>";

// Direct insert without any functions
$customer_id = 3;
$product_id = 8;
$quantity = 1;

$sql = "INSERT INTO cart (customer_id, product_id, quantity) VALUES ($customer_id, $product_id, $quantity)";

if ($conn->query($sql)) {
    echo "✅ INSERT SUCCESSFUL!<br>";
} else {
    echo "❌ FAILED: " . $conn->error . "<br>";
}

// Show cart
$result = $conn->query("SELECT * FROM cart WHERE customer_id = 3");
echo "<h3>Cart Contents:</h3>";
while ($row = $result->fetch_assoc()) {
    echo "Cart ID: " . $row['cart_id'] . " | Product: " . $row['product_id'] . " | Qty: " . $row['quantity'] . "<br>";
}
?>