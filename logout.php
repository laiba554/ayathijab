<?php
session_start();
session_unset();
session_destroy();
header("Location: customer/login.php"); // Default redirect
exit();
?>
