<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "livmedly");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

if (isset($_GET['type'])) {
    $recipientType = $_GET['type'];

    if ($recipientType === "doctor") {
        $sql = "SELECT id, name FROM doctor";
    } elseif ($recipientType === "patient") { // Change 'users' to 'patient'
        $sql = "SELECT id, fullname AS name FROM users WHERE role = 'user'";
    } else {
        echo json_encode(["error" => "Invalid recipient type"]);
        exit;
    }

    $result = $conn->query($sql);

    $recipients = [];
    while ($row = $result->fetch_assoc()) {
        $recipients[] = $row;
    }

    echo json_encode($recipients);
} else {
    echo json_encode(["error" => "No recipient type provided"]);
}

$conn->close();
?>
