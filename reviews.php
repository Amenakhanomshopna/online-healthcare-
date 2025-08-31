<?php
// Start the session
session_start();

// Database configuration
include 'db.php';

// Initialize variables
$error_message = '';
$success_message = '';

// Get reviews from database (for display)
$reviews = [];
$review_sql = "SELECT * FROM reviews ORDER BY id DESC LIMIT 5"; // Get last 5 reviews
$review_result = $conn->query($review_sql);
if ($review_result && $review_result->num_rows > 0) {
    $reviews = $review_result->fetch_all(MYSQLI_ASSOC);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data
    $reviewer_name = trim($_POST['reviewerName']);
    $review_text = trim($_POST['reviewText']);
    $avatar_url = trim($_POST['avatarUrl']);
    $user_role = trim($_POST['userRole']);
    
    // Basic validation
    if (empty($reviewer_name) || empty($review_text) || empty($user_role)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($avatar_url, FILTER_VALIDATE_URL) && !empty($avatar_url)) {
        $error_message = 'Please enter a valid URL for your profile picture.';
    } else {
        // Get user ID (assuming user is logged in)
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        // Insert review into database
        $sql = "INSERT INTO reviews (name, comment, avatar, role) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", 
            $reviewer_name, 
            $review_text, 
            $avatar_url, 
            $user_role
        );
        
        if ($stmt->execute()) {
            $success_message = 'Review submitted successfully! Thank you for your feedback.';
        } else {
            $error_message = 'Error submitting review: ' . $conn->error;
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Feedback - HealthCare Plus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></link>
    <link rel="stylesheet" href="reviews.css">
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

    <!-- Main Content - Give Review -->
    <main class="review-container">
        <div class="form-section">
            <div class="form-header">
                <h2>Share Your Experience</h2>
                <p>Help us improve our services by sharing your feedback about your recent appointment</p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>
            
            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                <div class="form-group">
                    <label for="reviewerName" class="form-label">Your Name</label>
                    <input type="text" id="reviewerName" name="reviewerName" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="reviewText" class="form-label">Your Review</label>
                    <textarea id="reviewText" name="reviewText" class="form-input" rows="5" placeholder="Tell us about your experience..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="avatarUrl" class="form-label">Profile Picture URL (Optional)</label>
                    <input type="url" id="avatarUrl" name="avatarUrl" class="form-input" placeholder="Enter URL to your profile picture">
                </div>
                
                <div class="form-group">
                    <label for="userRole" class="form-label">Your Role</label>
                    <select id="userRole" name="userRole" class="form-select" required>
                        <option value="">Select your role</option>
                        <option value="Patient">Patient</option>
                        <option value="Visitor">Visitor</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>
                
                <div class="rating-section">
                    <h3>Rate Your Experience</h3>
                    <div class="stars">
                        <span class="star" data-rating="1">★</span>
                        <span class="star" data-rating="2">★</span>
                        <span class="star" data-rating="3">★</span>
                        <span class="star" data-rating="4">★</span>
                        <span class="star" data-rating="5">★</span>
                    </div>
                </div>
                
                <div class="submission-section">
                    <button type="submit" class="btn-primary">Submit Review</button>
                </div>
            </form>
        </div>
        
        <div class="right-panel">
            <div class="recent-reviews">
                <div class="reviews-header">
                    <h3>
                        <i class="fas fa-comments"></i>
                        Recent Reviews
                    </h3>
                </div>
                
                <div class="review-list">
                    <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="review-info">
                            <div class="review-header">
                                <div class="reviewer-name"><?= htmlspecialchars($review['name']) ?></div>
                                <div class="review-date">Just now</div>
                            </div>
                            <div class="review-rating">
                                <div class="review-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="review-star">★</span>
                                    <?php endfor; ?>
                                </div>
                                <div class="review-comment"><?= htmlspecialchars($review['comment']) ?></div>
                            </div>
                            <div class="review-actions">
                                <div class="review-action">
                                    <i class="far fa-thumbs-up"></i>
                                    <span>12</span>
                                </div>
                                <div class="review-action">
                                    <i class="far fa-comment"></i>
                                    <span>Reply</span>
                                </div>
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
                        <li><a href="index.html">Home</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="services.html">Services</a></li>
                        <li><a href="doctors.html">Doctors</a></li>
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
        // Star rating functionality
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            let selectedRating = 0;
            
            stars.forEach((star, index) => {
                star.addEventListener('click', () => {
                    selectedRating = index + 1;
                    updateStars();
                });
                
                star.addEventListener('mouseover', () => {
                    highlightStars(index);
                });
                
                star.addEventListener('mouseout', () => {
                    updateStars();
                });
            });
            
            function highlightStars(index) {
                stars.forEach((star, i) => {
                    if (i <= index) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            }
            
            function updateStars() {
                stars.forEach((star, i) => {
                    if (i < selectedRating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            }
        });
    </script>
</body>
</html>