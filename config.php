<?php
// Start session only if none exists
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection details
define('DB_HOST', 'localhost');  // Change if needed
define('DB_USER', 'root');       // Your database username
define('DB_PASS', '');           // Your database password
define('DB_NAME', 'livmedly'); // Your database name

// Establish database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
