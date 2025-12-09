<?php
include 'session-check.php';



//change password
include "connection.php";
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $cpassword = $_POST['cpassword'];
    $npassword = $_POST['npassword'];
    $npass = password_hash($npassword, PASSWORD_BCRYPT);
    $fetch = "SELECT * FROM admin WHERE name='$name'";
    $view = mysqli_query($con, $fetch);
    $query = mysqli_fetch_assoc($view);
    $dbpass = $query['password'];
    $verify = password_verify($cpassword, $dbpass);
    if($verify){
        $update = "UPDATE admin SET name='$name', password='$npass' WHERE name='$name'";
        $result = mysqli_query($con, $update);
        if($result){
            echo "<script>alert('Profile updated successfully'); window.location.href='manage profile.php';</script>";
        } else {
            echo "<script>alert('Error updating profile'); window.location.href='manage profile.php';</script>";
        }
    } else {
        echo "<script>alert('Current password is incorrect'); window.location.href='manage profile.php';</script>";
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Profile</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
  * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  body {
      background-color: #f4f9f9;
      color: #333;
  }
  /* Sidebar */
  .sidebar {
      width: 220px;
      background-color: #007acc;
      color: #fff;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      padding: 20px 10px;
      transition: transform 0.3s ease;
      z-index: 1000;
  }
  .sidebar h2 {
      font-size: 1.5rem;
      margin-bottom: 30px;
      text-align: center;
  }
  .sidebar a {
      display: flex;
      align-items: center;
      padding: 12px 15px;
      margin-bottom: 10px;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      transition: background 0.3s ease;
  }
  .sidebar a i {
      margin-right: 10px;
      font-size: 1.2rem;
  }
  .sidebar a:hover,
  .sidebar a.active {
      background-color: #005f99;
  }

  /* Main Content */
  .main-content {
      margin-left: 220px;
      padding: 20px;
      transition: margin-left 0.3s ease;
  }
  .navbar {
      font-size: 1.4rem;
      font-weight: 600;
      margin-bottom: 20px;
      color: #007acc;
      border-bottom: 2px solid #e0e0e0;
      padding-bottom: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
  }
  /* Hamburger Button */
  .menu-btn {
      display: none;
      font-size: 1.6rem;
      background: none;
      border: none;
      cursor: pointer;
      color: #007acc;
  }

  /* Form */
  form {
      background: #fff;
      padding: 20px;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      max-width: 800px;
      width: 100%;
      margin: 20px auto;
  }
  .form-group {
      margin-bottom: 15px;
  }
  label {
      display: block;
      color: #007acc;
      margin-bottom: 6px;
      font-weight: 500;
  }
  input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 0.95rem;
  }
  button {
      background-color: #007acc;
      color: #fff;
      border: none;
      padding: 10px 16px;
      border-radius: 6px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
  }
  button:hover {
      background-color: #005f99;
  }

  footer {
      text-align: center;
      padding: 15px;
      margin-top: 30px;
      color: #666;
      font-size: 0.9rem;
      border-top: 1px solid #e0e0e0;
  }

  /* Responsive */
  @media (max-width: 992px) {
      .sidebar {
          transform: translateX(-100%);
      }
      .sidebar.active {
          transform: translateX(0);
      }
      .main-content {
          margin-left: 0;
      }
      .menu-btn {
          display: block;
      }
  }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
      <h2>Health Care Admin</h2>
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="view appointment.php"><i class="fas fa-calendar-check"></i> Appointments</a>
      <a href="add staff.php"><i class="fas fa-user-plus"></i> Add Staff</a>
      <a href="manage staff.php"><i class="fas fa-users-cog"></i> Manage Staff</a>
      <a href="upload report.php"><i class="fas fa-file-upload"></i> Upload Report</a>
      <a href="view report.php"><i class="fas fa-file-pdf"></i> View Reports</a>
      <a href="manage profile.php" class="active"><i class="fas fa-user-cog"></i> Manage Profile</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="main-content" id="main">
      <div class="navbar">
          <span>Manage Profile</span>
          <button class="menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
      </div>

      <form method="POST">
          <div class="form-group">
              <label for="username">Username</label>
              <input type="text" id="username" name="name" value="<?php echo $_SESSION['admin']; ?>" readonly>
          </div>
            <div class="form-group">
              <label for="cpassword">Current Password</label>
              <input type="password" id="cpassword" name="cpassword" placeholder="Enter current password" required>
          </div>
          <div class="form-group">
              <label for="new_password">New Password</label>
              <input type="password" id="npassword" name="npassword" placeholder="Enter new password" required>
          </div>
          <button type="submit" name="submit">Update Profile</button>
      </form>

      <footer>
          &copy; 2025 Health Care. All Rights Reserved.
      </footer>
  </div>

  <!-- JS for toggle -->
  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("active");
    }
  </script>
</body>
</html>
