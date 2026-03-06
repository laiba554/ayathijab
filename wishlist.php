<?php
ob_start();// OUTPUT BUFFERING START - YE LINE SABSE PEHLE
define('PAGE_TITLE', 'My Wishlist');
require_once 'includes/header.php';
require_customer_login();

$customer_id = $_SESSION['customer_id'];
$sql = "SELECT w.wishlist_id, p.product_id, p.product_name, p.price, p.image, c.category_name FROM wishlist w JOIN products p ON w.product_id = p.product_id JOIN categories c ON p.category_id = c.category_id WHERE w.customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<style>
/* Responsive Page Container */
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

/* Responsive Header Section */
@media (max-width: 768px) {
    div[style*="margin-bottom: 60px"] {
        margin-bottom: 40px !important;
    }

    h1[style*="font-size: 3rem"] {
        font-size: 2rem !important;
        margin-top: 8px !important;
    }

    span[style*="font-size: 0.9rem"] {
        font-size: 0.8rem !important;
    }
}

@media (max-width: 576px) {
    div[style*="margin-bottom: 60px"] {
        margin-bottom: 30px !important;
    }

    h1[style*="font-size: 3rem"] {
        font-size: 1.75rem !important;
    }

    span[style*="font-size: 0.9rem"] {
        font-size: 0.75rem !important;
    }
}

/* Responsive Empty Wishlist */
@media (max-width: 768px) {
    div[style*="padding: 60px"] {
        padding: 40px 30px !important;
    }

    i[style*="font-size: 3rem"] {
        font-size: 2.5rem !important;
        margin-bottom: 15px !important;
    }

    div[style*="padding: 60px"] p {
        font-size: 1rem !important;
        margin-bottom: 20px !important;
    }
}

@media (max-width: 576px) {
    div[style*="padding: 60px"] {
        padding: 30px 20px !important;
    }

    i[style*="font-size: 3rem"] {
        font-size: 2rem !important;
    }

    div[style*="padding: 60px"] p {
        font-size: 0.95rem !important;
    }
}

/* Responsive Product Grid */
.product-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
}

@media (max-width: 1200px) {
    .product-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }
}

@media (max-width: 968px) {
    .product-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
}

@media (max-width: 576px) {
    .product-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

/* Responsive Product Card */
.product-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.product-image-wrapper {
    position: relative;
    width: 100%;
    height: 300px;
    overflow: hidden;
    background: #f5f5f5;
}

.product-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.3s;
}

.product-card:hover .product-image-wrapper img {
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .product-image-wrapper {
        height: 250px;
    }
}

@media (max-width: 576px) {
    .product-image-wrapper {
        height: 280px;
    }
}

/* Responsive Remove Button */
a[style*="position: absolute"][style*="top: 15px"] {
    top: 15px;
    right: 15px;
    background: white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #e74c3c;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 10;
    position: absolute;
    text-decoration: none;
    transition: all 0.3s;
}

a[style*="position: absolute"][style*="top: 15px"]:hover {
    background: #e74c3c;
    color: white;
    transform: scale(1.1);
}

@media (max-width: 576px) {
    a[style*="position: absolute"][style*="top: 15px"] {
        width: 35px;
        height: 35px;
        top: 10px;
        right: 10px;
        font-size: 14px;
    }
}

/* Responsive Product Info */
.product-info {
    padding: 20px;
}

.product-info h3 {
    margin: 8px 0;
    font-family: var(--font-heading);
    color: var(--brand-espresso);
    font-size: 1rem;
}

.product-info h3 a {
    color: var(--brand-espresso);
    text-decoration: none;
    transition: color 0.3s;
}

.product-info h3 a:hover {
    color: var(--brand-latte);
}

@media (max-width: 768px) {
    .product-info {
        padding: 15px;
    }

    .product-info span {
        font-size: 0.7rem !important;
    }

    .product-info h3 {
        font-size: 0.95rem;
        margin: 6px 0;
    }

    .product-info p {
        font-size: 0.95rem !important;
        margin-bottom: 12px !important;
    }

    .product-info .btn {
        padding: 8px 16px !important;
        font-size: 0.65rem !important;
    }
}

@media (max-width: 576px) {
    .product-info {
        padding: 12px;
    }

    .product-info span {
        font-size: 0.65rem !important;
    }

    .product-info h3 {
        font-size: 0.9rem;
    }

    .product-info p {
        font-size: 0.9rem !important;
    }

    .product-info .btn {
        padding: 10px !important;
        font-size: 0.7rem !important;
    }
}

/* Button Responsive */
.btn {
    display: inline-block;
    text-decoration: none;
    transition: all 0.3s;
}

@media (max-width: 576px) {
    .btn-gold {
        padding: 12px 20px;
        font-size: 0.9rem;
    }
}
</style>

<div style="background: var(--bg-sand); padding: 80px 0; min-height: 80vh;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 60px;">
            <span
                style="color: var(--brand-latte); font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 2px;">My
                Favorites</span>
            <h1 style="font-size: 3rem; margin-top: 10px; color: var(--brand-espresso);">Your Wishlist</h1>
        </div>

        <?php if ($result->num_rows == 0): ?>
            <div
                style="text-align: center; padding: 60px; background: var(--bg-nude); border: 1px solid rgba(154, 123, 100, 0.1);">
                <i class="far fa-heart"
                    style="font-size: 3rem; color: var(--brand-latte); opacity: 0.3; margin-bottom: 20px;"></i>
                <p style="font-size: 1.1rem; color: var(--text-muted); margin-bottom: 30px;">You haven't added anything to
                    your favorites yet.</p>
                <a href="products.php" class="btn btn-gold">Explore Collections</a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-image-wrapper">
                            <a href="product_details.php?id=<?php echo $row['product_id']; ?>">
                                <?php if ($row['image']): ?>
                                    <img src="<?php echo BASE_URL . $row['image']; ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                <?php else: ?>
                                    <div
                                        style="background: #eee; height: 100%; display: flex; align-items: center; justify-content: center; color: #999;">
                                        No Image</div>
                                <?php endif; ?>
                            </a>
                            <a href="wishlist_action.php?action=remove&id=<?php echo $row['product_id']; ?>"
                                style="position: absolute; top: 15px; right: 15px; background: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; color: #e74c3c; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 10;">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                        <div class="product-info">
                            <span
                                style="font-size: 0.75rem; color: var(--brand-latte); text-transform: uppercase; letter-spacing: 1px; font-weight: 600;"><?php echo htmlspecialchars($row['category_name']); ?></span>
                            <h3 style="margin: 8px 0; font-family: var(--font-heading); color: var(--brand-espresso);">
                                <a href="product_details.php?id=<?php echo $row['product_id']; ?>"><?php echo htmlspecialchars($row['product_name']); ?></a>
                            </h3>
                            <p style="color: var(--brand-latte); font-weight: 700; margin-bottom: 15px;">
                                <?php echo formatPricePKR($row['price']); ?>
                            </p>
                            <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="btn"
                                style="padding: 10px 20px; font-size: 0.7rem; width: 100%;">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; 
ob_end_flush(); // OUTPUT BUFFERING END
?>