<?php
session_start();
include('config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone    = trim($_POST['phone']);
    $role     = 'customer'; // default role

    // Profile image upload
    $profile_image = '';
    if (!empty($_FILES['profile_image']['name'])) {
        $image_name = $_FILES['profile_image']['name'];
        $image_tmp  = $_FILES['profile_image']['tmp_name'];
        $ext        = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $new_name   = uniqid() . '.' . $ext;
        $upload_dir = 'uploads/' . $new_name;

        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            move_uploaded_file($image_tmp, $upload_dir);
            $profile_image = $new_name;
        }
    }

    // Check if email exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $error = "Email already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, profile_image, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $password, $phone, $profile_image, $role);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Account created successfully!";
            header("Location: login.php");
            exit;
        } else {
            $error = "Something went wrong!";
        }
    }
}
?>

<!-- Registration Page HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Karam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f6fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border-radius: 10px;
            border: none;
        }

        .card-header {
            background-color: #007bff;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            color: white;
        }

        input.form-control {
            border-radius: 5px;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
            border-radius: 5px;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        footer {
            background-color: #343a40;
            color: white;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .container {
            padding-top: 50px;
            padding-bottom: 50px;
        }

        .card-body {
            padding: 2rem;
        }

        .navbar {
            background-color: #007bff;
        }

        .navbar .navbar-brand {
            color: white;
            font-size: 1.5rem;
        }

        .navbar .navbar-nav .nav-link {
            color: white;
        }

        .navbar .navbar-nav .nav-link:hover {
            color: #ccc;
        }
    </style>
</head>
<body>
    <!-- Simplified Header -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">Karam</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Signup Form -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4>Create Your Account</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label>Full Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" pattern="[0-9]{10}" required placeholder="Enter 11-digit phone number">
                            </div>
                            <div class="mb-3">
                                <label>Profile Image</label>
                                <input type="file" name="profile_image" class="form-control" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-success w-100">Register</button>
                            <div class="mt-3 text-center">
                                Already have an account? <a href="login.php">Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simplified Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2025 Karam. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS & Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
