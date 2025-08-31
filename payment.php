<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$error_message = '';
$success_message = '';
// Include database
require_once 'db.php';

// Initialize variables
$patient_name = $contact_number = $email = $doctor_id = '';
$appointment_date = $appointment_time = $appointment_type = $notes = '';
$doctor_name = '';

// Check appointment session
if (!isset($_SESSION['appointment_data'])) {
    die("No appointment data found. Please book an appointment first.");
}

$appointment_data = $_SESSION['appointment_data'];
$patient_name = $appointment_data['patient_name'] ?? '';
$contact_number = $appointment_data['contact_number'] ?? '';
$email = $appointment_data['email'] ?? '';
$doctor_id = $appointment_data['preferred_doctor'] ?? '';
$appointment_date = $appointment_data['appointment_date'] ?? '';
$appointment_time = $appointment_data['appointment_time'] ?? '';
$appointment_type = $appointment_data['appointment_type'] ?? '';
$notes = $appointment_data['notes'] ?? '';

// Get doctor name
if ($doctor_id) {
    $stmt = $conn->prepare("SELECT name FROM doctors WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $stmt->bind_result($doctor_name);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Prepare failed: " . $conn->error);
    }
}

// Set fixed fee and total calculation
$base_fee = 500.00;  // Fixed for all appointment types
$online_charge = $base_fee * 0.02; // 2% online payment charge
$total_amount = $base_fee + $online_charge;

