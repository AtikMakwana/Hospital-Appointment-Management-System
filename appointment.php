<?php
// Include database connection
include 'connection.php';

// Include Composer autoloader for PHPMailer
require __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Appointment - Health Care</title>
  <link rel="shortcut icon" href="health.png" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="common.css">
</head>
<body>

  <!-- ✅ Same Navbar as index.php -->
  <nav>
    <div class="logo-container">
      <img src="https://image.pngaaa.com/72/873072-middle.png" alt="HealthCare Logo">
      <h1>Health Care</h1>
    </div>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="appointment.php" class="active">Book Appointment</a></li>
      <li><a href="index.php#features">Features</a></li>
      <li><a href="about.php">About Us</a></li>
    </ul>
  </nav>

  <div class="appointment-form">
    <h2>Book Appointment</h2>

    <!-- Step 1: Select Doctor & Date -->
    <form method="GET" class="form-group">
      <label>Doctor:</label>
      <select name="doctor" required <?php if(isset($_GET['doctor'])) echo "disabled"; ?>>
        <option value="" disabled selected>Select Doctor</option>
        <option value="Dr. Sharma" <?php if(isset($_GET['doctor']) && $_GET['doctor']=="Dr. Sharma") echo "selected"; ?>>Dr. Sharma</option>
        <option value="Dr. Mehta" <?php if(isset($_GET['doctor']) && $_GET['doctor']=="Dr. Mehta") echo "selected"; ?>>Dr. Mehta</option>
        <option value="Dr. Patel" <?php if(isset($_GET['doctor']) && $_GET['doctor']=="Dr. Patel") echo "selected"; ?>>Dr. Patel</option>
      </select>

      <label>Date:</label>
      <input type="date" name="date" min="<?php echo date('Y-m-d');?>"
             value="<?php echo isset($_GET['date']) ? $_GET['date'] : ''; ?>"
             <?php if(isset($_GET['date'])) echo "disabled"; ?> required>

      <?php if(!isset($_GET['doctor']) && !isset($_GET['date'])){ ?>
          <button type="submit">Check Available Time</button>
      <?php } ?>
    </form>

    <?php
    if(isset($_GET['doctor']) && isset($_GET['date'])){
        $doctor = $_GET['doctor'];
        $date = $_GET['date'];

        $all_slots = [
            "10:00 AM", "10:30 AM",
            "11:00 AM", "11:30 AM", "12:00 PM", "12:30 PM",
            "01:00 PM", "01:30 PM", "02:00 PM", "02:30 PM",
            "03:00 PM", "03:30 PM", "04:00 PM", "04:30 PM",
            "05:00 PM"
        ];

        // Fetch already booked slots
        $stmt = $con->prepare("SELECT time FROM appointments WHERE date=? AND doctor=?");
        $stmt->bind_param("ss", $date, $doctor);
        $stmt->execute();
        $result = $stmt->get_result();
        $booked = [];
        while($row = $result->fetch_assoc()){
            $booked[] = $row['time'];
        }

        $available = array_diff($all_slots, $booked);

        if(empty($available)){
            echo "<p class='msg'>❌ No slots available for $doctor on $date</p>";
        } else {
    ?>

    <!-- Step 2: Enter patient details -->
    <form method="POST" class="form-group">
      <input type="hidden" name="doctor" value="<?php echo $doctor; ?>">
      <input type="hidden" name="date" value="<?php echo $date; ?>">

      <label>Patient Name:</label>
      <input type="text" name="name" placeholder="Enter Name" oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" required>

      <label>Email:</label>
      <input type="email" name="email" placeholder="Enter Email" required>

      <label>Gender:</label>
      <div class="gender-options">
        <label><input type="radio" name="gender" value="Male" required> Male</label>
        <label><input type="radio" name="gender" value="Female" required> Female</label>
        <label><input type="radio" name="gender" value="Other" required> Other</label>
      </div>

      <label>Phone no:</label>
      <input type="text" name="phone" placeholder="Enter Phone no." maxlength="10"
             oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>

      <label>Available Time:</label>
      <select name="time" required>
        <option value="" disabled selected>Select Time</option>
        <?php foreach($available as $slot){ ?>
          <option value="<?php echo $slot; ?>"><?php echo $slot; ?></option>
        <?php } ?>
      </select>

      <button type="submit" name="submit">Book Appointment</button>
    </form>

    <?php }} ?>

<?php
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $doctor = $_POST['doctor'];

    // Check if slot is available
    $check = $con->prepare("SELECT * FROM appointments WHERE date=? AND doctor=? AND time=?");
    $check->bind_param("sss", $date, $doctor, $time);
    $check->execute();
    $res = $check->get_result();

    if($res->num_rows > 0){
        echo "<script>alert('❌ This slot has already been booked. Please select another slot.');</script>";
    } else {
        $stmt = $con->prepare("INSERT INTO appointments (name, email, gender, mobile, date, time, doctor) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $email, $gender, $phone, $date, $time, $doctor);

        if($stmt->execute()){
            // Send email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'atikwork1409@gmail.com';
                $mail->Password   = 'bgvu lqyy vlao czym';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('atikwork1409@gmail.com', 'Hospital Admin');
                $mail->addAddress($email, $name);

                $mail->isHTML(true);
                $mail->Subject = 'Patient Appointment Details';
                $mail->Body    = "
                    <h2>Patient Appointment Details</h2>
                    <p><strong>Dear </strong> $name,</p>
                    <p>Thank you for booking your appointment with Health Care Center. Here are your appointment details:</p>
                    <p><strong>Name:</strong> $name</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Mobile:</strong> $phone</p>
                    <p><strong>Gender:</strong> $gender</p>
                    <p><strong>Date:</strong> $date</p>
                    <p><strong>Time:</strong> $time</p>
                    <p><strong>Doctor:</strong> $doctor</p>
                    <p>Please arrive 10 minutes before your scheduled appointment. If you need to reschedule or cancel, kindly contact us at atikwork1409@gmail.com.</p>
                    <p>We look forward to seeing you!</p>
                    <p>Best regards,</p>
                    <p>Health Care Center</p>
                ";

                $mail->send();
                echo "<script>alert('✅ Appointment booked successfully! Confirmation sent to $email'); window.location.href='appointment.php';</script>";
                
            } catch (Exception $e) {
                echo "<script>alert('⚠️ Appointment booked but email could not be sent. Error: {$mail->ErrorInfo}');</script>";
            }
        } else {
            echo "<script>alert('❌ Error booking appointment: ".$stmt->error."');</script>";
        }
    }
}
?>
  </div>

  <footer>
    <p>&copy; 2025 HealthCare. All Rights Reserved.</p>
  </footer>

</body>
</html>
