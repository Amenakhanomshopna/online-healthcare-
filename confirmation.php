<?php
session_start();

// Check if user came from payment page
if (!isset($_SESSION['payment_receipt'])) {
    die("Invalid access. Please complete payment first.");
}

$payment_data = $_SESSION['payment_receipt'];
// Keep session if user wants to view receipt multiple times
// unset($_SESSION['payment_receipt']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - HealthCare Plus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="book_appointment.css">
    <style>
        .confirmation-container {
            display: flex;
            justify-content: center;
            margin-top: 50px;
        }
        .confirmation-box {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            width: 450px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            text-align: center;
        }
        .confirmation-icon i {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 15px;
        }
        .confirmation-details {
            text-align: left;
            margin-top: 20px;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
        }
        .action-buttons a.btn-primary, 
        .action-buttons a.btn-secondary {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            color: #fff;
        }
        .btn-primary { background-color: #28a745; }
        .btn-secondary { background-color: #007bff; }
    </style>
</head>
<body>
    <!-- Header -->
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
        <!-- User is logged in -->
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
    <?php else: ?>
        <!-- User not logged in -->
        <li><a href="login.php">Login</a></li>
        <li><a href="signup.php">Signup</a></li>
    <?php endif; ?>
</ul>

                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content - Confirmation -->
    <main class="confirmation-container">
        <div class="confirmation-box">
            <div class="confirmation-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Payment Confirmed!</h2>
            <p>Your appointment has been successfully booked. Thank you for choosing HealthCare Plus!</p>

            <!-- Booking Summary -->
            <div class="confirmation-details">
                <h3>Booking Summary</h3>
                <div class="detail-item">
                    <span>Appointment ID:</span>
                    <span>#<?= htmlspecialchars($payment_data['id'] ?? 'N/A') ?></span>
                </div>
                <div class="detail-item">
                    <span>Doctor:</span>
                    <span><?= htmlspecialchars($payment_data['doctor_name'] ?? 'N/A') ?></span>
                </div>
                <div class="detail-item">
                    <span>Date & Time:</span>
                    <span>
                        <?= isset($payment_data['appointment_date']) ? date('F d, Y', strtotime($payment_data['appointment_date'])) : 'N/A' ?>, 
                        <?= isset($payment_data['appointment_time']) ? date('g:i A', strtotime($payment_data['appointment_time'])) : 'N/A' ?>
                    </span>
                </div>
                <div class="detail-item">
                    <span>Amount Paid:</span>
                    <span><?= isset($payment_data['amount']) ? number_format($payment_data['amount'], 2) : '0.00' ?> Taka</span>
                </div>
                <div class="detail-item">
                    <span>Payment Method:</span>
                    <span><?= htmlspecialchars($payment_data['method'] ?? 'N/A') ?></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons" style="margin-top:20px;">
                <!-- View Receipt Button -->
                <a href="receipt.php?id=<?= htmlspecialchars($payment_data['id'] ?? '') ?>" class="btn-primary" target="_blank">
                    <i class="fas fa-receipt"></i> View Receipt
                </a>
                <!-- Back to Home Button -->
                <a href="index.php" class="btn-secondary" style="margin-left:10px;">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
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
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 HealthCare Plus. All rights reserved. Created by Amena Khanom Shopna</p>
            </div>
        </div>
    </footer>
</body>
</html>
