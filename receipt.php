<?php
session_start();

// Check if user came from payment page or passed an ID
if (!isset($_SESSION['payment_receipt'])) {
    die("Invalid access. Please complete payment first.");
}

$payment_data = $_SESSION['payment_receipt'];

// Optional: If you want to fetch from DB using ID (for security)
// $appointment_id = $_GET['id'] ?? null;
// Fetch from DB if needed, otherwise use session
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - HealthCare Plus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 20px; }
        .receipt-container { max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.2); }
        h2 { text-align: center; color: #28a745; }
        .receipt-details { margin-top: 20px; }
        .detail-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #ddd; }
        .detail-item:last-child { border-bottom: none; }
        .btn-print { display: block; text-align: center; margin: 30px auto 0; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px; }
        .btn-print:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="receipt-container">
        <h2>Payment Receipt</h2>
        <p style="text-align:center;">Thank you for booking your appointment with <strong>HealthCare Plus</strong>.</p>

        <div class="receipt-details">
            <div class="detail-item">
                <span>Appointment ID:</span>
                <span>#<?= htmlspecialchars($payment_data['id'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <span>Patient Name:</span>
                <span><?= htmlspecialchars($payment_data['patient_name'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <span>Email:</span>
                <span><?= htmlspecialchars($payment_data['email'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <span>Contact Number:</span>
                <span><?= htmlspecialchars($payment_data['contact_number'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <span>Doctor:</span>
                <span><?= htmlspecialchars($payment_data['doctor_name'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <span>Appointment Date:</span>
                <span><?= isset($payment_data['appointment_date']) ? date('F d, Y', strtotime($payment_data['appointment_date'])) : 'N/A' ?></span>
            </div>
            <div class="detail-item">
                <span>Appointment Time:</span>
                <span><?= isset($payment_data['appointment_time']) ? date('g:i A', strtotime($payment_data['appointment_time'])) : 'N/A' ?></span>
            </div>
            <div class="detail-item">
                <span>Appointment Type:</span>
                <span><?= htmlspecialchars($payment_data['appointment_type'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <span>Amount Paid:</span>
                <span><?= isset($payment_data['amount']) ? number_format($payment_data['amount'], 2) : '0.00' ?> Taka</span>
            </div>
            <div class="detail-item">
                <span>Payment Method:</span>
                <span><?= htmlspecialchars($payment_data['method'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
                <span>Payment Status:</span>
                <span><?= htmlspecialchars($payment_data['pay_status'] ?? 'N/A') ?></span>
            </div>
        </div>

        <a href="#" class="btn-print" onclick="window.print();return false;"><i class="fas fa-print"></i> Print Receipt</a>
        <a href="index.php" class="btn-primary">
    <i class="fas fa-home"></i> Back to Home
</a>


    </div>
</body>
</html>
