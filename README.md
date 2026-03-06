# AyyatulHijab E-Commerce System

Welcome to the AyyatulHijab E-Commerce System! This is a comprehensive e-commerce platform built with PHP and MySQL, designed specifically for a modest fashion brand.

## Features

- **Customer System**: Registration, login, profile management, and order history.
- **Product Catalog**: Browse categories, view product variants, and check stock statuses.
- **Shopping Cart & Checkout**: Add items to cart, apply coupons, and process orders.
- **Admin Dashboard**: Comprehensive admin panel to manage products, categories, orders, customers, and delivery settings.
- **Responsive Design**: Built with Bootstrap 5 for a mobile-friendly user experience across both customer and admin interfaces.

## Technology Stack

- **Backend**: PHP 8.x
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla), Bootstrap 5

## Project Structure

```text
ayathijab/
├── admin/               # Admin panel dashboard and management scripts
├── config/              # Database connection and base configuration
│   ├── config.php       # Environment constants (DB credentials, base URL)
│   ├── db.php           # MySQL connection setup
│   └── schema.sql       # Complete database layout and tables
├── customer/            # Customer profile, dashboard, and login scripts
├── helpers/             # Utility functions
├── includes/            # Reusable layout parts (header, footer, nav)
├── public/              # Public assets (CSS, JS, Uploaded images)
└── index.php            # Main homepage
```

## Setup & Installation

Follow these steps to set up the project locally for development or testing.

### Prerequisites

1. Install a local server environment like [XAMPP](https://www.apachefriends.org/index.html) or [WAMP](https://www.wampserver.com/en/).
2. Ensure Apache and MySQL services are running.
3. Make sure PHP is configured correctly on your system.

### Step 1: Clone or Copy the Repository

Place the `ayathijab` project folder inside your local web server's root directory:
- For **XAMPP**: `C:\xampp\htdocs\ayathijab`
- For **WAMP**: `C:\wamp64\www\ayathijab`

### Step 2: Configure the Database

1. Open your browser and go to `http://localhost/phpmyadmin/`.
2. Create a new database named **`ayyatulhijab_ecommerce`**.
3. Select the newly created database and click on the **Import** tab.
4. Choose the file located at `c:\xampp\htdocs\ayathijab\config\schema.sql`.
5. Click **Go** / **Import** to execute the SQL script. This will create all the necessary tables (admins, customers, products, orders, etc.).

> **Note**: If you have seed data or default admin credentials in other `.sql` files inside the `config/` directory (like `safe_create_tables.sql` or `delivery_charges_setup.sql`), you can import them sequentially after `schema.sql`.

### Step 3: Verify Configuration

The database configuration is managed in `config/config.php`. Verify that the settings match your local environment:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Change if your local setup uses a different username
define('DB_PASS', '');           // Change if your local setup has a password
define('DB_NAME', 'ayyatulhijab_ecommerce');

// App Configuration
define('BASE_URL', 'http://localhost/ayathijab/'); // Adjust if your folder name differs
```

### Step 4: Run the Application

1. Open your web browser.
2. Go to `http://localhost/ayathijab/` to view the customer-facing store.
3. To access the admin panel, go to `http://localhost/ayathijab/admin/login.php`.

## Note to Developers

- **Session Management**: Session starts implicitly based on `session_status()` checks in header components.
- **Security Check**: For pages restricted to logged-in users or admins, ensure functions like `require_customer_login()` or `require_admin_login()` are invoked at the very top of the script.
- **Paths**: When adding assets or links, rely on the `BASE_URL` constant defined in `config/config.php` to prevent broken links across local and production instances.
