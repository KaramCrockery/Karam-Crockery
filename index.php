<?php
include('includes/header.php');
include('includes/navbar.php');
include('config/db.php');

// Fetch categories
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

// Fetch latest 4 products with category names
$products = mysqli_query($conn, "
    SELECT p.*, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC 
    LIMIT 4
");
?>

<!-- ✨ HERO SECTION -->
<div class="hero-section">
    <div class="hero-content">
        <h1>Welcome to KaramCrockery</h1>
        <p class="hero-sub">Elegant & Premium Crockery for Every Home</p>
        <a href="shop.php" class="cta-button">Shop Now</a>
    </div>
</div>

<!-- 🖼️ BANNER -->
<div class="banner">
    <img src="assets/images/banner.jpg" alt="Main Banner">
</div>

<!-- 🧩 CATEGORIES SECTION -->
<div class="container" style="padding: 40px 0;">
    <h2 class="section-title">Shop by Category</h2>
    <div class="category-grid">
        <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
            <div class="category-card">
                <a href="shop.php?category=<?php echo $cat['id']; ?>">
                    <div class="category-img">
                        <img src="assets/images/<?php echo $cat['image'] ?? 'default-category.png'; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>">
                        <div class="category-label"><?php echo htmlspecialchars($cat['name']); ?></div>
                        <div class="category-hover">
                            <div class="hover-icon">🔍</div>
                            <div class="hover-info">
                                <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                                <p>Discover elegant <?php echo htmlspecialchars($cat['name']); ?>.</p>
                                <span class="view-btn" title="Explore this category">View Category</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php } ?>
    </div>
</div>

<!-- 🛍️ FEATURED PRODUCTS -->
<div class="container" style="padding: 40px 0;">
    <h2 class="section-title">Featured Products</h2>
    <div class="product-grid">
        <?php while ($row = mysqli_fetch_assoc($products)) { 
            $fake_rating = rand(3, 5); // Fake rating (3 to 5 stars)
        ?>
            <div class="product-card">
                <img src="assets/images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>

                <!-- ✅ Real category name from JOIN -->
                <p class="category-label">Category: <?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></p>

                <!-- ✅ Product Description -->
                <p class="description">
                    <?php echo !empty($row['description']) ? htmlspecialchars($row['description']) : 'No description available.'; ?>
                </p>

                <p class="price">Rs. <?php echo number_format($row['price'], 0); ?></p>

                <!-- ⭐ Fake rating display -->
                <div class="ratings">
                    <?php for ($i = 0; $i < 5; $i++) {
                        echo $i < $fake_rating ? '★' : '☆';
                    } ?>
                </div>

                <a href="pages/product.php?id=<?php echo $row['id']; ?>" class="btn">View Details</a>
            </div>
        <?php } ?>
    </div>
</div>

<!-- 🔒 INFO STRIP -->
<div class="info-strip">
    <div>
        <img src="assets/images/Delivery.avif" alt="Delivery">
        <p>Fast Delivery Nationwide</p>
    </div>
    <div>
        <img src="assets/images/Securepayments.avif" alt="Secure Payment">
        <p>Secure Payment</p>
    </div>
    <div>
        <img src="assets/images/support.png" alt="24/7 Support">
        <p>24/7 Customer Support</p>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<!-- 🌐 INTERNAL CSS -->
<style>
body {
    background-color: #f9fafb;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #111827;
    margin: 0;
    padding: 0;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(to right, #0ea5e9, #2563eb);
    padding: 80px 20px;
    text-align: center;
    color: white;
}
.hero-content h1 {
    font-size: 42px;
    font-weight: 700;
    margin-bottom: 15px;
}
.hero-sub {
    font-size: 18px;
    margin-bottom: 25px;
}
.cta-button {
    padding: 12px 24px;
    background-color: #1e3a8a;
    color: #fff;
    font-weight: 600;
    border-radius: 6px;
    text-decoration: none;
    transition: 0.3s;
}
.cta-button:hover {
    background-color: #1d4ed8;
}

/* Banner */
.banner {
    text-align: center;
    margin: 40px 0;
}
.banner img {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

/* Section Title */
.section-title {
    text-align: center;
    font-size: 30px;
    margin-bottom: 30px;
    font-weight: 700;
    color: #1e3a8a;
}

/* Category Grid */
.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 25px;
}

/* Category Card */
.category-card {
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    transition: transform 0.35s ease, box-shadow 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.category-card:hover {
    transform: scale(1.03);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}
.category-img {
    position: relative;
    width: 100%;
    height: 200px;
    background-color: #f3f4f6;
}
.category-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
}
.category-label {
    position: absolute;
    top: 12px;
    left: 12px;
    background-color: rgba(255, 255, 255, 0.9);
    color: #1e40af;
    font-size: 13px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 5px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    z-index: 2;
}
.category-hover {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    backdrop-filter: blur(6px);
    background: rgba(30, 58, 138, 0.65);
    color: white;
    opacity: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: 0.4s ease;
    padding: 20px;
    text-align: center;
}
.category-card:hover .category-hover {
    opacity: 1;
}
.hover-icon {
    font-size: 28px;
    margin-bottom: 12px;
}
.hover-info h3 {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 8px;
}
.hover-info p {
    font-size: 14px;
    margin-bottom: 10px;
}
.view-btn {
    background-color: #fff;
    color: #2563eb;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
}

/* Products */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 25px;
}
.product-card {
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: 0.3s;
}
.product-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}
.product-card img {
    width: 100%;
    height: 180px;
    object-fit: contain;
    margin-bottom: 10px;
}
.product-card h3 {
    font-size: 18px;
    font-weight: 500;
    color: #111827;
}
.category-label {
    font-size: 14px;
    color: #888;
}
.price {
    font-size: 16px;
    font-weight: bold;
    color: #22c55e;
    margin: 5px 0 10px;
}
.ratings {
    font-size: 20px;
    color: #fbbf24;
    margin: 10px 0;
}
.btn {
    display: inline-block;
    padding: 8px 16px;
    background-color: #2563eb;
    color: white;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
}
.btn:hover {
    background-color: #1d4ed8;
}

/* Info Strip */
.info-strip {
    background-color: #eef2ff;
    display: flex;
    justify-content: space-around;
    padding: 40px 10px;
    margin-top: 60px;
    text-align: center;
}
.info-strip div {
    max-width: 160px;
}
.info-strip div img {
    width: 45px;
    height: 45px;
    margin-bottom: 12px;
}
.info-strip div p {
    font-size: 14px;
    font-weight: 600;
    color: #334155;
    margin: 0;
}
</style>
