<?php
// Include database connection
require_once 'config.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('Invalid email format!');
                window.location.href = 'index.html';
              </script>";
        exit();
    }

    // Check if email already exists in the database
    $check_sql = "SELECT * FROM subscribers WHERE email = '$email'";
    $result = $conn->query($check_sql);

    if ($result === false) {
        die("Database error: " . $conn->error); // Debugging statement (remove in production)
    }

    if ($result->num_rows > 0) {
        // Email already exists
        echo "<script>
                alert('You are already subscribed!');
                window.location.href = 'index.html';
              </script>";
    } else {
        // Insert email into the database
        $sql = "INSERT INTO subscribers (email) VALUES ('$email')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert('Thank you for subscribing!');
                    window.location.href = 'index.html';
                  </script>";
        } else {
            die("Database error: " . $conn->error); // Debugging statement (remove in production)
        }
    }
}

// Close the database connection
$conn->close();
?>
