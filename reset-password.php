<?php
session_start();
include 'config.php'; // Ensure the correct connection file is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate unique reset token
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token valid for 1 hour

        // Store token in the database
        $stmt = $conn->prepare("UPDATE users SET reset_token=?, reset_expiry=? WHERE email=?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // Send reset email
        $reset_link = "http://yourwebsite.com/reset-form.php?token=" . $token;
        $subject = "Password Reset Request";
        $message = "Click the link below to reset your password:\n\n" . $reset_link;
        $headers = "From: no-reply@yourwebsite.com\r\n";

        if (mail($email, $subject, $message, $headers)) {
            $_SESSION["success"] = "A password reset link has been sent to your email.";
        } else {
            $_SESSION["error"] = "Failed to send the email. Please try again.";
        }
    } else {
        $_SESSION["error"] = "No account found with that email.";
    }

    header("Location: forgot-password.html");
    exit();
}
?>
