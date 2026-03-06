<?php
define('PAGE_TITLE', 'Home');
require_once 'includes/header.php';

// Fetch Recent Products - Use status = 1 for active products
$sql = "SELECT * FROM products WHERE status = 1 ORDER BY created_at DESC LIMIT 4";
$result = $conn->query($sql);

// Fetch Categories dynamically
$categories_sql = "SELECT category_id, category_name FROM categories ORDER BY category_id ASC";
$categories_result = $conn->query($categories_sql);
$categories = [];
if ($categories_result) {
    while ($cat = $categories_result->fetch_assoc()) {
        $categories[] = $cat;
    }
}

// Find Hijab and Abaya category IDs
$hijab_id = 6;
$abaya_id = 5;

foreach ($categories as $cat) {
    if (stripos($cat['category_name'], 'hijab') !== false) {
        $hijab_id = $cat['category_id'];
    }
    if (stripos($cat['category_name'], 'abaya') !== false || stripos($cat['category_name'], 'abayas') !== false) {
        $abaya_id = $cat['category_id'];
    }
}
?>

<style>
/* Responsive Hero Section */
@media (max-width: 768px) {
    .hero {
        height: 60vh !important;
        min-height: 400px !important;
    }

    .hero h1 {
        font-size: 2rem !important;
    }

    .hero p {
        font-size: 1rem !important;
        padding: 0 20px;
    }

    .hero .btn {
        padding: 10px 25px;
        font-size: 14px;
    }
}

@media (max-width: 576px) {
    .hero {
        height: 50vh !important;
        min-height: 350px !important;
    }

    .hero h1 {
        font-size: 1.5rem !important;
        margin-bottom: 20px !important;
    }

    .hero p {
        font-size: 0.9rem !important;
        margin-bottom: 30px !important;
    }
}

/* Responsive Categories Section */
@media (max-width: 768px) {
    .container[style*="padding: 80px 0"] {
        padding: 50px 0 !important;
    }

    .container h2 {
        font-size: 1.8rem !important;
        margin-bottom: 30px !important;
    }

    .category-placeholder {
        height: 250px !important;
    }

    .category-content {
        padding: 15px !important;
    }

    .category-content h3 {
        font-size: 1.2rem;
    }

    .category-link {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .container[style*="padding: 80px 0"] {
        padding: 40px 0 !important;
    }

    .container h2 {
        font-size: 1.5rem !important;
        margin-bottom: 25px !important;
    }

    .category-placeholder {
        height: 200px !important;
    }

    .category-content {
        padding: 12px !important;
    }

    .category-content h3 {
        font-size: 1rem;
        margin-bottom: 5px;
    }

    .category-link {
        font-size: 0.85rem;
    }
}

/* Responsive Latest Arrivals Section */
@media (max-width: 768px) {
    section[style*="padding: 80px 0"] {
        padding: 50px 0 !important;
    }

    .product-card {
        margin-bottom: 20px;
    }

    .product-title {
        font-size: 1rem;
    }

    .product-price {
        font-size: 1.1rem;
    }

    .btn-gold {
        padding: 8px 15px !important;
        font-size: 0.7rem !important;
    }
}

@media (max-width: 576px) {
    section[style*="padding: 80px 0"] {
        padding: 40px 0 !important;
    }

    .product-card {
        margin-bottom: 15px;
    }

    .product-info {
        padding: 12px !important;
    }

    .product-title {
        font-size: 0.95rem;
    }

    .product-price {
        font-size: 1rem;
    }

    .btn-gold {
        padding: 6px 12px !important;
        font-size: 0.65rem !important;
        margin-top: 8px !important;
    }
}

/* Product Grid Responsive */
@media (max-width: 576px) {
    .row-cols-1 > * {
        width: 100%;
    }
}

@media (min-width: 576px) and (max-width: 768px) {
    .row-cols-sm-2 > * {
        width: 50%;
    }
}

/* Responsive Images - Category Cards */
.category-placeholder img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.5s;
}

@media (max-width: 768px) {
    .category-placeholder img {
        object-fit: cover;
        height: 100%;
    }
}

@media (max-width: 576px) {
    .category-placeholder img {
        object-fit: cover;
        height: 100%;
    }
}

/* Responsive Images - Product Cards */
.product-image-wrapper {
    display: block;
    width: 100%;
    height: 300px;
    overflow: hidden;
    position: relative;
}

.product-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.3s ease;
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

/* Hero Background Image Responsive */
.hero > div:first-child {
    background-size: cover !important;
    background-position: center !important;
}

@media (max-width: 768px) {
    .hero > div:first-child {
        background-size: cover !important;
        background-position: center center !important;
    }
}

