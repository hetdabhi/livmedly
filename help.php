<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($message)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.history.back();</script>";
        exit;
    }

    if (!$conn) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO help (name, email, message) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }

    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Message sent successfully!'); window.location.href='help.html';</script>";
    } else {
        echo "<script>alert('Error sending message. Try again later.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: help.html");
    exit;
}
