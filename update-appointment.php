<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "livmedly";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure doctor is logged in
if (!isset($_SESSION['doctor_id'])) {
    die("Unauthorized access.");
}

// Get appointment ID and action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id']) && isset($_POST['action'])) {
    $appointment_id = $_POST['appointment_id'];
    $action = $_POST['action'];

    // Validate action
    if (!in_array($action, ['completed', 'canceled'])) {
        die("Invalid action.");
    }

    // Update appointment status
    $sql = "UPDATE appointments SET status = ? WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $action, $appointment_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Appointment status updated to $action!'); window.location.href='doctor-appointments.php';</script>";
    } else {
        echo "Error updating status: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