@media (max-width: 576px) {
    .hero > div:first-child {
        background-size: cover !important;
        background-position: center center !important;
    }
}

.category-card {
    display: block;
    position: relative;
    overflow: hidden;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    color: white;
    text-decoration: none;
    transition: transform 0.3s;
}

.category-card:hover {
    transform: translateY(-5px);
}

.category-placeholder {
    height: 300px;
    background: #ccc;
}

.category-content {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 20px;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
}

.category-card:hover img {
    transform: scale(1.1);
}

.product-card {
    background: white;
    border: 1px solid #eee;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.product-info {
    padding: 20px;
}

.product-title {
    font-size: 1.1rem;
    margin-bottom: 10px;
    color: var(--brand-espresso);
}

.product-price {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--brand-latte);
    margin-bottom: 0;
}
</style>

<!-- Luxury Hero Section -->
<section class="hero position-relative d-flex align-items-center justify-content-center text-center text-light overflow-hidden"
    style="height: 80vh; min-height: 500px;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background: url('public/images/theme/hero_bg.jpg') no-repeat center center/cover;">
    </div>
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(74, 50, 38, 0.7);">
    </div>

    <div class="container position-relative z-1">
        <h1 class="display-3 fw-bold mb-4">Elegance in Modesty</h1>
        <p class="fs-5 mb-5 mx-auto" style="max-width: 600px;">
            Explore our beautifully crafted hijabs, abayas, and enchanting itter, designed to reflect your modest style with gentle elegance and lasting fragrance.</p>
        <a href="products.php" class="btn">Shop Collection</a>
    </div>
</section>

<!-- Categories -->
<section class="container" style="padding: 80px 0;">
    <h2 style="text-align: center; margin-bottom: 50px; color: var(--brand-espresso);">Shop by Category</h2>
    <div class="row g-4 justify-content-center">
        <!-- Hijabs -->
        <div class="col-lg-4 col-md-4 col-sm-6">
            <a href="products.php?category_id=<?php echo $hijab_id; ?>" class="category-card">
                <div class="category-placeholder">
                    <img src="public/images/theme/category_hijab_v2.png" alt="Hijabs"
                        onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                </div>
                <div class="category-content">
                    <h3>Hijabs</h3>
                    <span class="category-link">View Collection</span>
                </div>
            </a>
        </div>

        <!-- Abayas -->
        <div class="col-lg-4 col-md-4 col-sm-6">
            <a href="products.php?category_id=<?php echo $abaya_id; ?>" class="category-card">
                <div class="category-placeholder">
                    <img src="public/images/theme/category_abaya_v2.png" alt="Abayas"
                        onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                </div>
                <div class="category-content">
                    <h3>Abayas</h3>
                    <span class="category-link">View Collection</span>
                </div>
            </a>
        </div>

        <!-- Explore All -->
        <div class="col-lg-4 col-md-4 col-sm-6">
            <a href="products.php" class="category-card">
                <div class="category-placeholder">
                    <img src="public/images/theme/category_explore_v2.png" alt="Explore All"
                        onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                </div>
                <div class="category-content">
                    <h3>Explore All</h3>
                    <span class="category-link">View Collection</span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Latest Arrivals -->
<section style="background-color: var(--bg-light-secondary); padding: 80px 0;">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 50px; color: var(--brand-espresso);">Latest Arrivals</h2>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col">
                        <div class="product-card h-100">
                            <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="product-image-wrapper">
                                <?php if ($row['image']): ?>
                                    <img src="<?php echo BASE_URL . $row['image']; ?>"
                                        alt="<?php echo htmlspecialchars($row['product_name']); ?>"
                                        onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #f0f0f0; color: #888;">
                                        No Image
                                    </div>
                                <?php endif; ?>
                            </a>
                            <div class="product-info">
                                <h3 class="product-title"><?php echo htmlspecialchars($row['product_name']); ?></h3>
                                <p class="product-price"><?php echo formatPricePKR($row['price']); ?></p>
                                <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="btn-gold"
                                    style="padding: 8px 20px; font-size: 0.75rem; display: inline-block; margin-top: 10px; text-decoration: none; border-radius: 5px;">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px;">
                        <i class="fas fa-box-open" style="font-size: 4rem; color: var(--brand-latte); opacity: 0.3; margin-bottom: 20px;"></i>
                        <h3 style="color: var(--brand-espresso); margin-bottom: 10px;">No Products Available</h3>
                        <p style="color: var(--text-muted); margin-bottom: 20px;">We're currently updating our inventory. Please check back soon!</p>
                        <a href="products.php" class="btn btn-gold">View All Products</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>