<?php
session_start();
include 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Get user data
$user_id = $_SESSION['user']['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

// Profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $update_stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $update_stmt->bind_param("ssi", $name, $email, $user_id);
    $update_stmt->execute();
    $update_stmt->close();

    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['email'] = $email;

    $success_msg = "Profile updated successfully!";
}

// Fetch orders with product names
$order_stmt = $conn->prepare("
    SELECT o.order_id, o.created_at, o.status, o.total_amount, GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
");

if (!$order_stmt) {
    die('SQL Error: ' . $conn->error);
}

$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$orders_result = $order_stmt->get_result();
$order_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Account - KaramMart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background-color: #f9fafb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .account-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        .account-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .account-header h2 {
            color: #333;
        }

        .account-header h2 span {
            color: #38bdf8;
        }

        .profile-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .profile-card img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .form-group input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.3);
        }

        .btn-save {
            background-color: #38bdf8;
            color: white;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .btn-save:hover {
            background-color: #0ea5e9;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        .order-table th, .order-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .order-table th {
            background-color: #f1f5f9;
        }

        .btn-go-home {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #0f172a;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .btn-go-home:hover {
            background-color: #38bdf8;
        }

        @media (max-width: 768px) {
            .account-container {
                padding: 15px;
            }
            .profile-card {
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <!-- Go to Home Button -->
    <button class="btn-go-home" onclick="window.location.href='index.php'"><i class="fas fa-home"></i> Home</button>

    <div class="account-container">
        <!-- Account Header -->
        <div class="account-header">
            <h2>My <span>Account</span></h2>
        </div>

        <!-- Profile Section -->
        <div class="profile-card">
            <div class="d-flex justify-content-center mb-3">
                <img src="assets/images/profile.jpg" alt="Profile Picture">
            </div>
            <h4 class="text-center mb-2"><?= htmlspecialchars($user['name']) ?></h4>
            <p class="text-center text-muted">Email: <?= htmlspecialchars($user['email']) ?></p>

            <form method="POST" action="">
                <h5 class="mb-3">Update Profile</h5>
                <div class="form-group">
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required placeholder="Full Name">
                </div>
                <div class="form-group">
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required placeholder="Email Address">
                </div>
                <button type="submit" name="update_profile" class="btn-save">Save Changes</button>
                <?php if (isset($success_msg)): ?>
                    <div class="alert alert-success mt-3"><?= $success_msg ?></div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Order History -->
        <div>
            <h4>Order History</h4>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Products</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders_result->num_rows > 0): ?>
                        <?php while ($order = $orders_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $order['order_id'] ?></td>
                                <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                <td><?= ucfirst($order['status']) ?></td>
                                <td><?= $order['product_names'] ?></td>
                                <td>₨<?= number_format($order['total_amount'], 2) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">You have no orders yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
