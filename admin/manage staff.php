<?php
include 'session-check.php';


include "connection.php";

// Change status
if (isset($_GET['status_id']) && isset($_GET['action'])) {
    $id = intval($_GET['status_id']);
    $action = $_GET['action'];

    $res = mysqli_query($con, "SELECT status FROM staff WHERE id=$id");
    if ($row = mysqli_fetch_assoc($res)) {
        $current_status = $row['status'];
        if ($action == 'cancel' && $current_status == 'Available') {
            mysqli_query($con, "UPDATE staff SET status='Not Available' WHERE id=$id");
        }
        if ($action == 'retrieve' && $current_status == 'Not Available') {
            mysqli_query($con, "UPDATE staff SET status='Available' WHERE id=$id");
        }
    }
    header("Location: manage staff.php");
    exit();
}

// Delete staff
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    mysqli_query($con, "DELETE FROM staff WHERE id = $delete_id");
    header("Location: manage staff.php");
    exit();
}

// Fetch all staff
$result = mysqli_query($con, "SELECT * FROM staff");
$i = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manage Staff</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
* { box-sizing: border-box; }
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
.sidebar h2 { font-size: 1.5rem; margin-bottom: 30px; text-align: center; }
.sidebar a { display: flex; align-items: center; padding: 12px 15px; margin-bottom: 10px; color: #fff; text-decoration: none; border-radius: 6px; transition: background 0.3s ease; }
.sidebar a i { margin-right: 10px; font-size: 1.2rem; }
.sidebar a:hover, .sidebar a.active { background-color: #005f99; }

/* Main Content */
.main-content { margin-left: 220px; padding: 20px; transition: margin-left 0.3s ease; }
.navbar { font-size: 1.4rem; font-weight: 600; color: #007acc; border-bottom: 2px solid #e0e0e0; padding: 10px; display: flex; justify-content: space-between; align-items: center; }

/* Toggle Button */
.menu-btn { display: none; font-size: 1.6rem; background: none; border: none; cursor: pointer; color: #007acc; }

/* Table */
.table-container { margin-top: 20px; overflow-x: auto; }
.table th, .table td { padding: 12px; text-align: left; font-size: 0.95rem; }
.table th { background-color: #007acc; color: white; }
.table-striped tbody tr:nth-child(odd) { background-color: #f7f7f7; }
.table-striped tbody tr:hover { background-color: #e0f1ff; }

/* Vertical buttons */
.status-buttons, .crud-buttons {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

footer { text-align: center; padding: 15px; margin-top: 30px; color: #666; font-size: 0.9rem; border-top: 1px solid #e0e0e0; }

/* Responsive */
@media (max-width: 992px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar.active { transform: translateX(0); }
    .main-content { margin-left: 0; }
    .menu-btn { display: block; }
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
<a href="manage staff.php" class="active"><i class="fas fa-users-cog"></i> Manage Staff</a>
<a href="upload report.php"><i class="fas fa-file-upload"></i> Upload Report</a>
<a href="view report.php"><i class="fas fa-file-pdf"></i> View Reports</a>
<a href="manage profile.php"><i class="fas fa-user-cog"></i> Manage Profile</a>
<a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content" id="main">
  <div class="navbar">
    <span>Manage Staff</span>
    <button class="menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
  </div>

  <div class="table-container">
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>No.</th>
          <th>Name</th>
          <th>Degree</th>
          <th>Email</th>
          <th>Contact</th>
          <th>Gender</th>
          <th>DOB</th>
          <th>Address</th>
          <th>Availability </th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
          <td><?php echo $i; ?></td>
          <td><?php echo $row['name']; ?></td>
          <td><?php echo $row['degree']; ?></td>
          <td><?php echo $row['email']; ?></td>
          <td><?php echo $row['number']; ?></td>
          <td><?php echo $row['gender']; ?></td>
          <td><?php echo $row['dob']; ?></td>
          <td><?php echo $row['address']; ?></td>
          <td>
            <div class="status-buttons">
              <a href="manage staff.php?status_id=<?php echo $row['id']; ?>&action=cancel"
                 class="btn btn-sm btn-warning <?php echo ($row['status'] == 'Not Available') ? 'disabled' : ''; ?>"
                 onclick="return confirm('Mark as Not Available?');">Cancel</a>
              <a href="manage staff.php?status_id=<?php echo $row['id']; ?>&action=retrieve"
                 class="btn btn-sm btn-success <?php echo ($row['status'] == 'Available') ? 'disabled' : ''; ?>"
                 onclick="return confirm('Mark as Available?');">Retrieve</a>
            </div>
          </td>
          <td>
            <div class="crud-buttons">
              <a href="edit staff.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
              <a href="manage staff.php?delete_id=<?php echo $row['id']; ?>"
                 class="btn btn-sm btn-danger"
                 onclick="return confirm('Delete this staff?');">Delete</a>
            </div>
          </td>
        </tr>
      <?php $i++; } ?>
      </tbody>
    </table>
  </div>

  <footer>
    &copy; 2025 Health Care. All Rights Reserved.
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
}
</script>
</body>
</html>
