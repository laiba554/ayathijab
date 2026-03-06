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
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT customer_id, name, password FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['customer_id'] = $row['customer_id'];
            $_SESSION['customer_name'] = $row['name'];
            redirect('index.php');
        } else {
            $error = "Invalid credentials provided.";
        }
    } else {
        $error = "User not found.";
    }
}

define('PAGE_TITLE', 'Account Login');
require_once '../includes/header.php';
?>

<div style="background: var(--bg-sand); min-height: 80vh; display: flex; align-items: center;" class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="p-4 p-md-5"
                    style="background: var(--bg-nude); border: 1px solid rgba(154, 123, 100, 0.1); text-align: center;">
                    <span
                        style="color: var(--brand-latte); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 2px;">Welcome
                        Back</span>
                    <h2
                        style="font-size: 2.5rem; margin: 15px 0 40px; border-bottom: 2px solid var(--brand-latte); padding-bottom: 15px; display: inline-block; color: var(--brand-espresso);">
                        Login</h2>

                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" style="text-align: left;">
                        <div style="margin-bottom: 25px;">
                            <label
                                style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 10px; color: var(--brand-espresso);">Email
                                Address</label>
                            <input type="email" name="email" required
                                style="width: 100%; padding: 15px; border: 1px solid var(--brand-latte); background: transparent; font-family: var(--font-body); color: var(--text-main);">
                        </div>
                        <div style="margin-bottom: 30px;">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <label
                                    style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; color: var(--brand-espresso);">Password</label>
                                <a href="#"
                                    style="font-size: 0.75rem; color: var(--brand-latte); font-weight: 600;">Forgot?</a>
                            </div>
                            <input type="password" name="password" required
                                style="width: 100%; padding: 15px; border: 1px solid var(--brand-latte); background: transparent; font-family: var(--font-body); color: var(--text-main);">
                        </div>

                        <button type="submit" class="btn btn-gold"
                            style="width: 100%; padding: 18px 0; letter-spacing: 3px;">Sign In</button>
                    </form>

                    <p style="margin-top: 40px; font-size: 0.9rem; color: var(--text-muted);">
                        Don't have an account? <a href="register.php"
                            style="color: var(--brand-latte); font-weight: 700; border-bottom: 1px solid var(--brand-latte);">Register
                            Now</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>