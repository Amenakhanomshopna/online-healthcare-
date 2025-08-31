<?php
session_start(); // Start the session
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthCare Plus - Your Trusted Healthcare Partner</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></link>
    <link rel="stylesheet" href="index.css">
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
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Your Health, Our Priority</h1>
            <p>Experience world-class healthcare services delivered by experienced professionals with personalized care and advanced technology.</p>
            <a href="book_appointment.php" class="btn-primary">Book Appointment</a>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose Us?</h2>
                <p>We provide comprehensive healthcare solutions that prioritize your well-being and comfort.</p>
            </div>
            
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3>Expert Doctors</h3>
                    <p>Our team of highly qualified doctors brings years of experience and expertise to ensure you receive the best possible care.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>24/7 Availability</h3>
                    <p>Access quality healthcare anytime, anywhere with our round-the-clock service availability.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Advanced Technology</h3>
                    <p>Leverage cutting-edge medical technology for accurate diagnosis and effective treatment plans.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Personalized Care</h3>
                    <p>Tailored healthcare solutions designed specifically for your unique needs and preferences.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Services Section -->
    <section class="services">
        <div class="container">
            <div class="section-title">
                <h2>Our Services</h2>
                <p>A comprehensive range of healthcare services to meet all your medical needs under one roof.</p>
            </div>
            
            <div class="service-cards">
                <div class="service-card">
                    <img src="https://picsum.photos/seed/consultation/400/200.jpg" alt="Consultation" class="service-img">
                    <div class="service-content">
                        <h3>Virtual Consultations</h3>
                        <p>Connect with our specialists from the comfort of your home through secure video consultations.</p>
                        <a href="#" class="btn-primary">Learn More</a>
                    </div>
                </div>
                
                <div class="service-card">
                    <img src="https://picsum.photos/seed/diagnostics/400/200.jpg" alt="Diagnostics" class="service-img">
                    <div class="service-content">
                        <h3>Diagnostic Services</h3>
                        <p>Comprehensive diagnostic testing including lab work, imaging, and specialized screenings.</p>
                        <a href="#" class="btn-primary">Learn More</a>
                    </div>
                </div>
                
                <div class="service-card">
                    <img src="https://picsum.photos/seed/treatment/400/200.jpg" alt="Treatment" class="service-img">
                    <div class="service-content">
                        <h3>Specialized Treatments</h3>
                        <p>Advanced treatment options for various conditions with personalized care plans.</p>
                        <a href="#" class="btn-primary">Learn More</a>
                    </div>
                </div>
                
                <div class="service-card">
                    <img src="https://picsum.photos/seed/wellness/400/200.jpg" alt="Wellness" class="service-img">
                    <div class="service-content">
                        <h3>Wellness Programs</h3>
                        <p>Preventive care and wellness programs to help you maintain optimal health and vitality.</p>
                        <a href="#" class="btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <div class="section-title">
                <h2>What Our Patients Say</h2>
                <p>Hear from people who have experienced our exceptional healthcare services.</p>
            </div>
            
            <div class="testimonial-slider">
                <?php
                // Include database connection
                require_once 'db.php';
                
                // Fetch reviews from database
                $sql = "SELECT * FROM reviews ORDER BY id DESC LIMIT 3"; // Limit to 3 most recent reviews
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '
                        <div class="testimonial-card">
                            <div class="testimonial-quote">
                                <i class="fas fa-quote-left"></i>
                            </div>
                            <p>' . htmlspecialchars($row["comment"]) . '</p>
                            <div class="testimonial-author">
                                <img src="' . htmlspecialchars($row["avatar"]) . '" alt="Patient" class="author-avatar">
                                <div class="author-info">
                                    <h4>' . htmlspecialchars($row["name"]) . '</h4>
                                    <p>' . htmlspecialchars($row["role"]) . '</p>
                                </div>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p>No reviews available yet.</p>';
                }
                
                // Close connection
                $conn->close();
                ?>
            </div>
        </div>
    </section>
    
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
                        <li><a href="#">About Us</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="doctors.php">Doctors</a></li>
                        <li><a href="#">Appointments</a></li>
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