<?php
include 'session-check.php';


include 'connection.php';

if(isset($_POST['submit'])) { 
    $doctor = $_POST['doctor'];

    if($doctor === "all") {
        $query = "SELECT * FROM appointments 
                  WHERE date >= CURDATE()
                  ORDER BY STR_TO_DATE(time, '%h:%i %p') ASC";
    } else {
        $doctor_safe = mysqli_real_escape_string($con, $doctor);
        $query = "SELECT * FROM appointments 
                  WHERE doctor = '$doctor_safe' AND date >= CURDATE()
                  ORDER BY STR_TO_DATE(time, '%h:%i %p') ASC";
    }

    $sql = mysqli_query($con, $query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Hospital Appointment Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet">

<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

body { display: flex; flex-direction: column; min-height: 100vh; background-color: #f4f9f9; }

/* Sidebar */
.sidebar {
    width: 220px;
    background-color: #007acc;
    color: #fff;
    position: fixed;
    top: 0;
    left: 0;
    min-height: 100vh;
    padding: 20px 10px;
    transition: all 0.3s ease;
    z-index: 999;
}

.sidebar h2 { text-align: center; margin-bottom: 30px; font-size: 1.5rem; }

.sidebar a {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    margin-bottom: 10px;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    transition: background 0.3s;
}

.sidebar a i { margin-right: 10px; }

.sidebar a:hover,
.sidebar a.active { background-color: #005f99; }

/* Overlay */
#overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
    z-index: 998;
    transition: all 0.3s;
}

/* Main Content */
.main-content {
    margin-left: 220px;
    padding: 20px 25px;
    transition: margin-left 0.3s;
    width: calc(100% - 220px);
}

.main-content.full { margin-left: 0; width: 100%; }

/* Toggle Button on right */
#sidebarToggle {
    display: none;
    position: fixed;
    top: 15px;
    right: 15px;
    font-size: 1.5rem;
    background-color: #007acc;
    color: #fff;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    z-index: 1000;
    transition: 0.3s;
}

/* Card */
.card { background-color: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.05); margin-bottom: 20px; }

/* Form */
.form-label { font-weight: 600; color: #007acc; }

/* Buttons */
.btn-dark { background-color: #007acc; border: none; color: #fff; transition: 0.3s; }
.btn-dark:hover { background-color: #005f99; }

/* Table */
table.dataTable thead { background-color: #007acc; color: #fff; }
table.dataTable tbody tr:nth-child(even) { background-color: #f2f9ff; }
table.dataTable tbody tr:hover { background-color: #e6f2ff; }
.btn-action { display: inline-block; margin-right: 5px; }

/* DataTable spacing */
.dataTables_wrapper .dataTables_filter { margin-bottom: 15px; } /* search box */
.dataTables_wrapper .dt-buttons { margin-top: 10px; margin-bottom: 10px; } /* buttons */

/* Footer */
.footer { text-align: center; padding: 15px 0; font-size: 0.9rem; color: #555; border-top: 1px solid #ccc; margin-top: 20px; }

/* Responsive */
@media (max-width: 992px) {
    .sidebar { left: -220px; }
    .sidebar.active { left: 0; }
    .main-content { margin-left: 0; width: 100%; }
    #sidebarToggle { display: block; }
    .table-responsive { overflow-x: auto; }
    .btn-action { display: block; width: 100%; margin-bottom: 5px; }
    #overlay.active { display: block; }
}

@media (max-width: 576px) {
    .card { padding: 15px; }
    .form-label { font-size: 0.9rem; }
    .btn-dark { font-size: 0.9rem; padding: 8px; }
    #sidebarToggle { font-size: 1.2rem; padding: 6px 10px; }
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dt-buttons { margin-bottom: 12px; margin-top: 8px; }
}
</style>
</head>
<body>

<!-- Toggle Button -->
<button id="sidebarToggle"><i class="fas fa-bars"></i></button>

<!-- Overlay -->
<div id="overlay"></div>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Health Care Admin</h2>
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="view appointment.php" class="active"><i class="fas fa-calendar-check"></i> Appointments</a>
    <a href="add staff.php"><i class="fas fa-user-plus"></i> Add Staff</a>
    <a href="manage staff.php"><i class="fas fa-users-cog"></i> Manage Staff</a>
    <a href="upload report.php"><i class="fas fa-file-upload"></i> Upload Report</a>
    <a href="view report.php"><i class="fas fa-file-pdf"></i> View Reports</a>
    <a href="manage profile.php"><i class="fas fa-user-cog"></i> Manage Profile</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>

</div>

<!-- Main Content -->
<div class="main-content">
    <h2>View Appointments</h2>

    <div class="card">
        <form method="POST" class="row g-3">
            <div class="col-12 col-md-6">
                <label for="doctor" class="form-label">Select Doctor</label>
                <select class="form-select" id="doctor" name="doctor" required>
                    <option value="all">All Doctors</option>
                    <option value="Dr. Sharma">Dr. Sharma</option>
                    <option value="Dr. Mehta">Dr. Mehta</option>
                    <option value="Dr. Patel">Dr. Patel</option>
                </select>
            </div>
            <div class="col-12 col-md-6 d-flex align-items-end">
                <button type="submit" name="submit" class="btn btn-dark w-100">View Appointments</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table id="appointmentsTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Mobile No.</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Doctor</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if(isset($sql) && mysqli_num_rows($sql) > 0){
                $i = 1;
                while($row = mysqli_fetch_assoc($sql)){ 
                    $formattedDate = date("d-m-Y", strtotime($row['date']));
                    echo "<tr>
                            <td>{$i}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['gender']}</td>
                            <td>{$row['mobile']}</td>
                            <td>{$formattedDate}</td>
                            <td>{$row['time']}</td>
                            <td>{$row['doctor']}</td>
                          </tr>";
                    $i++;
                }
            }
            ?>
            </tbody>
        </table>
    </div>

    <footer class="footer">
        &copy; 2025 Hospital Management System. All Rights Reserved.
    </footer>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#appointmentsTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['csv', 'excel', 'pdf', 'print'],
        responsive: true,
        autoWidth: false,
        columnDefs: [
            { orderable: false, targets: '_all' }
        ]
    });

    // Sidebar toggle
    $('#sidebarToggle').click(function() {
        $('.sidebar').toggleClass('active');
        $('.main-content').toggleClass('full');
        $('#overlay').toggleClass('active');
    });

    // Click overlay to close sidebar
    $('#overlay').click(function(){
        $('.sidebar').removeClass('active');
        $('.main-content').removeClass('full');
        $(this).removeClass('active');
    });
});
</script>

</body>
</html>
