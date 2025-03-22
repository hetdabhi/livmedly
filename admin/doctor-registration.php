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
    $gender = isset($_POST['gender']) ? $_POST['gender'] : 'Not Specified';
    $blood_group = isset($_POST['blood_group']) ? $_POST['blood_group'] : 'Unknown';
    $specialization = isset($_POST['specialization']) ? $_POST['specialization'] : 'General';
    $address = trim($_POST['address']);

    // Check if username already exists
    $check_sql = "SELECT username FROM doctor WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('Error: Username already exists! Please choose a different username.'); window.location.href='doctor-registration.html';</script>";
    } else {
        // Insert new doctor record
        $sql = "INSERT INTO doctor (name, username, email, phone_number, gender, blood_group, specialization, address) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $name, $username, $email, $phone_number, $gender, $blood_group, $specialization, $address);

        if ($stmt->execute()) {
            echo "<script>alert('Doctor registered successfully!'); window.location.href='doctor-registration.html';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='doctor-registration.html';</script>";
        }

        $stmt->close();
    }
    
    $check_stmt->close();
}
$conn->close();
?>
