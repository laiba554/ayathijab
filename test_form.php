<?php
session_start();
?>
<!DOCTYPE html>
<html>
<body>
<h2>Form Test</h2>

<!-- Test Form -->
<form action="cart_action.php" method="POST">
    <input type="hidden" name="action" value="add">
    <input type="hidden" name="product_id" value="8">
    <input type="hidden" name="quantity" value="1">
    <button type="submit" style="padding:15px 30px; background:gold; border:none; cursor:pointer; font-size:18px;">
        TEST ADD TO CART
    </button>
</form>

<br><br>

<?php if(isset($_SESSION['success'])): ?>
    <p style="color:green; font-size:20px;">✅ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <p style="color:red; font-size:20px;">❌ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
<?php endif; ?>

<br>
<h3>Session Data:</h3>
<pre><?php print_r($_SESSION); ?></pre>

</body>
</html>