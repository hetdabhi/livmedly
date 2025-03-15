<?php
require 'config.php'; // Database connection

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"])) {
    $email = trim($_POST["email"]);

    // Insert email into forgotpassword table
    $stmt = $conn->prepare("INSERT INTO forgotpassword (email) VALUES (?)");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        echo "success"; // Email stored successfully
    } else {
        echo "error"; // Failed to insert email
    }

    $stmt->close();
}

$conn->close();
?>
