<?php
define('PAGE_TITLE', 'Share Your Feedback');
require_once 'includes/header.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validations
    if (empty($name)) {
        $error = 'Please enter your name.';
    } elseif (strlen($name) < 2) {
        $error = 'Name must be at least 2 characters.';
    } elseif (strlen($name) > 100) {
        $error = 'Name is too long (max 100 characters).';
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $error = 'Name should only contain letters and spaces.';
    } elseif (empty($message)) {
        $error = 'Please write your feedback.';
    } elseif (strlen($message) < 10) {
        $error = 'Feedback must be at least 10 characters.';
    } elseif (strlen($message) > 1000) {
        $error = 'Feedback is too long (max 1000 characters).';
    } else {
        $name = sanitize_input($name);
        $message = sanitize_input($message);

        $stmt = $conn->prepare("INSERT INTO feedback (name, message) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $message);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = 'Failed to submit feedback. Please try again.';
        }
        $stmt->close();
    }
}
?>

<div style="background: var(--bg-nude); padding: 80px 0; border-bottom: 1px solid rgba(154, 123, 100, 0.1);">
    <div class="container" style="text-align: center;">
        <span style="color: var(--brand-latte); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 2px;">Your Voice Matters</span>
        <h1 style="font-size: 3.5rem; margin-top: 10px; color: var(--brand-espresso);">Feedback</h1>
    </div>
</div>

<div class="container" style="padding: 100px 0; max-width: 600px;">
    <?php if ($success): ?>
        <div style="background: #fdfaf3; border: 1px solid var(--accent-gold); padding: 30px; border-radius: var(--radius-md); text-align: center; margin-bottom: 40px;">
            <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--accent-gold); margin-bottom: 20px;"></i>
            <h2 style="color: var(--brand-espresso); margin-bottom: 10px;">Thank You!</h2>
            <p style="color: var(--text-muted);">Your feedback has been received. We value your input and appreciate you taking the time to share it with us.</p>
            <a href="index.php" class="btn" style="margin-top: 25px; display: inline-block;">Return Home</a>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div style="background: #fee; border: 1px solid #c33; padding: 15px; border-radius: var(--radius-md); margin-bottom: 20px; color: #c33;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <div style="background: white; padding: 50px; border-radius: var(--radius-lg); box-shadow: 0 15px 40px rgba(0,0,0,0.05);">
            <form action="" method="POST">
                <div class="form-group" style="margin-bottom: 30px;">
                    <label style="display: block; font-size: 0.9rem; font-weight: 600; color: var(--brand-espresso); margin-bottom: 10px;">Full Name *</label>
                    <input type="text" name="name" placeholder="Enter your name" required minlength="5" maxlength="100" pattern="[a-zA-Z\s]+" title="Only letters and spaces allowed" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" style="width: 100%; padding: 15px; border: 1px solid #e1e1e1; border-radius: var(--radius-md); font-family: inherit;">
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label style="display: block; font-size: 0.9rem; font-weight: 600; color: var(--brand-espresso); margin-bottom: 10px;">Your Feedback *</label>
                    <textarea name="message" rows="6" placeholder="Write your feedback here..." required minlength="10" maxlength="1000" style="width: 100%; padding: 15px; border: 1px solid #e1e1e1; border-radius: var(--radius-md); font-family: inherit; resize: vertical;"><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    <small style="display: block; margin-top: 5px; color: #999;">Minimum 10 characters</small>
                </div>

                <button type="submit" class="btn">SUBMIT FEEDBACK</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>