// Handle payment POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'] ?? 'bkash';
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        die("User not logged in!");
    }

    // Insert appointment
    $stmt = $conn->prepare("
        INSERT INTO appointments 
        (user_id, doctor_id, appointment_date, appointment_time, contact_number, email, appointment_type, notes, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'scheduled')
    ");

    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param(
        "iissssss", 
        $user_id, 
        $doctor_id, 
        $appointment_date, 
        $appointment_time, 
        $contact_number, 
        $email, 
        $appointment_type, 
        $notes
    );

    if ($stmt->execute()) {
        $appointment_id = $stmt->insert_id;
        $stmt->close();

        // Insert payment
        $stmt_pay = $conn->prepare("
            INSERT INTO payments 
            (appointment_id, user_id, amount, payment_method, payment_status) 
            VALUES (?, ?, ?, ?, 'Completed')
        ");

        if (!$stmt_pay) die("Prepare failed: " . $conn->error);

        $stmt_pay->bind_param("idds", $appointment_id, $user_id, $total_amount, $payment_method);

        if ($stmt_pay->execute()) {
            // Store payment data including appointment ID for confirmation page
            $_SESSION['payment_receipt'] = [
                'id' => $appointment_id,
                'patient_name' => $patient_name,
                'email' => $email,
                'contact_number' => $contact_number,
                'doctor_name' => $doctor_name,
                'appointment_date' => $appointment_date,
                'appointment_time' => $appointment_time,
                'appointment_type' => $appointment_type,
                'amount' => $total_amount,
                'method' => ucfirst($payment_method),
                'pay_status' => 'Completed'
            ];

            // Clear appointment session
            unset($_SESSION['appointment_data']);

            // Redirect to confirmation page
            header("Location: confirmation.php");
            exit;
        } else {
            die("Payment insert failed: " . $stmt_pay->error);
        }

        $stmt_pay->close();
    } else {
        die("Appointment insert failed: " . $stmt->error);
    }
}

// Load doctors list for right panel
$doctors = [];
$doctor_result = $conn->query("SELECT id, name, specialty, image_url FROM doctors");
if ($doctor_result && $doctor_result->num_rows > 0) {
    $doctors = $doctor_result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page - HealthCare Plus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></link>
    <link rel="stylesheet" href="book_appointment.css">
    <link rel="stylesheet" href="payment.css">
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
    <!-- Main Content - Payment Page -->
    <main class="payment-container">
        <div class="form-section">
            <div class="form-header">
                <h2>Payment Details</h2>
                <p>Please review your appointment details and proceed with payment</p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>
            
            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                <div class="bill-summary">
    <h3>Bill Summary</h3>
    <div class="details-cards">
        <div class="detail-card">
            <h4>Patient Information</h4>
            <div class="detail-item">
                <label>Name:</label>
                <span><?= htmlspecialchars($patient_name) ?></span>
            </div>
            <div class="detail-item">
                <label>Phone:</label>
                <span><?= htmlspecialchars($contact_number) ?></span>
            </div>
            <div class="detail-item">
                <label>Email:</label>
                <span><?= htmlspecialchars($email) ?></span>
            </div>
        </div>
        
        <div class="detail-card">
            <h4>Appointment Details</h4>
            <div class="detail-item">
                <label>Doctor:</label>
                <span><?= htmlspecialchars($doctor_name) ?></span>
            </div>
            <div class="detail-item">
                <label>Date:</label>
                <span><?= date('F d, Y', strtotime($appointment_date)) ?></span>
            </div>
            <div class="detail-item">
                <label>Time:</label>
                <span><?= date('g:i A', strtotime($appointment_time)) ?></span>
            </div>
            <div class="detail-item">
                <label>Type:</label>
                <span><?= ucfirst($appointment_type) ?></span>
            </div>
        </div>
    </div>
</div>

                 <div class="pricing-table">
    <table>
        <tr>
            <td><?= ucfirst($appointment_type) ?> Fee</td>
            <td><?= number_format($base_fee, 2) ?> Taka</td>
        </tr>
        <tr class="total-row">
            <td>Total Amount (including 2% online charge)</td>
            <td><?= number_format($total_amount, 2) ?> Taka</td>
        </tr>
    </table>
</div>




                
                <div class="payment-methods">
                    <h3>Payment Method</h3>
                    <div class="method-options">
                        <div class="method-option">
                            <input type="radio" id="bkash" name="payment_method" value="bkash" checked>
                            <label for="bkash">
                                <i class="fas fa-mobile-alt"></i>
                                Bkash
                            </label>
                        </div>
                        <div class="method-option">
                            <input type="radio" id="nagad" name="payment_method" value="nagad">
                            <label for="nagad">
                                <i class="fas fa-mobile-alt"></i>
                                Nagad
                            </label>
                        </div>
                        <div class="method-option">
                            <input type="radio" id="rocket" name="payment_method" value="rocket">
                            <label for="rocket">
                                <i class="fas fa-rocket"></i>
                                Rocket
                            </label>
                        </div>
                    </div>
                    
                    <!-- Bkash -->
                    <div class="mobile-payment" id="bkash_details">
                        <div class="form-group">
                            <label for="bkash_number" class="form-label">Bkash Number</label>
                            <input type="text" id="bkash_number" name="bkash_number" class="form-input" placeholder="Enter Bkash number" required>
                        </div>
                    </div>
                    
                    <!-- Nagad -->
                    <div class="mobile-payment" id="nagad_details" style="display:none;">
                        <div class="form-group">
                            <label for="nagad_number" class="form-label">Nagad Number</label>
                            <input type="text" id="nagad_number" name="nagad_number" class="form-input" placeholder="Enter Nagad number" required>
                        </div>
                    </div>
                    
                    <!-- Rocket -->
                    <div class="mobile-payment" id="rocket_details" style="display:none;">
                        <div class="form-group">
                            <label for="rocket_number" class="form-label">Rocket Number</label>
                            <input type="text" id="rocket_number" name="rocket_number" class="form-input" placeholder="Enter Rocket number" required>
                        </div>
                    </div>
                </div>
                
                <div class="terms-agreement">
                    <input type="checkbox" id="terms_agreed" name="terms_agreed" required>
                    <label for="terms_agreed">I agree to the <a href="#" target="_blank">Terms and Conditions</a> and <a href="#" target="_blank">Privacy Policy</a></label>
                </div>
                
                <button type="submit" class="btn-primary">Pay Now - <?= number_format($total_amount, 2) ?> Taka</button>
            </form>
        </div>
        
        <div class="right-panel">
            <div class="available-doctors">
                <div class="summary-header">
                    <h3>
                        <i class="fas fa-user-md"></i>
                        Available Doctors
                    </h3>
                </div>
                
                <div class="doctor-list">
                    <?php foreach ($doctors as $doc): ?>
                        <div class="doctor-card">
                            <img src="<?= htmlspecialchars($doc['image_url']) ?>" alt="<?= htmlspecialchars($doc['name']) ?>" class="doctor-image">
                            <div class="doctor-info">
                                <div class="doctor-name"><?= htmlspecialchars($doc['name']) ?></div>
                                <div class="doctor-specialty"><?= htmlspecialchars($doc['specialty']) ?></div>
                                <div class="doctor-rating">
                                    <i class="fas fa-star star"></i>
                                    <i class="fas fa-star star"></i>
                                    <i class="fas fa-star star"></i>
                                    <i class="fas fa-star star"></i>
                                    <i class="fas fa-star-half-alt star"></i>
                                    <span class="rating-score">(4.5)</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
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
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
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
                
                <div class="footer-column">
                    <h3>Newsletter</h3>
                    <p>Subscribe to our newsletter for the latest health tips and updates.</p>
                    <form>
                        <input type="email" placeholder="Your email address" style="padding: 10px; width: 100%; margin-bottom: 10px; border-radius: 5px; border: none;">
                        <button type="submit" class="btn-primary" style="width: 100%;">Subscribe</button>
                    </form>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2023 HealthCare Plus. All rights reserved. Created by Amena Khanom Shopna</p>
            </div>
        </div>
    </footer>
    
    <script>
     document.addEventListener('DOMContentLoaded', function() {
    // Payment method switcher
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const bkashDetails = document.getElementById('bkash_details');
    const nagadDetails = document.getElementById('nagad_details');
    const rocketDetails = document.getElementById('rocket_details');
    
    function updatePaymentMethodDisplay() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        // Hide all sections
        bkashDetails.style.display = 'none';
        nagadDetails.style.display = 'none';
        rocketDetails.style.display = 'none';
        
        // Remove required from all inputs
        [bkashDetails, nagadDetails, rocketDetails].forEach(section => {
            section.querySelectorAll('input').forEach(i => i.required = false);
        });
        
        // Show selected method section and make input required
        if (selectedMethod === 'bkash') {
            bkashDetails.style.display = 'block';
            bkashDetails.querySelectorAll('input').forEach(i => i.required = true);
        } else if (selectedMethod === 'nagad') {
            nagadDetails.style.display = 'block';
            nagadDetails.querySelectorAll('input').forEach(i => i.required = true);
        } else if (selectedMethod === 'rocket') {
            rocketDetails.style.display = 'block';
            rocketDetails.querySelectorAll('input').forEach(i => i.required = true);
        }
    }

    paymentMethods.forEach(method => {
        method.addEventListener('change', updatePaymentMethodDisplay);
    });
    
    updatePaymentMethodDisplay(); // initialize

    // --- Dynamic total calculation based on appointment type ---
    const appointmentTypeSelect = document.querySelector('select[name="appointment_type"]'); // Make sure you have a select element
    const payButton = document.querySelector('.btn-primary');

    function updateTotalAmount() {
        let baseFee = 500; // default for all types
        if(appointmentTypeSelect){
            const type = appointmentTypeSelect.value.toLowerCase();
            // You can customize fees for each type if needed
            if(type === 'consultation') baseFee = 500;
            else if(type === 'follow-up visit') baseFee = 500;
            else if(type === 'routine check-up') baseFee = 500;
            else if(type === 'special test') baseFee = 500;
        }
        const onlineCharge = baseFee * 0.02;
        const total = baseFee + onlineCharge;

        if(payButton){
            payButton.textContent = `Pay Now - ${total.toFixed(2)} Taka`;
        }
    }

    if(appointmentTypeSelect){
        appointmentTypeSelect.addEventListener('change', updateTotalAmount);
        updateTotalAmount(); // initialize
    }
});

    </script>
</body>
</html>