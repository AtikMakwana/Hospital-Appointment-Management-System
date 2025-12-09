<?php
session_start();
include "connection.php";

$message = ""; // initialize message

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $pass = $_POST['password'];

    $fetch = "SELECT * FROM admin WHERE name='$name'";
    $sql = mysqli_query($con, $fetch);

    if ($sql && mysqli_num_rows($sql) > 0) {
        $query = mysqli_fetch_assoc($sql);
        $check_pass = $query['password'];

        // Verify password
        if (password_verify($pass, $check_pass)) {
            $_SESSION['admin'] = $name; // store session
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "❌ Incorrect username or password";
        }
    } else {
        $message = "⚠️ Username not found";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <style>
    * {
      margin: 0; padding: 0; box-sizing: border-box;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      background: linear-gradient(135deg, #007acc, #004d80);
      height: 100vh; display: flex; justify-content: center; align-items: center;
    }
    .login-container {
      background: #fff; padding: 40px 30px;
      border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.15);
      width: 100%; max-width: 400px; text-align: center;
    }
    .login-container h2 {
      margin-bottom: 20px; color: #333;
    }
    .message {
      margin-bottom: 15px;
      padding: 10px;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 600;
    }
    .error {
      background: #ffe6e6; color: #d8000c; border: 1px solid #d8000c;
    }
    .success {
      background: #e6ffed; color: #006400; border: 1px solid #006400;
    }
    .input-group { margin-bottom: 15px; text-align: left; }
    .input-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #444; }
    .input-group input {
      width: 100%; padding: 10px 12px; border: 1px solid #ccc;
      border-radius: 6px; outline: none; font-size: 14px; transition: border-color 0.3s;
    }
    .input-group input:focus { border-color: #007acc; }
    .btn {
      width: 100%; padding: 12px; background: #007acc; color: #fff;
      border: none; border-radius: 6px; font-size: 16px; cursor: pointer;
      transition: background 0.3s;
    }
    .btn:hover { background: #005f99; }
  </style>
</head>
<body>
  
  <div class="login-container">
    <h2>Admin Login</h2>

    <?php if (!empty($message)): ?>
      <div class="message error"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="input-group">
        <label for="name">Username</label>
        <input type="text" id="name" placeholder="Enter username" name="name" required>
      </div>
      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" placeholder="Enter password" name="password" required>
      </div>
      <button type="submit" class="btn" name="submit">Login</button>
    </form>
  </div>
</body>
</html>
