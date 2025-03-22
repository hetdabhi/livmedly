<?php
session_start();

// Check if the user is logged in as a doctor
if (isset($_SESSION['doctor_id'])) {
    // Unset doctor session variables
    unset($_SESSION['doctor_id']);
    unset($_SESSION['name']);
    unset($_SESSION['specialization']);
    unset($_SESSION['is_doctor']); // If you have this flag

    // Optionally, you can redirect to the login page or a specific page for doctors
    header("Location: login.html");
    exit();
}

// Check if the user is logged in as a regular user
if (isset($_SESSION['user_id'])) {
    // Unset user session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['fullname']);
    unset($_SESSION['email']);
    unset($_SESSION['phone']);
    unset($_SESSION['bloodgroup']);
    unset($_SESSION['is_user']); // If you have this flag

    // Optionally, you can redirect to the login page or a specific page for users
    header("Location: login.html");
    exit();
}

// If no session is found, just redirect to the login page
header("Location: login.html");
exit();
?>