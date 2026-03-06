<?php
require_once 'helpers/functions.php';

if (function_exists('formatPricePKR')) {
    echo "Function exists!\n";
    echo formatPricePKR(1250) . "\n";
    echo formatPricePKR(50.5) . "\n";
}
else {
    echo "Function NOT found!\n";
}
?>
