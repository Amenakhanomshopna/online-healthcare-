<?php
// Start the session
session_start();
// Database configuration
include 'db.php';
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// Get doctors list from database
$doctors = [];
$doctor_sql = "SELECT id, name, specialty, image_url FROM doctors";
$doctor_result = $conn->query($doctor_sql);
if ($doctor_result && $doctor_result->num_rows > 0) {
    $doctors = $doctor_result->fetch_all(MYSQLI_ASSOC);
}

// Handle form submission
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data
    $patient_name = trim($_POST['patientName']);
    $contact_number = trim($_POST['contactNumber']);
    $email = trim($_POST['email']);
    $preferred_doctor = trim($_POST['preferredDoctor']);
    $appointment_date = trim($_POST['appointmentDate']);
    $appointment_time = trim($_POST['appointmentTime']);
    $appointment_type = trim($_POST['appointmentType']);
    $notes = trim($_POST['notes']);

    // Basic validation
    if (empty($patient_name) || empty($contact_number) || empty($email) || 
        empty($preferred_doctor) || empty($appointment_date) || 
        empty($appointment_time) || empty($appointment_type)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Check if doctor exists
        $doctor_check_sql = "SELECT id FROM doctors WHERE id = ?";
        $doctor_check_stmt = $conn->prepare($doctor_check_sql);
        $doctor_check_stmt->bind_param("i", $preferred_doctor);
        $doctor_check_stmt->execute();
        $doctor_exists = $doctor_check_stmt->get_result()->num_rows > 0;
        $doctor_check_stmt->close();
        
        if (!$doctor_exists) {
            $error_message = 'Selected doctor not found. Please try again.';
        } else {
            // Store appointment data in session
            $_SESSION['appointment_data'] = [
                'patient_name' => $patient_name,
                'contact_number' => $contact_number,
                'email' => $email,
                'preferred_doctor' => $preferred_doctor,
                'appointment_date' => $appointment_date,
                'appointment_time' => $appointment_time,
                'appointment_type' => $appointment_type,
                'notes' => $notes
            ];
            
            // Redirect to payment page
            header("Location: payment.php");
            exit();
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - HealthCare Plus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></link>
    <link rel="stylesheet" href="book_appointment.css">
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
    <!-- Main Content - Book Appointment -->
    <main class="booking-container">
        <div class="form-section">
            <div class="form-header">
                <h2>Schedule an Appointment</h2>
                <p>Select your preferred doctor and time slot for your next visit</p>
            </div>
            
           <?php if (!empty($error_message)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error_message) ?></div>
<?php endif; ?>

<?php if (!empty($success_message)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
<?php endif; ?>

            
            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                <div class="form-group">
                    <label for="patientName" class="form-label">Patient Name</label>
                    <input type="text" id="patientName" name="patientName" class="form-input" placeholder="Enter patient name" required>
                </div>
                
                <div class="form-group">
                    <label for="contactNumber" class="form-label">Contact Number</label>
                    <input type="tel" id="contactNumber" name="contactNumber" class="form-input" placeholder="Enter contact number" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="Enter email address" required>
                </div>
                
                <div class="form-group">
                    <label for="preferredDoctor" class="form-label">Preferred Doctor</label>
                    <select id="preferredDoctor" name="preferredDoctor" class="form-select" required>
                        <option value="">Select a doctor</option>
                        <?php foreach ($doctors as $doc): ?>
                            <option value="<?= $doc['id'] ?>">
                                <?= htmlspecialchars($doc['name']) ?> - <?= htmlspecialchars($doc['specialty']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="date-time-section">
                    <div class="form-group">
                        <label for="appointmentDate" class="form-label">Appointment Date</label>
                        <input type="date" id="appointmentDate" name="appointmentDate" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="appointmentTime" class="form-label">Preferred Time</label>
                        <select id="appointmentTime" name="appointmentTime" class="form-select" required>
                            <option value="">Select a time</option>
                            <option value="09:00">9:00 AM</option>
                            <option value="09:30">9:30 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="10:30">10:30 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="11:30">11:30 AM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="14:30">2:30 PM</option>
                            <option value="15:00">3:00 PM</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="appointmentType" class="form-label">Appointment Type</label>
                    <select id="appointmentType" name="appointmentType" class="form-select" required>
                        <option value="">Select appointment type</option>
                        <option value="consultation">Consultation</option>
                        <option value="followup">Follow-up Visit</option>
                        <option value="checkup">Routine Check-up</option>
                        <option value="specialtest">Special Test</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="notes" class="form-label">Reason</label>
                    <textarea id="notes" name="notes" class="form-input" rows="3" placeholder="Please provide any additional information..."></textarea>
                </div>
                
                <button type="submit" class="btn-primary">Schedule Appointment</button>
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
</body>
</html>