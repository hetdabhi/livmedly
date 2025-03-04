<?php
include 'config.php';

// Ensure session is started only if it's not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debugging: Print session variables to check if `admin_logged_in` exists
var_dump($_SESSION);

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized access"]);
    exit;
}

$sql = "SELECT name, username, email, phone, gender, bloodgroup, address FROM users";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($users, JSON_PRETTY_PRINT);
?>
