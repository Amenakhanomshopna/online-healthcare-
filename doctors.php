<?php
session_start(); // Start the session
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Doctors - HealthCare Plus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></link>
    <link rel="stylesheet" href="doctors.css">
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
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="section-title">
                <h2>Meet Our Expert Team</h2>
                <p>Our dedicated team of healthcare professionals committed to providing the highest quality care.</p>
            </div>
            <div class="doctors-grid">
                <?php
                // Include database connection
                require_once 'db.php';
                
                // Fetch doctors from database
                $sql = "SELECT * FROM doctors ORDER BY id ASC";
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '
                        <div class="doctor-card">
                            <img src="' . htmlspecialchars($row["image_url"]) . '" alt="' . htmlspecialchars($row["name"]) . '" class="doctor-image">
                            <div class="doctor-info">
                                <h3 class="doctor-name">' . htmlspecialchars($row["name"]) . '</h3>
                                <p class="doctor-designation">' . htmlspecialchars($row["designation"]) . '</p>
                                <p class="doctor-specialty">' . htmlspecialchars($row["specialty"]) . '</p>
                                
                                <div class="doctor-contact">
                                    <span class="contact-icon"><i class="fas fa-envelope"></i></span>
                                    <span>' . htmlspecialchars($row["email"]) . '</span>
                                </div>
                                
                                <div class="doctor-contact">
                                    <span class="contact-icon"><i class="fas fa-phone"></i></span>
                                    <span>' . htmlspecialchars($row["phone"]) . '</span>
                                </div>
                                
                                <p class="doctor-bio">' . htmlspecialchars($row["bio"]) . '</p>
                                
                                <a href="#" class="view-profile-btn">View Full Profile</a>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p>No doctors found.</p>';
                }
                
                // Close connection
                $conn->close();
                ?>
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
                        <button type="submit" class="view-profile-btn" style="width: 100%;">Subscribe</button>
                    </form>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2025 HealthCare Plus. All rights reserved. Created by Amena Khanom Shopna</p>
            </div>
        </div>
    </footer>
</body>
</html>