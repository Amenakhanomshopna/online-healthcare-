<?php
session_start();
// Database configuration
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get appointments - FIXED QUERY
$stmt = $conn->prepare("
    SELECT a.*, d.name AS doctor_name
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.user_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$appointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - HealthCare Plus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></link>
    <link rel="stylesheet" href="dashboard.css">
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
    
    <!-- Main Content - Dashboard -->
    <main class="dashboard-container">
        <!-- User Information Section -->
        <div class="user-info-section">
            <div class="user-welcome">
                <h2>Welcome back, <?= htmlspecialchars($user['first_name']) ?>!</h2>
                <p>Last login: <?= date('F j, Y \a\t h:i A') ?></p>
            </div>
            
            <div class="user-details">
                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="detail-text">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="detail-text">
                        <div class="detail-label">Email</div>
                        <div class="detail-value"><?= htmlspecialchars($user['email']) ?></div>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="detail-text">
                        <div class="detail-label">Phone</div>
                        <div class="detail-value"><?= htmlspecialchars($user['phone']) ?></div>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="detail-text">
                        <div class="detail-label">Address</div>
                        <div class="detail-value"><?= htmlspecialchars($user['address']) ?></div>
                    </div>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="edit_profile.php" class="btn-primary">
                    <i class="fas fa-edit"></i>
                    Edit Profile
                </a>
                <a href="logout.php" class="btn-secondary">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
        
        <!-- Overview Section -->
        <div class="overview-section">
            <!-- Recent Appointments -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-check"></i>
                        Recent Appointments
                    </h3>
                    
                </div>
                <?php if (!empty($appointments)): ?>
    <div class="appointments-list">
        <?php 
        // Latest 4 appointments only
        $latestAppointments = array_slice($appointments, 0, 4); 
        foreach ($latestAppointments as $appointment): 
        ?>
            <div class="appointment-item">
                <div class="appointment-info">
                    <div class="appointment-doctor">Dr. <?= htmlspecialchars($appointment['doctor_name']) ?></div>
                    <div class="appointment-date"><?= date('l, F j, Y', strtotime($appointment['appointment_date'])) ?></div>
                    <div class="appointment-time"><?= date('g:i A', strtotime($appointment['appointment_time'])) ?></div>
                </div>
                <div class="appointment-status status-<?= htmlspecialchars($appointment['status']) ?>">
                    <?= htmlspecialchars(ucfirst($appointment['status'])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="no-data">
        <i class="fas fa-calendar-times"></i>
        <p>No appointments found. Click "New Appointment" to schedule one.</p>
    </div>
<?php endif; ?>

            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tasks"></i>
                        Quick Actions
                    </h3>
                </div>
                
                <div class="action-buttons" style="justify-content: center; flex-wrap: wrap; gap: 15px;">
                    <a href="book_appointment.php" class="btn-primary">
                        <i class="fas fa-calendar-plus"></i>
                        Make New Appointment
                    </a>
                    
                    <a href="view_appointments.php" class="btn-primary">
                        <i class="fas fa-list"></i>
                        View All Appointments
                    </a>
                    
                    
                    <a href="reviews.php" class="btn-primary">
                        <i class="fas fa-star"></i>
                        Give Feedback
                    </a>
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