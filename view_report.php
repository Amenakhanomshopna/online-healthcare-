<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['appointment_id'])) {
    echo "No appointment selected.";
    exit();
}

$appointment_id = intval($_GET['appointment_id']);

// Fetch appointment, user, doctor info
$sql = "SELECT a.*, d.name AS doctor_name, d.designation AS doctor_designation, d.specialty AS doctor_specialty,
        u.first_name, u.last_name, u.email AS user_email, u.phone AS user_phone, u.address AS user_address
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.id = ? AND a.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $appointment_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Appointment not found.";
    exit();
}

$appointment = $result->fetch_assoc();

// Fetch reports
$report_sql = "SELECT * FROM reports WHERE appointment_id = ?";
$report_stmt = $conn->prepare($report_sql);
$report_stmt->bind_param("i", $appointment_id);
$report_stmt->execute();
$report_result = $report_stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Appointment Report - HealthCare Plus</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<style>
body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
.container { max-width: 900px; margin: 30px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
.header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #007BFF; padding-bottom: 10px; margin-bottom: 20px; }
.header img { height: 60px; }
.header h2 { color: #007BFF; margin: 0; }
.info-section { margin-bottom: 20px; }
.info-section h3 { margin-bottom: 10px; color: #007BFF; }
.info-section p { margin: 3px 0; }
.report-card { border: 1px solid #ddd; border-radius: 6px; padding: 15px; margin-bottom: 15px; background: #f9f9f9; }
.report-card h4 { margin: 0 0 10px 0; color: #333; }
.actions { display: flex; justify-content: space-between; margin-top: 20px; }
button, .btn-back { background: #007BFF; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; }
button:hover, .btn-back:hover { background: #0056b3; }
@media print {
    .actions { display: none; }
}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="logo123.png" alt="HealthCare Plus Logo">
        <h2>Appointment Report</h2>
    </div>

    <div class="info-section">
        <h3>Patient Info</h3>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($appointment['user_email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($appointment['user_phone']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($appointment['user_address']); ?></p>
    </div>

    <div class="info-section">
        <h3>Doctor Info</h3>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($appointment['doctor_name']); ?></p>
        <p><strong>Designation:</strong> <?php echo htmlspecialchars($appointment['doctor_designation']); ?></p>
        <p><strong>Specialty:</strong> <?php echo htmlspecialchars($appointment['doctor_specialty']); ?></p>
    </div>

    <div class="info-section">
        <h3>Appointment Info</h3>
        <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($appointment['appointment_date'])); ?></p>
        <p><strong>Time:</strong> <?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></p>
        <p><strong>Status:</strong> <?php echo ucfirst($appointment['status']); ?></p>
    </div>

    <div class="info-section">
        <h3>Medical Reports</h3>
        <?php 
        if($report_result->num_rows > 0) {
            while($report = $report_result->fetch_assoc()) {
                echo '<div class="report-card">';
                echo '<h4>' . htmlspecialchars($report['report_title']) . '</h4>';
                echo '<p>' . nl2br(htmlspecialchars($report['report_details'])) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No reports available.</p>';
        }
        ?>
    </div>

    <div class="actions">
        <button onclick="window.print();"><i class="fas fa-print"></i> Print Report</button>
        <a href="view_appointments.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>
</body>
</html>
<?php
$stmt->close();
$report_stmt->close();
$conn->close();
?>
