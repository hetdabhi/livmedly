<?php
session_start();
include 'config.php'; // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim input to remove unnecessary whitespace
    $name = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $gender = trim($_POST['gender']);
    $bloodgroup = trim($_POST['bloodgroup']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);
    $cpassword = trim($_POST['cpassword']);

    // Validate required fields
    if (empty($name) || empty($username) || empty($phone) || empty($email) || empty($gender) || empty($bloodgroup) || empty($address) || empty($password) || empty($cpassword)) {
        echo "<script>alert('All fields are required!'); window.location.href='registration.html';</script>";
        exit();
    }

    // Check if username contains uppercase letters
    if (preg_match('/[A-Z]/', $username)) {
        echo "<script>alert('Username must be in lowercase letters only!'); window.location.href='registration.html';</script>";
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!'); window.location.href='registration.html';</script>";
        exit();
    }

    // Validate phone number (check if it is numeric and has length of 10 digits)
    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        echo "<script>alert('Invalid phone number! Please enter a valid 10-digit phone number.'); window.location.href='registration.html';</script>";
        exit();
    }

    // Check if email already exists
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='login.html';</script>";
        exit();
    }
    $stmt->close();

    // Validate password and confirm password
    if ($password !== $cpassword) {
        echo "<script>alert('Passwords do not match!'); window.location.href='registration.html';</script>";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert user data into database
    $stmt = $conn->prepare("INSERT INTO users (fullname, username, phone, email, gender, bloodgroup, address, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $username, $phone, $email, $gender, $bloodgroup, $address, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Error occurred! Please try again later.'); window.location.href='registration.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>