<?php
include 'session-check.php';
include "connection.php"; 

if (isset($_POST['submit'])) {
    $patient_id = $_POST['patient'];
    $title = $_POST['title'];

    $folder = "reports/";
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $uploadedPaths = [];
    $total = count($_FILES['files']['name']); // number of files selected

    for ($i = 0; $i < $total; $i++) {
        if ($_FILES['files']['error'][$i] == 0) {
            $filename = $_FILES['files']['name'][$i];
            $tmpname = $_FILES['files']['tmp_name'][$i];
            $newName = time() . "_" . $filename; // unique name
            $destination = $folder . $newName;

            if (move_uploaded_file($tmpname, $destination)) {
                $uploadedPaths[] = $destination;
            }
        }
    }

    if (!empty($uploadedPaths)) {
        $allFiles = implode(",", $uploadedPaths);
        $query = "INSERT INTO reports (patient_id, title, file_path) 
                  VALUES ('$patient_id', '$title', '$allFiles')";
        mysqli_query($con, $query);
        echo "<script>alert('Reports uploaded successfully'); 
              window.location.href='upload report.php';</script>";
    } else {
        echo "<script>alert('No files uploaded');</script>";
    }
}

// Fetch patients
$query = "SELECT no, name FROM appointments";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;}
body{display:flex;min-height:100vh;background:#f4f9f9;color:#333;}

/* Sidebar */
.sidebar{
    width:220px;background:#007acc;color:#fff;flex-shrink:0;
    min-height:100vh;padding:20px 10px;position:fixed;top:0;left:0;
    transition:transform 0.3s ease-in-out;
}
.sidebar h2{text-align:center;margin-bottom:30px;}
.sidebar a{display:flex;align-items:center;padding:12px 15px;margin-bottom:10px;color:#fff;text-decoration:none;border-radius:6px;transition:0.3s;}
.sidebar a i{margin-right:10px;}
.sidebar a:hover,.sidebar a.active{background:#005f99;}

/* Main content */
.page-wrapper{display:flex;width:100%;}
.main-content{margin-left:220px;width:calc(100% - 220px);padding:20px;transition:margin-left 0.3s ease-in-out;}

/* Navbar */
.navbar{font-size:1.2rem;font-weight:600;color:#007acc;border-bottom:2px solid #e0e0e0;padding-bottom:10px;display:flex;justify-content:space-between;align-items:center;}

/* Toggle button on right */
.toggle-btn{
    display:none;background:#007acc;color:#fff;border:none;padding:8px 12px;
    border-radius:6px;cursor:pointer;font-size:1.2rem;
}

/* Card & Form */
.card{background:#fff;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.1);padding:20px;}
.form-label{color:#007acc;font-weight:600;}
.form-control,.form-select{border-radius:6px;border:1px solid #ccc;padding:10px;font-size:1rem;}
.form-control:focus,.form-select:focus{border-color:#007acc;box-shadow:0 0 5px rgba(0,122,204,0.3);}
.btn-primary{background:#007acc;border:none;padding:10px 20px;border-radius:6px;font-size:1rem;}
.btn-primary:hover{background:#005f99;}
footer{text-align:center;margin-top:20px;padding:15px;border-top:1px solid #ddd;color:#666;}

/* Responsive */
@media(max-width:992px){
    .sidebar{transform:translateX(-100%);position:fixed;z-index:1000;}
    .sidebar.active{transform:translateX(0);}
    .toggle-btn{display:inline-block;}
    .main-content{margin-left:0;width:100%;}
}
</style>
</head>
<body>

<div class="sidebar" id="sidebar">
  <h2>Health Care Admin</h2>
  <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="view appointment.php"><i class="fas fa-calendar-check"></i> Appointments</a>
  <a href="add staff.php"><i class="fas fa-user-plus"></i> Add Staff</a>
  <a href="manage staff.php"><i class="fas fa-users-cog"></i> Manage Staff</a>
  <a href="upload report.php" class="active"><i class="fas fa-file-upload"></i> Upload Report</a>
  <a href="view report.php"><i class="fas fa-file-pdf"></i> View Reports</a>
  <a href="manage profile.php"><i class="fas fa-user-cog"></i> Manage Profile</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
  <nav class="navbar mb-4">
    <span>Upload Report</span>
    <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
  </nav>

  <div class="container">
    <div class="card">
      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Select Patient</label>
          <select name="patient" class="form-select" required>
            <option value="" disabled selected>-- Select Patient --</option>
            <?php while($r = mysqli_fetch_assoc($result)) { ?>
              <option value="<?php echo $r['no']; ?>"><?php echo $r['name'] . ' (' . $r['no'] . ')'; ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Report Title</label>
          <input type="text" name="title" class="form-control" placeholder="Enter report title" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Upload Files</label>
          <input type="file" name="files[]" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png" required multiple>
          <small class="text-muted">You can upload multiple files</small>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Upload Reports</button>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
