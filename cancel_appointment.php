<?php
session_start();
require_once 'db.php'; // তোমার database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if appointment_id is passed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
    $appointment_id = intval($_POST['appointment_id']);

    // Prepare the SQL to update status
    $sql = "UPDATE appointments 
            SET status = 'cancelled' 
            WHERE id = ? AND user_id = ? AND status = 'scheduled'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $appointment_id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Appointment cancelled successfully.";
    } else {
        $_SESSION['error_message'] = "Unable to cancel appointment. Please try again.";
    }

    $stmt->close();
}

// Redirect back to appointments page
header("Location: view_appointments.php");
exit();
?>
