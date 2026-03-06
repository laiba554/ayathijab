<?php
define('PAGE_TITLE', 'Shop Collections');
require_once 'includes/header.php';

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
$sort_by = isset($_GET['sort_by']) ? sanitize_input($_GET['sort_by']) : 'latest';
$category_name = 'All Products';

// Build Query - Use status = 1 for active products
$sql = "SELECT p.*, c.category_name FROM products p JOIN categories c ON p.category_id = c.category_id WHERE p.status = 1";

if ($category_id) {
    $sql .= " AND p.category_id = " . intval($category_id);
    $cat_res = $conn->query("SELECT category_name FROM categories WHERE category_id = " . intval($category_id));
    if ($cat_res && $cat_row = $cat_res->fetch_assoc()) {
        $category_name = $cat_row['category_name'];
    }
}

// Add sorting
switch ($sort_by) {
    case 'price_low':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'latest':
    default:
        $sql .= " ORDER BY p.created_at DESC";
        break;
}

// Execute query
$result = $conn->query($sql);

// Fetch categories for filter
$cats = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
?>

<style>
/* Responsive Header */
@media (max-width: 768px) {
    div[style*="padding: 60px 0"] {
        padding: 40px 0 !important;
    }
    h1[style*="font-size: 3rem"] {
        font-size: 2rem !important;
    }
}

@media (max-width: 576px) {
    div[style*="padding: 60px 0"] {
        padding: 30px 0 !important;
    }
    h1[style*="font-size: 3rem"] {
        font-size: 1.75rem !important;
    }
}

/* Responsive Filters */
@media (max-width: 991px) {
    #filterSidebar {
        margin-bottom: 20px;
    }
}

/* Product Cards */
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

.product-image-wrapper {
    display: block;
    width: 100%;
    aspect-ratio: 3/4;
    overflow: hidden;
    background: var(--bg-nude);
}

.product-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.product-card:hover .product-image-wrapper img {
    transform: scale(1.05);
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
    margin-bottom: 15px;
}

@media (max-width: 576px) {
    .product-info {
        padding: 15px;
    }
    .product-title {
        font-size: 1rem;
    }
    .product-price {
        font-size: 1.1rem;
    }
}
</style>

<!-- Shop Header -->
<div style="background: var(--bg-nude); padding: 60px 0; border-bottom: 1px solid rgba(154, 123, 100, 0.1); margin-bottom: 40px;">
    <div class="container" style="text-align: center;">
        <span style="color: var(--brand-latte); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 2px;">
            Shop Collection
        </span>
        <h1 style="font-size: 3rem; margin-top: 10px; color: var(--brand-espresso);">
            <?php echo htmlspecialchars($category_name); ?>
        </h1>
    </div>
</div>

<div class="container" style="margin-top: 40px; margin-bottom: 80px;">

    <!-- Mobile Toggle for Filters -->
    <div class="d-lg-none mb-3">
        <button class="btn btn-gold w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filterSidebar"
            aria-expanded="false" aria-controls="filterSidebar">
            <i class="fas fa-filter me-2"></i> Show Filters
        </button>
    </div>

    <div class="row g-4">
        <!-- Filters Sidebar -->
        <aside class="col-lg-3 collapse d-lg-block" id="filterSidebar">
            <div style="background: #fff; padding: 20px; border: 1px solid #eee; border-radius: 8px;">
                <h3 style="font-size: 1.2rem; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
                    Filters
                </h3>

                <!-- Categories -->
                <div class="mb-4">
                    <h4 style="font-size: 1rem; margin-bottom: 15px; font-weight: 600;">Category</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 10px;">
                            <a href="products.php<?php echo $sort_by != 'latest' ? '?sort_by=' . urlencode($sort_by) : ''; ?>"
                                style="text-decoration: none; color: <?php echo !$category_id ? 'var(--accent-gold)' : '#666'; ?>; font-weight: <?php echo !$category_id ? 'bold' : 'normal'; ?>;">
                                All Products
                            </a>
                        </li>
                        <?php if ($cats && $cats->num_rows > 0): ?>
                            <?php while ($cat = $cats->fetch_assoc()): ?>
                                <li style="margin-bottom: 10px;">
                                    <a href="products.php?category_id=<?php echo $cat['category_id']; ?><?php echo $sort_by != 'latest' ? '&sort_by=' . urlencode($sort_by) : ''; ?>"
                                        style="text-decoration: none; color: <?php echo $category_id == $cat['category_id'] ? 'var(--accent-gold)' : '#666'; ?>; font-weight: <?php echo $category_id == $cat['category_id'] ? 'bold' : 'normal'; ?>;">
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Product Grid -->
        <main class="col-lg-9">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
                <h2 style="margin: 0;">
                    <?php echo htmlspecialchars($category_name); ?>
                </h2>

                <!-- Sorting -->
                <div style="display: flex; align-items: center;">
                    <label for="sortSelect" style="margin-right: 10px; color: #666; font-size: 0.9em;">Sort by:</label>
                    <select id="sortSelect" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                        onchange="handleSort()">
                        <option value="latest" <?php echo $sort_by == 'latest' ? 'selected' : ''; ?>>Latest Arrivals</option>
                        <option value="price_low" <?php echo $sort_by == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo $sort_by == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    </select>
                </div>
            </div>

            <script>
                function handleSort() {
                    const sortValue = document.getElementById('sortSelect').value;
                    const urlParams = new URLSearchParams(window.location.search);
                    urlParams.set('sort_by', sortValue);
                    window.location.href = 'products.php?' + urlParams.toString();
                }
            </script>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($product = $result->fetch_assoc()): ?>
                        <div class="col">
                            <div class="product-card h-100">
                                <a href="product_details.php?id=<?php echo $product['product_id']; ?>"
                                    class="product-image-wrapper">
                                    <?php if ($product['image']): ?>
                                        <img src="<?php echo BASE_URL . $product['image']; ?>"
                                            alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                            onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                                    <?php else: ?>
                                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #f0f0f0; color: #888;">
                                            No Image
                                        </div>
                                    <?php endif; ?>
                                </a>
                                <div class="product-info">
                                    <h3 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                    <p class="product-price"><?php echo formatPricePKR($product['price']); ?></p>
                                    <a href="product_details.php?id=<?php echo $product['product_id']; ?>" class="btn-gold"
                                        style="padding: 10px 20px; font-size: 0.8rem; display: inline-block; margin-top: 10px; text-decoration: none;">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div style="text-align: center; padding: 100px 20px; background: white; border-radius: 12px;">
                            <i class="fas fa-box-open" style="font-size: 3rem; color: var(--brand-latte); opacity: 0.3; margin-bottom: 20px;"></i>
                            <h3 style="color: var(--brand-espresso); margin-bottom: 10px;">No Products Available</h3>
                            <p style="color: var(--text-muted); margin-bottom: 20px;">
                                <?php if ($category_id): ?>
                                    No products available in this category yet.
                                <?php else: ?>
                                    We're currently updating our inventory. Please check back soon!
                                <?php endif; ?>
                            </p>
                            <a href="products.php" class="btn btn-gold">View All Products</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
    
<?php require_once 'includes/footer.php'; ?>