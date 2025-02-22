<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "livmedly";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $specialization = $_POST['specialization'];
    $address = trim($_POST['address']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $confirm_password = trim($_POST['confirm_password']);

    if (!password_verify($confirm_password, $password)) {
        die("<script>alert('Passwords do not match!'); window.location.href='doctor-registeration.html';</script>");
    }

    $sql = "INSERT INTO doctor (name, username, email, phone_number, gender, blood_group, specialization, address, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $name, $username, $email, $phone_number, $gender, $blood_group, $specialization, $address, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Doctor registered successfully!'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='doctor-registeration.html';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
