<?php
session_start();
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'helpers/functions.php';

echo "<h1>Direct Cart Add Test</h1>";

// Test product ID 8
$product_id = 8;
$quantity = 1;

echo "<h3>Login Status:</h3>";
if (isset($_SESSION['customer_id'])) {
    echo "✅ Logged in as: " . $_SESSION['customer_name'] . " (ID: " . $_SESSION['customer_id'] . ")<br>";
    $customer_id = $_SESSION['customer_id'];
    
    // Direct database insert
    $check = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE customer_id = ? AND product_id = ?");
    $check->bind_param("ii", $customer_id, $product_id);
    $check->execute();
    $result = $check->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $new_qty = $row['quantity'] + $quantity;
        $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
        $update->bind_param("ii", $new_qty, $row['cart_id']);
        if ($update->execute()) {
            echo "✅ <strong>Cart updated! New quantity: $new_qty</strong><br>";
        } else {
            echo "❌ Update failed: " . $conn->error . "<br>";
        }
    } else {
        $insert = $conn->prepare("INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->bind_param("iii", $customer_id, $product_id, $quantity);
        if ($insert->execute()) {
            echo "✅ <strong>Product added to cart!</strong><br>";
        } else {
            echo "❌ Insert failed: " . $conn->error . "<br>";
        }
    }
    
    echo "<h3>Current Cart in Database:</h3>";
    $cart_result = $conn->query("SELECT c.*, p.product_name FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.customer_id = $customer_id");
    if ($cart_result->num_rows > 0) {
        echo "<ul>";
        while ($item = $cart_result->fetch_assoc()) {
            echo "<li>" . $item['product_name'] . " (Qty: " . $item['quantity'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "Cart is empty<br>";
    }
    
} else {
    echo "❌ NOT logged in<br>";
    echo "<a href='login.php'>Please login first</a>";
}

echo "<br><br>";
echo "<a href='cart.php' style='padding: 10px 20px; background: gold; color: black; text-decoration: none; border-radius: 5px;'>View Cart</a>";
?>
```

---

## **Testing Steps:**

### **Step 1: Login Check**
```
http://localhost/ayathijab/test_cart.php
```
- Agar "❌ Customer is NOT logged in" dikhe to pehle login karein

### **Step 2: Login Karein**
```
http://localhost/ayathijab/login.php
```
- Email aur password enter karein

### **Step 3: Direct Cart Test**
```
http://localhost/ayathijab/test_add_cart.php
```
- Ye directly cart mein add karega
- Success message dikhai dega

### **Step 4: Product Details Test**
```
http://localhost/ayathijab/product_details.php?id=8
```
- "Add to Bag" button click karein
- Cart page pe redirect hoga

### **Step 5: Cart Check**
```
http://localhost/ayathijab/cart.php