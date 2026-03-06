<?php
session_start();
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../helpers/functions.php';
require_customer_login();

$customer_id = $_SESSION['customer_id'];
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $work_phone = trim($_POST['work_phone'] ?? '');
    $cell_phone = trim($_POST['cell_phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');

    if (empty($name)) {
        $error = 'Please enter your name.';
    } elseif (strlen($name) < 2) {
        $error = 'Name must be at least 2 characters.';
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $error = 'Name should only contain letters and spaces.';
    } elseif (empty($email)) {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($cell_phone)) {
        $error = 'Please enter your cell phone number.';
    } elseif (!preg_match("/^[0-9]{11}$/", $cell_phone)) {
        $error = 'Cell phone must be exactly 11 digits.';
    } elseif (!empty($work_phone) && !preg_match("/^[0-9]{11}$/", $work_phone)) {
        $error = 'Work phone must be exactly 11 digits.';
    } elseif (empty($address)) {
        $error = 'Please enter your address.';
    } elseif (strlen($address) < 10) {
        $error = 'Address must be at least 10 characters.';
    } elseif (!empty($date_of_birth) && strtotime($date_of_birth) > time()) {
        $error = 'Date of birth cannot be in the future.';
    } elseif (strlen($remarks) > 500) {
        $error = 'Remarks cannot exceed 500 characters.';
    } else {
        $check_stmt = $conn->prepare("SELECT customer_id FROM customers WHERE email = ? AND customer_id != ?");
        $check_stmt->bind_param("si", $email, $customer_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = 'This email is already registered to another account.';
        } else {
            $name = sanitize_input($name);
            $email = sanitize_input($email);
            $work_phone = sanitize_input($work_phone);
            $cell_phone = sanitize_input($cell_phone);
            $address = sanitize_input($address);
            $date_of_birth = sanitize_input($date_of_birth);
            $remarks = sanitize_input($remarks);

            $stmt = $conn->prepare("UPDATE customers SET name = ?, email = ?, work_phone = ?, cell_phone = ?, address = ?, date_of_birth = ?, remarks = ? WHERE customer_id = ?");
            $stmt->bind_param("sssssssi", $name, $email, $work_phone, $cell_phone, $address, $date_of_birth, $remarks, $customer_id);

            if ($stmt->execute()) {
                $success = true;
                $_SESSION['customer_name'] = $name;
            } else {
                $error = 'Failed to update profile. Please try again.';
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

$sql = "SELECT * FROM customers WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

if (!$customer) {
    redirect('../logout.php');
}

define('PAGE_TITLE', 'Profile Settings');
require_once '../includes/header.php';
?>

<style>
@media (max-width: 768px) {
    div[style*="padding: 80px 0"] {
        padding: 50px 0 !important;
    }
}

@media (max-width: 576px) {
    div[style*="padding: 80px 0"] {
        padding: 40px 0 !important;
    }
}

.sidebar-sticky {
    position: sticky;
    top: 120px;
}

@media (max-width: 991px) {
    .sidebar-sticky {
        position: relative !important;
        top: 0 !important;
        margin-bottom: 30px;
    }
}

@media (max-width: 768px) {
    aside[style*="padding: 40px"] {
        padding: 25px !important;
    }
    aside div[style*="margin-bottom: 40px"] {
        margin-bottom: 25px !important;
        padding-bottom: 20px !important;
    }
    aside div[style*="width: 80px"] {
        width: 60px !important;
        height: 60px !important;
        font-size: 1.5rem !important;
        margin-bottom: 12px !important;
    }
    aside h3 {
        font-size: 1.1rem !important;
    }
    aside p {
        font-size: 0.75rem !important;
    }
    aside nav {
        gap: 8px !important;
    }
    aside nav a {
        padding: 10px 0 !important;
        font-size: 0.85rem !important;
        gap: 12px !important;
        text-decoration: none;
    }
    aside nav a[style*="margin-top: 20px"] {
        margin-top: 15px !important;
        padding-top: 15px !important;
    }
}

@media (max-width: 576px) {
    aside[style*="padding: 40px"] {
        padding: 20px !important;
    }
    aside nav a {
        font-size: 0.8rem !important;
    }
}

@media (max-width: 768px) {
    main[style*="padding: 50px"] {
        padding: 30px !important;
    }
    h2[style*="font-size: 2.2rem"] {
        font-size: 1.8rem !important;
        margin-bottom: 30px !important;
        padding-bottom: 15px !important;
    }
}

@media (max-width: 576px) {
    main[style*="padding: 50px"] {
        padding: 25px !important;
    }
    h2[style*="font-size: 2.2rem"] {
        font-size: 1.5rem !important;
        margin-bottom: 25px !important;
    }
}

@media (max-width: 576px) {
    .alert {
        font-size: 0.9rem;
        padding: 12px;
    }
}

@media (max-width: 768px) {
    form > div[style*="display: flex"] {
        gap: 15px !important;
    }
    form > div > div[style*="width: 48%"] {
        width: 100% !important;
        min-width: 100% !important;
    }
    label[style*="font-size: 0.8rem"] {
        font-size: 0.75rem !important;
        margin-bottom: 8px !important;
    }
    input[style*="padding: 15px"],
    textarea[style*="padding: 15px"] {
        padding: 12px !important;
        font-size: 14px;
    }
    textarea[style*="height: 100px"] {
        height: 90px !important;
    }
}

@media (max-width: 576px) {
    form > div[style*="display: flex"] {
        gap: 12px !important;
    }
    label[style*="font-size: 0.8rem"] {
        font-size: 0.7rem !important;
    }
    input[style*="padding: 15px"],
    textarea[style*="padding: 15px"] {
        padding: 10px !important;
        font-size: 13px;
    }
    textarea[style*="height: 100px"] {
        height: 80px !important;
    }
}

@media (max-width: 768px) {
    div[style*="text-align: right"] {
        text-align: center !important;
    }
    button[style*="padding: 15px 40px"] {
        padding: 14px 35px !important;
        font-size: 0.95rem !important;
    }
}

@media (max-width: 576px) {
    button[style*="padding: 15px 40px"] {
        width: 100% !important;
        padding: 12px !important;
        font-size: 0.9rem !important;
    }
}

@media (max-width: 991px) {
    .row.g-5 {
        gap: 2rem !important;
    }
}

@media (max-width: 576px) {
    .row.g-5 {
        gap: 1.5rem !important;
    }
}
</style>

<div style="background: var(--bg-cream); padding: 80px 0; min-height: 80vh;">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-3">
                <aside class="sidebar-sticky" style="background: white; padding: 40px; border: 1px solid #eee; z-index: 1;">
                    <div style="text-align: center; margin-bottom: 40px; border-bottom: 1px solid #eee; padding-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: var(--secondary-tan); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 15px; font-family: var(--font-heading);">
                            <?php echo substr($_SESSION['customer_name'], 0, 1); ?>
                        </div>
                        <h3 style="margin: 0; font-size: 1.2rem;">
                            <?php echo htmlspecialchars($_SESSION['customer_name']); ?>
                        </h3>
                        <p style="margin: 5px 0 0; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">Customer</p>
                    </div>

                    <nav style="display: flex; flex-direction: column; gap: 10px;">
                        <a href="dashboard.php" style="display: flex; align-items: center; gap: 15px; padding: 12px 0; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; text-decoration: none;">
                            <i class="fas fa-shopping-bag"></i> My Orders
                        </a>
                        <a href="../wishlist.php" style="display: flex; align-items: center; gap: 15px; padding: 12px 0; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; text-decoration: none;">
                            <i class="far fa-heart"></i> Wishlist
                        </a>
                        <a href="profile.php" style="display: flex; align-items: center; gap: 15px; padding: 12px 0; color: var(--primary-gold); font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; text-decoration: none;">
                            <i class="far fa-user"></i> Profile Settings
                        </a>
                        <a href="../logout.php" style="display: flex; align-items: center; gap: 15px; padding: 12px 0; color: #e74c3c; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px; text-decoration: none;">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </nav>
                </aside>
            </div>

            <div class="col-lg-9">
                <main style="background: white; padding: 50px; border: 1px solid #eee;">
                    <h2 style="font-size: 2.2rem; margin-bottom: 40px; border-bottom: 2px solid var(--primary-gold); padding-bottom: 20px;">
                        Edit Profile</h2>

                    <?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            Profile updated successfully!
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: space-between;">
                            <div style="width: 48%; min-width: 300px;">
                                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 10px; color: var(--text-main);">Full Name *</label>
                                <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : htmlspecialchars($customer['name']); ?>" required minlength="2" pattern="[a-zA-Z\s]+" title="Only letters and spaces allowed" style="width: 100%; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">
                            </div>

                            <div style="width: 48%; min-width: 300px;">
                                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 10px; color: var(--text-main);">Email Address *</label>
                                <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($customer['email']); ?>" required style="width: 100%; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">
                            </div>

                            <div style="width: 48%; min-width: 300px;">
                                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 10px; color: var(--text-main);">Phone (Work)</label>
                                <input type="tel" name="work_phone" value="<?php echo isset($_POST['work_phone']) ? htmlspecialchars($_POST['work_phone']) : htmlspecialchars($customer['work_phone']); ?>" pattern="[0-9]{11}" maxlength="11" title="11 digit phone number" placeholder="03001234567" style="width: 100%; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">
                            </div>

                            <div style="width: 48%; min-width: 300px;">
                                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 10px; color: var(--text-main);">Phone (Cell) *</label>
                                <input type="tel" name="cell_phone" value="<?php echo isset($_POST['cell_phone']) ? htmlspecialchars($_POST['cell_phone']) : htmlspecialchars($customer['cell_phone']); ?>" required pattern="[0-9]{11}" maxlength="11" title="11 digit phone number" placeholder="03001234567" style="width: 100%; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">
                            </div>

                            <div style="width: 100%;">
                                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 10px; color: var(--text-main);">Shipping Address *</label>
                                <textarea name="address" required minlength="5" style="width: 100%; padding: 15px; border: 1px solid #ddd; background: #f9f9f9; height: 100px;"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : htmlspecialchars($customer['address']); ?></textarea>
                                <small style="color: #999; font-size: 0.85rem;">Minimum 10 characters</small>
                            </div>

                            <div style="width: 48%; min-width: 300px;">
                                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 10px; color: var(--text-main);">Date of Birth</label>
                                <input type="date" name="date_of_birth" value="<?php echo htmlspecialchars($customer['date_of_birth']); ?>" max="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">
                            </div>

                            <div style="width: 100%;">
                                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 10px; color: var(--text-main);">Remarks (Optional)</label>
                                <textarea name="remarks" maxlength="500" style="width: 100%; padding: 15px; border: 1px solid #ddd; background: #f9f9f9; height: 100px;"><?php echo isset($_POST['remarks']) ? htmlspecialchars($_POST['remarks']) : htmlspecialchars($customer['remarks']); ?></textarea>
                                <small style="color: #999; font-size: 0.85rem;">Maximum 500 characters</small>
                            </div>

                            <div style="width: 100%; text-align: right;">
                                <button type="submit" class="btn btn-gold" style="padding: 15px 40px; font-size: 1rem;">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>