<?php
include 'session-check.php';


include "connection.php";

// Get staff ID from URL
$id = $_GET["id"];

// Fetch staff details
$query = "SELECT * FROM staff WHERE id = '$id'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_array($result);

if(isset($_POST["submit"])){
    $name = $_POST['name'];
    $degree = $_POST['degree'];
    $email = $_POST['email'];
    $number = $_POST['number'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];

    $update = "UPDATE staff SET 
                name='$name', 
                degree='$degree', 
                email='$email', 
                number='$number', 
                gender='$gender',
                dob='$dob',
                address='$address'
                WHERE id='$id'";
    $update_sql = mysqli_query($con, $update);

    if($update_sql){
        echo "<script>alert('Staff updated successfully'); window.location='manage staff.php';</script>";
    } else {
        echo "<script>alert('Error updating staff');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Staff</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; }
body { display:flex; min-height:100vh; background:#f4f9f9; }

/* Sidebar */
.sidebar {
    width:220px; background:#007acc; color:#fff; flex-shrink:0;
    min-height:100vh; padding:20px 10px; position:fixed; top:0; left:0;
    transition:transform 0.3s ease-in-out;
}
.sidebar h2 { text-align:center; margin-bottom:30px; }
.sidebar a { display:flex; align-items:center; padding:12px 15px; margin-bottom:10px; color:#fff; text-decoration:none; border-radius:6px; transition:0.3s; }
.sidebar a i { margin-right:10px; }
.sidebar a:hover, .sidebar a.active { background:#005f99; }

/* Main content */
.page-wrapper { display:flex; width:100%; }
.main-content { margin-left:220px; width:calc(100% - 220px); padding:20px; transition:margin-left 0.3s ease-in-out; }

/* Navbar */
.navbar { font-size:1.2rem; font-weight:600; color:#007acc; border-bottom:2px solid #e0e0e0; padding-bottom:10px; display:flex; justify-content:space-between; align-items:center; }

/* Toggle button on right */
.toggle-btn {
    display:none; background:#007acc; color:#fff; border:none; padding:8px 12px;
    border-radius:6px; cursor:pointer; font-size:1.2rem;
}

/* Card & Form */
.card { background:#fff; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); padding:20px; }
.form-label { color:#007acc; }
.form-control, .form-select { border-radius:6px; }
.form-control:focus, .form-select:focus { border-color:#007acc; box-shadow:0 0 5px rgba(0,122,204,0.4); }
button[type="submit"] { background:#007acc; border:none; color:#fff; }
button[type="submit"]:hover { background:#005f99; }

footer { text-align:center; margin-top:20px; padding:15px; border-top:1px solid #ddd; color:#666; }

/* Responsive */
@media(max-width:992px){
    .sidebar { transform:translateX(-100%); position:fixed; z-index:1000; }
    .sidebar.active { transform:translateX(0); }
    .toggle-btn { display:inline-block; }
    .main-content { margin-left:0; width:100%; }
}
</style>
</head>
<body>

<div class="sidebar" id="sidebar">
  <h2>Health Care Admin</h2>
  <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="view appointment.php"><i class="fas fa-calendar-check"></i> Appointments</a>
  <a href="add staff.php"><i class="fas fa-user-plus"></i> Add Staff</a>
  <a href="manage staff.php" class="active"><i class="fas fa-users-cog"></i> Manage Staff</a>
  <a href="upload report.php"><i class="fas fa-file-upload"></i> Upload Report</a>
  <a href="view report.php"><i class="fas fa-file-pdf"></i> View Reports</a>
  <a href="manage profile.php"><i class="fas fa-user-cog"></i> Manage Profile</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>

</div>

<div class="main-content">
  <nav class="navbar mb-4">
    <span>Edit Staff</span>
    <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
  </nav>

  <div class="container">
    <div class="card">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Staff Name</label>
          <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Degree</label>
          <input type="text" name="degree" class="form-control" value="<?php echo $row['degree']; ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?php echo $row['email']; ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Contact Number</label>
          <input type="text" name="number" class="form-control" maxlength="10"
                 oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                 value="<?php echo $row['number'];?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-select" required>
            <option value="">Select Gender</option>
            <option value="Male" <?php if($row['gender']=="Male") echo "selected"; ?>>Male</option>
            <option value="Female" <?php if($row['gender']=="Female") echo "selected"; ?>>Female</option>
            <option value="Other" <?php if($row['gender']=="Other") echo "selected"; ?>>Other</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Date of Birth</label>
          <input type="date" name="dob" class="form-control" value="<?php echo $row['dob']; ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Address</label>
          <textarea name="address" class="form-control" rows="3" required><?php echo $row['address']; ?></textarea>
        </div>
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <button type="submit" class="btn btn-primary" name="submit">Update Staff</button>
      </form>
    </div>
  </div>

  <footer>&copy; 2025 Health Care. All Rights Reserved.</footer>
</div>

<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('active');
}
</script>
</body>
</html>
