<?php
session_start();
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../helpers/functions.php';

if (is_customer_logged_in()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($name)) {
        $error = 'Please enter your name.';
    } elseif (strlen($name) < 3) {
        $error = 'Name must be at least 3 characters.';
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $error = 'Name should only contain letters and spaces.';
    } elseif (empty($email)) {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address (e.g., name@example.com).';
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $error = 'Please enter a valid email address (e.g., name@example.com).';
    } elseif (empty($password)) {
        $error = 'Please enter a password.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $name = sanitize_input($name);
        $email = sanitize_input($email);

        $check = $conn->prepare("SELECT customer_id FROM customers WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "This email address is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO customers (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            if ($stmt->execute()) {
                $_SESSION['customer_id'] = $stmt->insert_id;
                $_SESSION['customer_name'] = $name;
                set_flash_message('success', 'Registration successful! Welcome to AyyatulHijab.');
                redirect('index.php');
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

define('PAGE_TITLE', 'Create Account');
require_once '../includes/header.php';
?>

<div style="background: var(--bg-sand); min-height: 80vh; display: flex; align-items: center;" class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="p-4 p-md-5" style="background: var(--bg-nude); border: 1px solid rgba(154, 123, 100, 0.1); text-align: center;">
                    <span style="color: var(--brand-latte); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 2px;">Join Our Collection</span>
                    <h2 style="font-size: 2.5rem; margin: 15px 0 40px; border-bottom: 2px solid var(--brand-latte); padding-bottom: 15px; display: inline-block; color: var(--brand-espresso);">
                        Register</h2>

                    <?php if ($error): ?>
                        <div style="padding: 10px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 20px;">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" style="text-align: left;">
                        <div style="margin-bottom: 25px;">
                            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 10px; color: var(--brand-espresso);">Full Name *</label>
                            <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required minlength="3" pattern="[a-zA-Z\s]+" title="Only letters and spaces allowed" style="width: 100%; padding: 15px; border: 1px solid var(--brand-latte); background: transparent; font-family: var(--font-body); color: var(--text-main);">
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 10px; color: var(--brand-espresso);">Email Address *</label>
                            <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" title="Please enter a valid email (e.g., name@example.com)" placeholder="name@example.com" style="width: 100%; padding: 15px; border: 1px solid var(--brand-latte); background: transparent; font-family: var(--font-body); color: var(--text-main);">
                        </div>

                        <div style="margin-bottom: 30px;">
                            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 10px; color: var(--brand-espresso);">Password *</label>
                            <input type="password" name="password" required minlength="6" style="width: 100%; padding: 15px; border: 1px solid var(--brand-latte); background: transparent; font-family: var(--font-body); color: var(--text-main);">
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 8px;">Minimum 6 characters</p>
                        </div>

                        <button type="submit" class="btn btn-gold" style="width: 100%; padding: 18px 0; letter-spacing: 3px;">Join Collection</button>
                    </form>

                    <p style="margin-top: 40px; font-size: 0.9rem; color: var(--text-muted);">
                        Already have an account? <a href="login.php" style="color: var(--brand-latte); font-weight: 700; border-bottom: 1px solid var(--brand-latte);">Sign In</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>