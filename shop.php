<?php
include('includes/header.php');
include('includes/navbar.php');
include('config/db.php');

// Fetch categories
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

// Filters
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : '';
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : 'latest';

// Build SQL
$sql = "SELECT * FROM products WHERE 1";
if ($category_filter) $sql .= " AND category_id = $category_filter";

switch ($sort_order) {
    case 'low_high': $sql .= " ORDER BY price ASC"; break;
    case 'high_low': $sql .= " ORDER BY price DESC"; break;
    default: $sql .= " ORDER BY created_at DESC"; break;
}

$products = mysqli_query($conn, $sql);
?>

<div class="shop-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Filter by Category</h3>
        <ul>
            <li><a href="shop.php" class="<?= $category_filter == '' ? 'active' : ''; ?>">All Categories</a></li>
            <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                <li>
                    <a href="shop.php?category=<?= $cat['id']; ?>"
                       class="<?= ($category_filter == $cat['id']) ? 'active' : ''; ?>">
                        <?= htmlspecialchars($cat['name']); ?>
                    </a>
                </li>
            <?php } ?>
        </ul>

        <h3>Sort by Price</h3>
        <form method="GET" action="shop.php">
            <?php if ($category_filter): ?>
                <input type="hidden" name="category" value="<?= $category_filter; ?>">
            <?php endif; ?>
            <select name="sort" onchange="this.form.submit()">
                <option value="latest" <?= $sort_order == 'latest' ? 'selected' : ''; ?>>Latest</option>
                <option value="low_high" <?= $sort_order == 'low_high' ? 'selected' : ''; ?>>Low to High</option>
                <option value="high_low" <?= $sort_order == 'high_low' ? 'selected' : ''; ?>>High to Low</option>
            </select>
        </form>
    </div>

    <!-- Products -->
    <div class="products">
        <h2>Shop Products</h2>
        <div class="product-grid">
            <?php while ($product = mysqli_fetch_assoc($products)) { ?>
                <div class="product-card">
                    <img src="assets/images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                    <h3><?= htmlspecialchars($product['name']); ?></h3>
                    
                    <p class="description"><?= htmlspecialchars(substr($product['short_description'], 0, 80)); ?>...</p>

                    <?php if (!empty($product['available_sizes'])): ?>
                        <p class="product-meta"><strong>Sizes:</strong> <?= htmlspecialchars($product['available_sizes']); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($product['available_colors'])): ?>
                        <p class="product-meta"><strong>Colors:</strong> <?= htmlspecialchars($product['available_colors']); ?></p>
                    <?php endif; ?>

                    <p class="category">Category: <?= getCategoryName($conn, $product['category_id']); ?></p>
                    <div class="rating">⭐⭐⭐⭐☆</div>
                    <p class="price">Rs <?= number_format($product['price']); ?></p>
                    <a href="pages/product.php?id=<?= $product['id']; ?>" class="btn">View Details</a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<!-- STYLES -->
<style>
body {
    background: #f3f4f6;
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
}

.shop-container {
    display: flex;
    flex-wrap: wrap;
    padding: 30px;
}

/* Sidebar */
.sidebar {
    width: 220px;
    background: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    margin-right: 20px;
    border-radius: 8px;
    box-shadow: 0 0 8px rgba(0,0,0,0.05);
}
.sidebar h3 {
    font-size: 18px;
    margin-bottom: 10px;
}
.sidebar ul {
    list-style: none;
    padding-left: 0;
}
.sidebar ul li {
    margin: 8px 0;
}
.sidebar ul li a {
    text-decoration: none;
    color: #374151;
}
.sidebar ul li a:hover,
.sidebar ul li a.active {
    color: #2563eb;
    font-weight: bold;
}
.sidebar select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-top: 10px;
}

/* Products Section */
.products {
    flex: 1;
}
.products h2 {
    font-size: 24px;
    margin-bottom: 20px;
}

/* Grid */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 30px;
}

/* Card */
.product-card {
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    transition: 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px;
    position: relative;
}
.product-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}
.product-card img {
    width: 100%;
    aspect-ratio: 1 / 1;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 12px;
    background-color: #f9fafb;
}
.product-card h3 {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 6px;
    text-align: center;
}
.product-card .description {
    font-size: 13px;
    color: #6b7280;
    text-align: center;
    margin-bottom: 6px;
    line-height: 1.4;
    min-height: 36px;
}
.product-card .category {
    font-size: 12px;
    color: #9ca3af;
    margin-bottom: 5px;
}
.product-card .rating {
    font-size: 14px;
    color: #facc15;
    margin-bottom: 6px;
}
.product-card .price {
    font-size: 17px;
    font-weight: bold;
    color: #10b981;
    margin-bottom: 12px;
}
.product-meta {
    font-size: 12px;
    color: #4b5563;
    margin-bottom: 4px;
    text-align: center;
}
.btn {
    padding: 8px 16px;
    background-color: #2563eb;
    color: #fff;
    border-radius: 5px;
    font-size: 14px;
    text-decoration: none;
    font-weight: 500;
    transition: 0.3s ease;
}
.btn:hover {
    background-color: #1e40af;
}

/* Responsive */
@media(max-width: 768px) {
    .shop-container {
        flex-direction: column;
    }
    .sidebar {
        width: 100%;
        margin-bottom: 20px;
    }
}
</style>

<?php
// Helper function
function getCategoryName($conn, $cat_id) {
    $res = mysqli_query($conn, "SELECT name FROM categories WHERE id = $cat_id LIMIT 1");
    $row = mysqli_fetch_assoc($res);
    return $row ? $row['name'] : 'Unknown';
}
?>
