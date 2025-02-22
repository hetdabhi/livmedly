<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch user details
    $stmt = $conn->prepare("SELECT id, fullname, username, phone, email, gender, bloodgroup, address, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $fullname, $username, $phone, $email, $gender, $bloodgroup, $address, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Store user details in session
            $_SESSION['user_id'] = $id;
            $_SESSION['fullname'] = $fullname;
            $_SESSION['username'] = $username;
            $_SESSION['phone'] = $phone;
            $_SESSION['email'] = $email;
            $_SESSION['gender'] = $gender;
            $_SESSION['bloodgroup'] = $bloodgroup;
            $_SESSION['address'] = $address;

            header("Location:dashboard.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('Email not registered!'); window.location.href='login.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
