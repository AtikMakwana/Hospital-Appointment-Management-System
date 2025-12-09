<?php
include 'session-check.php';
include "connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Patient Reports</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;}
body{display:flex;min-height:100vh;background:#f4f9f9;color:#333;}

/* Sidebar */
.sidebar{width:220px;background:#007acc;color:#fff;flex-shrink:0;min-height:100vh;padding:20px 10px;position:fixed;top:0;left:0;transition:transform 0.3s ease-in-out;}
.sidebar h2{text-align:center;margin-bottom:30px;}
.sidebar a{display:flex;align-items:center;padding:12px 15px;margin-bottom:10px;color:#fff;text-decoration:none;border-radius:6px;transition:0.3s;}
.sidebar a i{margin-right:10px;}
.sidebar a:hover,.sidebar a.active{background:#005f99;}

/* Toggle button */
.toggle-btn{display:none;background:#007acc;color:#fff;border:none;padding:8px 12px;border-radius:6px;cursor:pointer;font-size:1.2rem;}

/* Main content */
.main-content{margin-left:220px;padding:30px;width:calc(100% - 220px);transition:margin-left 0.3s ease-in-out;}
.page-title{color:#007acc;margin-bottom:20px;text-align:center;}

/* Card & Table */
.card{border-radius:8px;background:#fff;padding:20px;box-shadow:0 0 15px rgba(0,0,0,0.05);}
.table thead{background:#007acc;color:#fff;}
.table tbody tr:nth-child(even){background:#f2f9ff;}
.table td, .table th{vertical-align:middle;}

/* Buttons */
.btn-action{
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:5px 10px;
    border-radius:6px;
    color:#fff;
    text-decoration:none;
    font-size:0.9rem;
    transition:0.3s;
    margin:3px 3px 3px 0;
}
.btn-view{background:#007acc;color:#fff;}
.btn-view:hover{background:#005f99;color:#fff;}
.btn-download{background:#005f99;color:#fff;}
.btn-download:hover{background:#004d80;color:#fff;}

/* NEW: Themed button for forms */
.btn-theme{
    background:#007acc;
    color:#fff;
    font-weight:500;
}
.btn-theme:hover{
    background:#005f99;
    color:#fff;
}

/* Search bar */
.table-search {
    margin-bottom:15px;
    max-width:300px;
}

/* Responsive */
@media(max-width:768px){
    .sidebar{transform:translateX(-100%);position:fixed;z-index:1000;}
    .sidebar.active{transform:translateX(0);}
    .toggle-btn{display:inline-block;}
    .main-content{margin-left:0;width:100%;padding:15px;}
    
    table, thead, tbody, th, td, tr{display:block;width:100%;}
    thead tr{display:none;}
    tbody tr{margin-bottom:15px;background:#fff;border-radius:8px;padding:10px;box-shadow:0 0 5px rgba(0,0,0,0.05);}
    td{display:flex;justify-content:space-between;padding:5px 10px;border:none;flex-wrap:wrap;}
    td::before{content: attr(data-label);font-weight:600;color:#007acc;width:100%;}
    td:last-child .btn-action{display:inline-flex;width:auto;margin-bottom:5px;}
}
.footer{text-align:center;margin-top:40px;font-size:0.9rem;color:#555;}
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
    <a href="view report.php" class="active"><i class="fas fa-file-pdf"></i> View Reports</a>
    <a href="manage profile.php"><i class="fas fa-user-cog"></i> Manage Profile</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <nav class="navbar mb-4" style="display:flex;justify-content:space-between;align-items:center;">
        <span class="page-title"><h4>Patient Reports</h4></span>
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
    </nav>

<?php
// Fetch patients for dropdown
$name = "SELECT name,no FROM appointments";
$name_result = mysqli_query($con, $name);
?>
    <div class="card mb-4">
        <form method="POST" class="row g-3">
            <div class="col-12 col-md-6">
                <label for="id" class="form-label">Select Patient</label>
                <select class="form-select" id="id" name="id" required>
                    <option value="all">All Patients</option>
                    <?php while($row = mysqli_fetch_assoc($name_result)){ ?>
                    <option value="<?php echo $row['no'];?>"><?php echo $row['name']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-12 col-md-6 d-flex align-items-end">
                <button type="submit" name="submit" class="btn btn-theme w-100">View Reports</button>
            </div>
        </form>
    </div>

    <div class="card">
        <!-- Search bar -->
        <input type="text" id="searchInput" class="form-control table-search" placeholder="Search reports...">

        <table class="table table-striped table-bordered align-middle" id="reportTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Report Title</th>
                    <th>Files</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // Default: show all reports
            if(isset($_POST['submit'])){
                $id = $_POST['id'];
                if($id === "all"){
                    $query = "SELECT r.*, a.name 
                              FROM reports r 
                              JOIN appointments a ON r.patient_id = a.no 
                              ORDER BY r.id DESC";
                } else {
                    $id_safe = mysqli_real_escape_string($con, $id);
                    $query = "SELECT r.*, a.name 
                              FROM reports r 
                              JOIN appointments a ON r.patient_id = a.no 
                              WHERE r.patient_id = '$id_safe' 
                              ORDER BY r.id DESC";
                }
            } else {
                $query = "SELECT r.*, a.name 
                          FROM reports r 
                          JOIN appointments a ON r.patient_id = a.no 
                          ORDER BY r.id DESC";
            }

            $sql = mysqli_query($con, $query);

            if(mysqli_num_rows($sql) > 0){
                $count = 1;
                while($row2 = mysqli_fetch_assoc($sql)){
                    $files = explode(",", $row2['file_path']);
                    echo "<tr>
                            <td data-label='#'>{$count}</td>
                            <td data-label='Patient ID'>{$row2['patient_id']}</td>
                            <td data-label='Patient Name'>{$row2['name']}</td>
                            <td data-label='Report Title'>{$row2['title']}</td>
                            <td data-label='Files'>";
                    
                    foreach($files as $file){
                        $filename = basename($file);
                        echo "<a class='btn-action btn-view' href='$file' target='_blank'><i class='fas fa-eye'></i> $filename</a>";
                        echo "<a class='btn-action btn-download' href='$file' download><i class='fas fa-download'></i></a>";
                    }

                    echo "</td></tr>";
                    $count++;
                }
            } else {
                echo "<tr><td colspan='5' class='text-center text-danger'>No reports found</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <div class="footer">&copy; 2025 Health Care. All Rights Reserved.</div>
</div>

<script>
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('active');
}

// Search filter
document.getElementById("searchInput").addEventListener("keyup", function() {
  var value = this.value.toLowerCase();
  var rows = document.querySelectorAll("#reportTable tbody tr");
  rows.forEach(function(row) {
    row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
  });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
