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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']); // Get the selected role

    if ($role == "user") {
        $sql = "SELECT id, password FROM users WHERE email = ?";
    } elseif ($role == "doctor") {
        $sql = "SELECT id, password FROM doctor WHERE email = ?"; // Ensure correct table name
    } else {
        die("Invalid role selected.");
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {  // Secure password check
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $role; // Store user role in session

            if ($role == "user") {
                header("Location: userdashboard.php"); // Redirect user to their dashboard
            } elseif ($role == "doctor") {
                header("Location: doctordashboard.php"); // Redirect doctor to their dashboard
            }
            exit();
        } else {
            echo "<script>alert('Invalid email or password.'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('No account found with this email.'); window.location.href='login.html';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
