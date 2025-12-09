<?php
require __DIR__ . '/vendor/autoload.php'; // include Composer autoloader
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['send'])) {
    $patient_name = $_POST['name'];
    $patient_email = $_POST['email'];
    $patient_mobile = $_POST['mobile'];
    $patient_gender = $_POST['gender'];
    $patient_date = $_POST['date'];
    $patient_time = $_POST['time'];
    $doctor = $_POST['doctor'];

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = // add your details
        $mail->SMTPAuth   = // add your details
        $mail->Username   = // add your details
        $mail->Password   = // add your details
        $mail->SMTPSecure = // add your details
        $mail->Port       =// add your details

        // Sender
        $mail->setFrom('your email', 'Hospital Admin');

        // Recipient: patient email from the form
        $mail->addAddress($patient_email, $patient_name);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Patient Appointment Details';
        $mail->Body    = "
            <h2>Patient Appointment Details</h2>
            <p><strong>Name:</strong> $patient_name</p>
            <p><strong>Email:</strong> $patient_email</p>
            <p><strong>Mobile:</strong> $patient_mobile</p>
            <p><strong>Gender:</strong> $patient_gender</p>
            <p><strong>Date:</strong> $patient_date</p>
            <p><strong>Time:</strong> $patient_time</p>
            <p><strong>Doctor:</strong> $doctor</p>
        ";

        $mail->send();
        echo "<p style='color:green;'>Mail has been sent successfully to $patient_email!</p>";
    } catch (Exception $e) {
        echo "<p style='color:red;'>Mail could not be sent. Error: {$mail->ErrorInfo}</p>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Send Patient Details</title>
</head>
<body>
<h2>Send Patient Appointment Details</h2>
<form method="POST">
    <input type="text" name="name" placeholder="Patient Name" required><br><br>
    <input type="email" name="email" placeholder="Patient Email" required><br><br>
    <input type="text" name="mobile" placeholder="Mobile Number" required><br><br>
    <select name="gender" required>
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select><br><br>
    <input type="date" name="date" required><br><br>
    <input type="time" name="time" required><br><br>
    <input type="text" name="doctor" placeholder="Doctor Name" required><br><br>
    <button type="submit" name="send">Send Email</button>
</form>
</body>
</html>

