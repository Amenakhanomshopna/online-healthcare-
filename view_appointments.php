<?php 
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Appointments - HealthCare Plus</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="view_appointments.css">
<style>
/* Card system styling */
.appointments-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}
.appointment-card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
}
.appointment-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin-bottom: 15px;
}
.appointment-header div {
    margin-right: 10px;
}
.appointment-details p {
    margin: 5px 0;
}
.status-scheduled { color: #ff9800; font-weight: bold; }
.status-completed { color: #4caf50; font-weight: bold; }
.status-cancelled { color: #f44336; font-weight: bold; }

.appointment-actions {
    margin-top: 15px;
}
.action-button {
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    color: #fff;
    text-decoration: none;
    font-size: 14px;
    margin-right: 10px;
}
.cancel { background-color: #f44336; }
.view-report { background-color: #2196f3; }
</style>
</head>
<body>

<header>
<div class="container">
    <div class="header-content">
        <div class="logo">
            <img src="logo123.png" alt="HealthCare Plus Logo">
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="doctors.php">Doctors</a></li>
                <li><a href="services.php">Services</a></li>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Signup</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>
</header>

<main class="appointments-container">
    <div class="filters-section">
        <div class="filter-header">
            <h3><i class="fas fa-calendar-check"></i> My Appointments</h3>
        </div>

        <div class="appointments-list">
        <?php
        $sql = "SELECT a.*, d.name AS doctor_name FROM appointments a 
                JOIN doctors d ON a.doctor_id = d.id 
                WHERE a.user_id = ? 
                ORDER BY a.appointment_date DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $statusClass = '';
                switch ($row['status']) {
                    case 'scheduled': $statusClass = 'status-scheduled'; break;
                    case 'done': case 'completed': $statusClass = 'status-completed'; break;
                    case 'cancelled': $statusClass = 'status-cancelled'; break;
                }

                echo '<div class="appointment-card">';
                echo '<div class="appointment-header">';
                echo '<div><strong>Doctor:</strong> ' . htmlspecialchars($row['doctor_name']) . '</div>';
                echo '<div><strong>Date:</strong> ' . date('F j, Y', strtotime($row['appointment_date'])) . '</div>';
                echo '<div><strong>Time:</strong> ' . date('g:i A', strtotime($row['appointment_time'])) . '</div>';
                echo '<div class="' . $statusClass . '"><strong>Status:</strong> ' . ucfirst($row['status']) . '</div>';
                echo '</div>';

                echo '<div class="appointment-details">';
                echo '<p><strong>Contact:</strong> ' . htmlspecialchars($row['contact_number']) . '</p>';
                echo '<p><strong>Email:</strong> ' . htmlspecialchars($row['email']) . '</p>';
                echo '<p><strong>Appointment Type:</strong> ' . ucfirst($row['appointment_type']) . '</p>';
                if (!empty($row['notes'])) echo '<p><strong>Notes:</strong> ' . htmlspecialchars($row['notes']) . '</p>';
                echo '</div>';

                echo '<div class="appointment-actions">';
                if ($row['status'] == 'scheduled') {
                    echo '<form method="POST" action="cancel_appointment.php" style="display:inline-block;">';
                    echo '<input type="hidden" name="appointment_id" value="' . $row['id'] . '">';
                    echo '<button type="submit" class="action-button cancel"><i class="fas fa-times"></i> Cancel</button>';
                    echo '</form>';
                } elseif ($row['status'] == 'done' || $row['status'] == 'completed') {
    echo '<a href="view_report.php?appointment_id=' . $row['id'] . '" class="action-button view-report">
          <i class="fas fa-file-medical-alt"></i> View Report</a>';
}

                echo '</div>';

                echo '</div>'; // card
            }
        } else {
            echo '<div class="no-appointments">';
            echo '<i class="fas fa-calendar-times"></i>';
            echo '<p>No appointments found</p>';
            echo '</div>';
        }

        $stmt->close();
        $conn->close();
        ?>
        </div>
    </div>
</main>

<footer>
<div class="container">
    <div class="footer-content">
        <div class="footer-column">
            <h3>HealthCare Plus</h3>
            <p>Providing comprehensive healthcare solutions with compassion and excellence. Your health is our priority.</p>
        </div>
        <div class="footer-column">
            <h3>Quick Links</h3>
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="doctors.php">Doctors</a></li>
                <li><a href="#">Contact Us</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>Contact Info</h3>
            <ul class="footer-links">
                <li><i class="fas fa-map-marker-alt"></i> 123 Health Street, Medical City, HC 12345</li>
                <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                <li><i class="fas fa-envelope"></i> info@healthcareplus.com</li>
                <li><i class="fas fa-clock"></i> Mon-Fri: 8AM-8PM, Sat-Sun: 9AM-5PM</li>
            </ul>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; 2025 HealthCare Plus. All rights reserved. Created by Amena Khanom Shopna</p>
    </div>
</div>
</footer>

</body>
</html>
