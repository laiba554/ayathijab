<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
define('PAGE_TITLE', 'Product Details');
require_once 'includes/header.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$product_id) {
    header("Location: products.php");
    exit;
}

// Use status = 1 for active products
$stmt = $conn->prepare("SELECT p.*, c.category_name FROM products p JOIN categories c ON p.category_id = c.category_id WHERE p.product_id = ? AND p.status = 1");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) {
    header("Location: products.php");
    exit;
}

$images_res = $conn->query("SELECT * FROM product_images WHERE product_id = $product_id");
$images = [];
while ($img = $images_res->fetch_assoc()) {
    $images[] = $img;
}

$variants_res = $conn->query("SELECT * FROM product_variants WHERE product_id = $product_id AND variant_stock > 0");
$variants = [];
while ($v = $variants_res->fetch_assoc()) {
    $variants[] = $v;
}
?>

<style>
.position-sticky {
    position: sticky !important;
}

@media (max-width: 991px) {
    .position-sticky {
        position: relative !important;
        top: 0 !important;
    }
}

@media (max-width: 768px) {
    div[style*="aspect-ratio: 3/4"] {
        aspect-ratio: 4/5 !important;
    }
    h1[style*="font-size: 3rem"] {
        font-size: 2rem !important;
    }
    span[style*="font-size: 2rem"] {
        font-size: 1.5rem !important;
    }
}

@media (max-width: 576px) {
    div[style*="aspect-ratio: 3/4"] {
        aspect-ratio: 1/1 !important;
    }
    h1[style*="font-size: 3rem"] {
        font-size: 1.75rem !important;
    }
    span[style*="font-size: 2rem"] {
        font-size: 1.3rem !important;
    }
}

.quantity-cart-wrapper {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 1.5rem;
}

.quantity-counter {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid var(--brand-latte);
    border-radius: 4px;
    height: 55px;
    min-width: 140px;
    background: white;
}

.quantity-counter button {
    background: none;
    border: none;
    color: #333;
    cursor: pointer;
    width: 40px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.2s;
}

.quantity-counter button:hover {
    background: #f5f5f5;
}

.quantity-counter input {
    width: 60px;
    border: 0;
    background: transparent;
    text-align: center;
    font-weight: bold;
    font-size: 1.1rem;
    color: var(--brand-espresso);
    outline: none;
}

.quantity-counter input::-webkit-outer-spin-button,
.quantity-counter input::-webkit-inner-spin-button {
    -webkit-appearance: none;
}

.quantity-counter input[type=number] {
    -moz-appearance: textfield;
}

.add-to-bag-btn {
    flex-grow: 1;
    height: 55px;
}

.wishlist-btn {
    width: 55px;
    height: 55px;
    border: 1px solid var(--brand-latte);
    color: #f43f5e;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all 0.2s;
    text-decoration: none;
}

.wishlist-btn:hover {
    background: #fff5f5;
    border-color: #f43f5e;
    color: #f43f5e;
}

@media (max-width: 768px) {
    .quantity-cart-wrapper {
        flex-wrap: wrap;
        gap: 10px;
    }
    .quantity-counter {
        height: 50px;
        min-width: 130px;
    }
    .add-to-bag-btn { height: 50px; }
    .wishlist-btn { width: 50px; height: 50px; }
}

@media (max-width: 576px) {
    .quantity-cart-wrapper {
        flex-direction: column;
        width: 100%;
    }
    .quantity-counter {
        width: 100%;
        min-width: 100%;
        height: 52px;
    }
    .add-to-bag-btn { width: 100%; height: 52px; }
    .wishlist-btn { width: 100%; height: 52px; }
}

input[type="radio"]:checked + .variant-chip {
    background: var(--brand-espresso);
    color: white;
    border-color: var(--brand-espresso);
    opacity: 1;
}

.variant-chip:hover {
    border-color: var(--brand-latte);
    opacity: 1;
}

.flash-success {
    background: #d4edda;
    color: #155724;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #c3e6cb;
    display: flex;
    align-items: center;
    gap: 10px;
}

.flash-error {
    background: #f8d7da;
    color: #721c24;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
    display: flex;
    align-items: center;
    gap: 10px;
}
</style>

<!-- Flash Message -->
<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="container mt-3">
        <div class="<?php echo $_SESSION['flash_message']['type'] == 'success' ? 'flash-success' : 'flash-error'; ?>">
            <i class="fas <?php echo $_SESSION['flash_message']['type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($_SESSION['flash_message']['text']); unset($_SESSION['flash_message']); ?>
        </div>
    </div>
<?php endif; ?>

