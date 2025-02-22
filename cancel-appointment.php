<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "livmedly";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $user_id = $_SESSION['user_id'];

    // Update the appointment status to canceled
    $sql = "UPDATE appointments SET status = 'canceled' WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $appointment_id, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment canceled successfully!'); window.location.href = 'appointment.php';</script>";
    } else {
        echo "Error canceling appointment.";
    }

    $stmt->close();
}
$conn->close();
?>
