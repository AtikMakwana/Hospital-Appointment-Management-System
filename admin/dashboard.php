<?php
include 'session-check.php';


include "connection.php";

// Fetch all upcoming appointments
$query = "SELECT * FROM appointments WHERE date >= CURDATE() ORDER BY date ASC, time ASC";
$sql = mysqli_query($con, $query);

// Total patients
$patient_count = "SELECT COUNT(*) AS total FROM appointments";
$count = mysqli_query($con, $patient_count);
$view_patient = mysqli_fetch_assoc($count);

// Today's appointments
$total_today_query = "SELECT COUNT(*) AS total_today FROM appointments WHERE date = CURDATE()";
$count_today = mysqli_query($con, $total_today_query);
$view_total_today = mysqli_fetch_assoc($count_today);

// Upcoming appointments
$upcoming_total_query = "SELECT COUNT(*) AS total_upcoming FROM appointments WHERE date >= CURDATE()";
$result_upcoming = mysqli_query($con, $upcoming_total_query);
$upcoming_count = mysqli_fetch_assoc($result_upcoming);

// Staff
$total_staff = "SELECT COUNT(*) AS total_upcoming FROM staff";
$staff = mysqli_query($con, $total_staff);
$count_staff = mysqli_fetch_assoc($staff);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hospital Admin Dashboard</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
/* Reset & Base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
body {
    display: flex;
    min-height: 100vh;
    background-color: #f4f9f9;
    color: #333;
    overflow-x: hidden;
}

/* Sidebar */
.sidebar {
    width: 220px;
    background-color: #007acc;
    color: #fff;
    flex-shrink: 0;
    min-height: 100vh;
    padding: 20px 10px;
    position: fixed;
    top: 0;
    left: 0;
    transition: left 0.3s ease;
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
    width: calc(100% - 220px);
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
    align-items: center;
    justify-content: space-between;
}

/* Hamburger Button */
.toggle-btn {
    display: none;
    font-size: 1.5rem;
    background: none;
    border: none;
    color: #007acc;
    cursor: pointer;
}

/* Cards */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.card {
    display: flex;
    align-items: center;
    padding: 20px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    transition: transform 0.2s ease;
}
.card:hover {
    transform: translateY(-3px);
}
.card i {
    font-size: 2rem;
    color: #007acc;
    margin-right: 15px;
}
.card-content h3 {
    margin: 0;
    font-size: 1.4rem;
    color: #333;
}
.card-content p {
    margin: 3px 0 0;
    font-size: 0.95rem;
    color: #666;
}

/* Table */
.table-container {
    width: 100%;
    overflow-x: auto;
}
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 30px;
}
table th, table td {
    padding: 12px 15px;
    text-align: left;
    font-size: 0.95rem;
    border-bottom: 1px solid #eaeaea;
    white-space: nowrap;
}
table th {
    background-color: #f2f8fc;
    color: #007acc;
    font-weight: 600;
}
table tr:hover {
    background-color: #f9f9f9;
}

/* Footer */
footer {
    text-align: center;
    padding: 15px;
    margin-top: 30px;
    color: #666;
    font-size: 0.9rem;
    border-top: 1px solid #e0e0e0;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        left: -220px;
    }
    .sidebar.active {
        left: 0;
    }
    .main-content {
        margin-left: 0;
        width: 100%;
        overflow-x: hidden;
    }
    .toggle-btn {
        display: block;
    }
}
@media (max-width: 480px) {
    .cards {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
    <h2>Health Care Admin</h2>
    <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="view appointment.php"><i class="fas fa-calendar-check"></i> Appointments</a>
    <a href="add staff.php"><i class="fas fa-user-plus"></i> Add Staff</a>
    <a href="manage staff.php"><i class="fas fa-users-cog"></i> Manage Staff</a>
    <a href="upload report.php"><i class="fas fa-file-upload"></i> Upload Report</a>
    <a href="view report.php"><i class="fas fa-file-pdf"></i> View Reports</a>
    <a href="manage profile.php"><i class="fas fa-user-cog"></i> Manage Profile</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="navbar">
        <span><h3>Dashboard</h3></span>
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
    </div>

    <h2 style="color: #007acc;">Overview</h2><br>
    <div class="cards">
        <div class="card">
            <i class="fas fa-user-injured"></i>
            <div class="card-content">
                <h3><?php echo $view_patient['total']; ?></h3>
                <p>Total Patients</p>
            </div>
        </div>
        <div class="card">
            <i class="fas fa-user-md"></i>
            <div class="card-content">
                <h3><?php echo $count_staff['total_upcoming'];?></h3>
                <p>Total Doctors</p>
            </div>
        </div>
        <div class="card">
            <i class="fas fa-calendar-day"></i>
            <div class="card-content">
                <h3><?php echo $view_total_today['total_today']; ?></h3>
                <p>Today's Appointments</p>
            </div>
        </div>
        <div class="card">
            <i class="fas fa-calendar-alt"></i>
            <div class="card-content">
                <h3><?php echo $upcoming_count['total_upcoming'];?></h3>
                <p>Upcoming Appointments</p>
            </div>
        </div>
    </div>

    <footer>
        &copy; 2025 Hospital Management System. All Rights Reserved.
    </footer>
</div>

<script>
function toggleSidebar() {
    document.querySelector(".sidebar").classList.toggle("active");
}
</script>
</body>
</html>
