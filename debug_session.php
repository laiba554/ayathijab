<?php
session_start();
echo "<h1>Session Debug</h1>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Customer Login Check:</h3>";
if (isset($_SESSION['customer_id'])) {
    echo "✅ customer_id SET: " . $_SESSION['customer_id'] . "<br>";
} else {
    echo "❌ customer_id NOT SET<br>";
}

if (isset($_SESSION['customer_name'])) {
    echo "✅ customer_name SET: " . $_SESSION['customer_name'] . "<br>";
} else {
    echo "❌ customer_name NOT SET<br>";
}

echo "<br><a href='login.php'>Go to Login</a> | <a href='index.php'>Home</a>";
?>