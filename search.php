<?php
// Start session and include database
session_start();
include('config/db.php');

// Get search query from URL
$search = isset($_GET['query']) ? $_GET['query'] : '';

// Fetch search results from the products table based on query
$sql = "SELECT p.id, p.name, p.price, p.image, c.name AS category
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%' 
        ORDER BY p.id DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error fetching products: " . mysqli_error($conn));
}

?>

<?php include('includes/header.php'); ?>

<div class="container" style="padding: 30px 0;">
    <h2>Search Results for "<?php echo htmlspecialchars($search); ?>"</h2>
    
    <!-- Back to Shop Button -->
    <a href="/ecommerce-website/shop.php" style="
        display: inline-block;
        margin-top: 10px;
        background: #007bff;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
    ">← Back to Shop</a>

    <!-- Display products -->
    <div class="product-grid" style="margin-top: 30px;">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="product-card">
                    <img src="assets/images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p class="price">$<?php echo number_format($row['price'], 2); ?></p>
                    <p class="category"><?php echo htmlspecialchars($row['category']); ?></p>
                    <a href="product.php?id=<?php echo $row['id']; ?>" class="btn">View Details</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products found matching your search.</p>
        <?php endif; ?>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<style>
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 25px;
    }

    .product-card {
        background: #fff;
        border: 1px solid #e1e1e1;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        transition: box-shadow 0.2s ease;
    }

    .product-card:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .product-card img {
        width: 100%;
        height: 200px;
        object-fit: contain;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .product-card h3 {
        font-size: 1.1em;
        margin: 10px 0;
        color: #333;
    }

    .product-card .price {
        color: #27ae60;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .product-card .category {
        font-size: 0.9em;
        color: #888;
        margin-bottom: 10px;
    }

    .product-card .btn {
        display: inline-block;
        padding: 8px 14px;
        background: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .product-card .btn:hover {
        background: #0056b3;
    }

    .product-grid p {
        text-align: center;
        color: #888;
        font-size: 1.2em;
    }
</style>
