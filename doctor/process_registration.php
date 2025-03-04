<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "livmedly";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $permanentAddress = trim($_POST['permanentAddress']);
    $currentAddress = trim($_POST['currentAddress']);

    $errors = [];

    // Validation
    if (strlen($firstName) < 2) {
        $errors['firstName'] = "First name must be at least 2 characters.";
    }
    
    if (strlen($lastName) < 2) {
        $errors['lastName'] = "Last name must be at least 2 characters.";
    }

    $age = date_diff(date_create($dob), date_create('today'))->y;
    if ($age < 25) {
        $errors['dob'] = "Doctor must be at least 25 years old.";
    }

    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors['phone'] = "Please enter a valid 10-digit phone number.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    if (strlen($permanentAddress) < 10) {
        $errors['permanentAddress'] = "Please enter a complete permanent address.";
    }

    if (strlen($currentAddress) < 10) {
        $errors['currentAddress'] = "Please enter a complete current address.";
    }

    // Profile Photo Handling
    if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = mime_content_type($_FILES['profilePhoto']['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $errors['profilePhoto'] = "Please upload a valid image file (JPEG, JPG, PNG).";
        } else {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileName = time() . "_" . basename($_FILES['profilePhoto']['name']);
            $targetFilePath = $targetDir . $fileName;

            if (!move_uploaded_file($_FILES['profilePhoto']['tmp_name'], $targetFilePath)) {
                $errors['profilePhoto'] = "Error uploading the file.";
            }
        }
    } else {
        $errors['profilePhoto'] = "Please upload a profile photo.";
    }

    // If errors exist, redirect back
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: Step-1 doctor-registration.html");
        exit;
    }

    // Insert into Database
    $stmt = $conn->prepare("INSERT INTO doctors (first_name, last_name, dob, gender, phone, email, permanent_address, current_address, profile_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $firstName, $lastName, $dob, $gender, $phone, $email, $permanentAddress, $currentAddress, $fileName);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Step - 1 Successful!";
        header("Location: Step-2 professional-details.html");
    } else {
        $_SESSION['errors'][] = "Something went wrong. " . $stmt->error;
        header("Location: Step-1 doctor-registration.html");
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: Step-1 doctor-registration.html");
    exit;
}