<div class="container" style="margin-top: 50px; margin-bottom: 80px;">
    <div class="row g-5">

        <!-- Left: Image Gallery -->
        <div class="col-md-6 col-lg-7">
            <div class="position-sticky" style="top: 100px;">
                <div style="background: var(--bg-nude); border: 1px solid rgba(154, 123, 100, 0.1); overflow: hidden; aspect-ratio: 3/4; margin-bottom: 20px;">
                    <img id="mainProductImage"
                        src="<?php echo BASE_URL . $product['image']; ?>"
                        style="width: 100%; height: 100%; object-fit: cover; transition: opacity 0.3s;"
                        onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                </div>

                <?php if (!empty($images)): ?>
                    <div class="d-flex gap-2 flex-wrap">
                        <div style="width: 70px;">
                            <div onclick="updateMainImage('<?php echo BASE_URL . $product['image']; ?>')"
                                style="cursor: pointer; border: 2px solid var(--brand-latte); aspect-ratio: 1/1; overflow: hidden;">
                                <img src="<?php echo BASE_URL . $product['image']; ?>"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>
                        <?php foreach ($images as $img): ?>
                            <div style="width: 70px;">
                                <div onclick="updateMainImage('<?php echo BASE_URL . $img['image_path']; ?>')"
                                    style="cursor: pointer; border: 1px solid rgba(154, 123, 100, 0.1); aspect-ratio: 1/1; overflow: hidden;">
                                    <img src="<?php echo BASE_URL . $img['image_path']; ?>"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Product Info -->
        <div class="col-md-6 col-lg-5">
            <div style="border-bottom: 1px solid rgba(154, 123, 100, 0.1); padding-bottom: 30px; margin-bottom: 30px;">
                <span style="color: var(--brand-latte); font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 2px;">
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </span>
                <h1 style="font-size: 3rem; margin: 15px 0 20px; line-height: 1.1; color: var(--brand-espresso);">
                    <?php echo htmlspecialchars($product['product_name']); ?>
                </h1>
                <div class="d-flex align-items-center gap-3">
                    <span style="font-size: 2rem; font-weight: 700; color: var(--brand-espresso);">
                        <?php echo formatPricePKR($product['price']); ?>
                    </span>
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <span style="color: #2ecc71; font-size: 0.9rem; font-weight: 600;">
                            <i class="fas fa-check-circle"></i> In Stock
                        </span>
                    <?php else: ?>
                        <span style="color: #e74c3c; font-size: 0.9rem; font-weight: 600;">
                            <i class="fas fa-times-circle"></i> Out of Stock
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div style="margin-bottom: 40px;">
                <h3 style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 15px; color: var(--text-muted);">
                    Description
                </h3>
                <p style="color: var(--text-muted); line-height: 1.8; font-size: 1.05rem;">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </p>
            </div>

            <?php if ($product['stock_quantity'] > 0): ?>

                <form action="cart_action.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo intval($product['product_id']); ?>">

                    <?php if (!empty($variants)): ?>
                        <div style="margin-bottom: 30px;">
                            <h3 style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 15px; color: var(--text-muted);">
                                Select Option
                            </h3>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($variants as $v): ?>
                                    <label style="cursor: pointer;">
                                        <input type="radio" name="variant_id"
                                            value="<?php echo $v['variant_id']; ?>"
                                            required style="display: none;">
                                        <div class="variant-chip"
                                            style="padding: 12px 25px; border: 1px solid var(--brand-latte); opacity: 0.8; font-size: 0.9rem; font-weight: 600; border-radius: 4px; transition: all 0.2s;">
                                            <?php echo htmlspecialchars($v['size'] . " " . $v['color']); ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="quantity-cart-wrapper">
                        <div class="quantity-counter">
                            <button type="button" onclick="changeQty(-1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="quantity" name="quantity"
                                value="1" min="1"
                                max="<?php echo intval($product['stock_quantity']); ?>"
                                readonly>
                            <button type="button" onclick="changeQty(1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>

                        <button type="submit" class="btn btn-gold add-to-bag-btn">
                            <i class="fas fa-shopping-bag"></i> Add to Bag
                        </button>

                        <a href="wishlist_action.php?action=add&id=<?php echo intval($product['product_id']); ?>"
                            class="wishlist-btn">
                            <i class="far fa-heart"></i>
                        </a>
                    </div>
                </form>

            <?php else: ?>
                <div class="quantity-cart-wrapper">
                    <button type="button" class="btn btn-secondary"
                        disabled style="width: 100%; height: 55px;">
                        <i class="fas fa-times-circle"></i> Out of Stock
                    </button>
                </div>
            <?php endif; ?>

            <div style="border-top: 1px solid rgba(154, 123, 100, 0.1); padding-top: 30px;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <i class="fas fa-shipping-fast" style="color: var(--brand-latte);"></i>
                    <span style="font-size: 0.9rem; font-weight: 500;">
                        Fast delivery within 3-5 business days across Pakistan.
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-shield-alt" style="color: var(--brand-latte);"></i>
                    <span style="font-size: 0.9rem; font-weight: 500;">
                        Secure payment & quality guarantee.
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateMainImage(src) {
        var img = document.getElementById('mainProductImage');
        img.style.opacity = '0';
        setTimeout(() => {
            img.src = src;
            img.style.opacity = '1';
        }, 300);
    }

    function changeQty(amt) {
        var input = document.getElementById('quantity');
        var val = parseInt(input.value) + amt;
        var max = parseInt(input.max);
        if (val >= 1 && val <= max) {
            input.value = val;
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>