<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
                'role'  => $user['role']
            ];

            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "❌ Incorrect password!";
        }
    } else {
        $error = "❌ Email not registered!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>KaramMart Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Modern Custom Styling -->
  <style>
    body {
      background: #f9fafb;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .login-box {
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      padding: 40px;
      max-width: 450px;
      width: 100%;
    }

    .login-box h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #333;
    }

    .login-box h2 span {
      color: #38bdf8;
    }

    .form-floating > .form-control {
      border-radius: 8px;
      border: 1px solid #cbd5e1;
      background-color: #f8fafc;
      padding: 12px 15px;
      font-size: 16px;
      transition: all 0.3s ease;
    }

    .form-floating > .form-control:focus {
      border-color: #38bdf8;
      box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.3);
      background-color: #ffffff;
    }

    .form-floating label {
      color: #64748b;
      font-size: 16px;
    }

    .form-icon {
      position: absolute;
      top: 50%;
      left: 15px;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 18px;
    }

    .input-icon-wrapper {
      position: relative;
    }

    .input-icon-wrapper .form-control {
      padding-left: 40px;
    }

    .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #94a3b8;
      font-size: 18px;
    }

    .btn-login {
      background-color: #0f172a;
      color: #fff;
      width: 100%;
      border: none;
      padding: 12px;
      font-size: 16px;
      margin-top: 20px;
      border-radius: 8px;
      transition: background-color 0.3s ease;
    }

    .btn-login:hover {
      background-color: #1e293b;
    }

    .error-msg {
      background-color: #fee2e2;
      color: #dc2626;
      padding: 10px 15px;
      border-radius: 6px;
      font-size: 14px;
      margin-bottom: 20px;
    }

    .login-box p {
      text-align: center;
      font-size: 14px;
      margin-top: 20px;
    }

    .login-box p a {
      color: #0ea5e9;
      text-decoration: none;
    }

    .login-box p a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <h2>Karam<span>Mart</span> Login</h2>

    <?php if (isset($error)): ?>
      <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <!-- Email Input -->
      <div class="form-floating mb-3 input-icon-wrapper">
        <i class="fas fa-envelope form-icon"></i>
        <input type="email" name="email" class="form-control" id="email" placeholder="Email address" required>
        <label for="email">Email address</label>
      </div>

      <!-- Password Input -->
      <div class="form-floating mb-3 input-icon-wrapper">
        <i class="fas fa-lock form-icon"></i>
        <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
        <label for="password">Password</label>
        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
      </div>

      <button type="submit" class="btn-login">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register Now</a></p>
  </div>

  <!-- Show/Hide Password -->
  <script>
    const togglePassword = document.getElementById("togglePassword");
    const passwordField = document.getElementById("password");

    togglePassword.addEventListener("click", () => {
      const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
      passwordField.setAttribute("type", type);
      togglePassword.classList.toggle("fa-eye");
      togglePassword.classList.toggle("fa-eye-slash");
    });
  </script>

</body>
</html>
