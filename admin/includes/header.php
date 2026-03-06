<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../helpers/functions.php';

// Enforce Admin Login
require_admin_login();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo defined('PAGE_TITLE') ? PAGE_TITLE . ' - ' : ''; ?>Admin Panel - AyyatulHijab</title>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo BASE_URL; ?>public/uploads/logo_ayathijab.jpg">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS (Must be after Bootstrap) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/admin.css">

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Admin Header Responsive Styles */
        .admin-header {
            position: relative;
        }

        /* Mobile Menu Toggle Button */
        .admin-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: white;
            cursor: pointer;
            padding: 10px;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1001;
        }

        .admin-menu-toggle span {
            display: block;
            width: 25px;
            height: 3px;
            background: white;
            margin: 5px 0;
            transition: 0.3s;
        }

        .admin-menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(7px, 7px);
        }

        .admin-menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .admin-menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .admin-menu-toggle {
                display: block !important;
            }

            .admin-nav {
                position: fixed;
                top: 0;
                right: -100%;
                width: 280px;
                height: 100vh;
                background: #2c3e50;
                flex-direction: column !important;
                align-items: flex-start !important;
                padding: 80px 20px 20px !important;
                gap: 0 !important;
                transition: right 0.4s ease;
                z-index: 999;
                overflow-y: auto;
                box-shadow: -5px 0 15px rgba(0, 0, 0, 0.3);
            }

            .admin-nav.active {
                right: 0 !important;
            }

            .admin-nav a {
                width: 100%;
                padding: 15px 20px !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                font-size: 1rem !important;
            }

            /* Overlay when menu open */
            body.admin-menu-open::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 998;
            }

            body.admin-menu-open {
                overflow: hidden;
            }

            .admin-header h1 {
                font-size: 1.3rem !important;
            }
        }

        @media (max-width: 480px) {
            .admin-header h1 {
                font-size: 1.1rem !important;
            }

            .admin-nav {
                width: 250px;
            }
        }
    </style>
</head>

<body>

    <header class="admin-header">
        <h1>AyyatulHijab Admin</h1>

        <!-- Mobile Menu Toggle Button -->
        <button class="admin-menu-toggle" id="adminMenuToggle" aria-label="Toggle Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="admin-nav" id="adminNav">
            <a href="dashboard.php">Dashboard</a>
            <a href="categories.php">Categories</a>
            <a href="products.php">Products</a>
            <a href="orders.php">Orders</a>
            <a href="coupons.php">Coupons</a>
            <a href="delivery_settings.php">Delivery Settings</a>
            <a href="customers.php">Customers</a>
            <a href="feedback.php">Feedback</a>
            <a href="<?php echo BASE_URL; ?>logout.php" style="color: #ffcccc;">Logout</a>
        </nav>
    </header>

    <script>
        // Mobile Menu Toggle Script
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById('adminMenuToggle');
            const adminNav = document.getElementById('adminNav');

            if (menuToggle && adminNav) {
                menuToggle.addEventListener('click', function () {
                    this.classList.toggle('active');
                    adminNav.classList.toggle('active');
                    document.body.classList.toggle('admin-menu-open');
                });

                // Close menu when clicking outside
                document.addEventListener('click', function (event) {
                    if (!adminNav.contains(event.target) && !menuToggle.contains(event.target)) {
                        adminNav.classList.remove('active');
                        menuToggle.classList.remove('active');
                        document.body.classList.remove('admin-menu-open');
                    }
                });

                // Close menu when clicking a link
                adminNav.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function () {
                        adminNav.classList.remove('active');
                        menuToggle.classList.remove('active');
                        document.body.classList.remove('admin-menu-open');
                    });
                });
            }
        });
    </script>

    <div class="container">

        <div class="container">
            <?php display_flash_message(); ?>