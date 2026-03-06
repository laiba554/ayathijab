<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/functions.php';

// Calculate Cart Info
$cart_count = 0;
if (is_customer_logged_in()) {
    $c_id = $_SESSION['customer_id'];
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE customer_id = ?");
    $stmt->bind_param("i", $c_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $cart_count = $row['total'] ? $row['total'] : 0;
    }
} else {
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $qty) {
            $cart_count += $qty;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo defined('PAGE_TITLE') ? PAGE_TITLE . ' - ' : ''; ?>AyyatulHijab</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" href="<?php echo BASE_URL; ?>public/uploads/logo_ayathijab.jpg">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS (Must be after Bootstrap) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .logo img {
            height: 70px;
            width: auto;
            display: block;
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #333;
            padding: 5px;
        }

        @media (max-width: 968px) {
            .header-content {
                flex-wrap: wrap;
                position: relative;
            }

            .logo img {
                height: 60px;
            }

            .mobile-menu-toggle {
                display: block;
                order: 2;
                margin-left: auto;
            }

            .nav-icons {
                order: 3;
            }

            .nav-menu {
                display: none;
                order: 4;
                width: 100%;
                flex-direction: column;
                background: white;
                padding: 15px 0;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                margin-top: 15px;
            }

            .nav-menu.active {
                display: flex !important;
            }

            .nav-menu a {
                padding: 12px 15px !important;
                border-bottom: 1px solid #f0f0f0;
            }

            .nav-menu a:last-child {
                border-bottom: none;
            }

            .nav-icons a {
                font-size: 18px;
                padding: 0 8px;
            }
        }

        @media (max-width: 576px) {
            .header-content {
                padding: 10px 15px !important;
            }

            .logo img {
                height: 50px;
            }

            .nav-icons a {
                font-size: 16px;
                padding: 0 6px;
            }

            .mobile-menu-toggle {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    <div class="top-announcement"></div>

    <header>
        <div class="container header-content">
            <a href="<?php echo BASE_URL; ?>index.php" class="logo">
                <img src="<?php echo BASE_URL; ?>public/uploads/logo_ayathijab.jpg" alt="AYAAT-UL-HIJAB">
            </a>

            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>

            <nav class="nav-menu" id="navMenu">
                <a href="<?php echo BASE_URL; ?>index.php">Home</a>
                <a href="<?php echo BASE_URL; ?>products.php">Shop</a>
                <a href="<?php echo BASE_URL; ?>about.php">About Us</a>
                <a href="<?php echo BASE_URL; ?>contact.php">Contact Us</a>
                <a href="<?php echo BASE_URL; ?>feedback.php">Feedback</a>
            </nav>

            <div class="nav-icons">
                <a href="<?php echo BASE_URL; ?>wishlist.php" title="Wishlist"><i class="far fa-heart"></i></a>
                <a href="<?php echo BASE_URL; ?>cart.php" title="Cart" style="position: relative;">
                    <i class="fas fa-shopping-bag"></i>
                    <?php if ($cart_count > 0): ?>
                        <span
                            style="position: absolute; top: -10px; right: -12px; background: var(--brand-latte); color: white; font-size: 0.6rem; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <?php echo $cart_count; ?>
                        </span>
                    <?php endif; ?>
                </a>

                <?php if (is_customer_logged_in()): ?>
                    <a href="<?php echo BASE_URL; ?>customer/dashboard.php" title="My Account"><i
                            class="far fa-user"></i></a>
                    <a href="<?php echo BASE_URL; ?>logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>customer/login.php" title="Login"><i class="far fa-user"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <script>
        function toggleMobileMenu() {
            const navMenu = document.getElementById('navMenu');
            const toggle = document.querySelector('.mobile-menu-toggle i');
            navMenu.classList.toggle('active');

            if (navMenu.classList.contains('active')) {
                toggle.classList.remove('fa-bars');
                toggle.classList.add('fa-times');
            } else {
                toggle.classList.remove('fa-times');
                toggle.classList.add('fa-bars');
            }
        }

        document.addEventListener('click', function (event) {
            const navMenu = document.getElementById('navMenu');
            const toggle = document.querySelector('.mobile-menu-toggle');

            if (navMenu.classList.contains('active') &&
                !navMenu.contains(event.target) &&
                !toggle.contains(event.target)) {
                navMenu.classList.remove('active');
                document.querySelector('.mobile-menu-toggle i').classList.remove('fa-times');
                document.querySelector('.mobile-menu-toggle i').classList.add('fa-bars');
            }
        });
    </script>

    <div style="min-height: 70vh;">
        <?php display_flash_message(); ?>