<?php
// Include the config file for database connection
require_once 'config.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and store input values
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Prepare SQL statement to prevent SQL injection
    $sql = "INSERT INTO contacts (full_name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("sssss", $full_name, $email, $phone, $subject, $message);
        
        // Execute the query
        if ($stmt->execute()) {
            echo "<script>
                    alert('Thank you for contacting us! We will get back to you soon.');
                    window.location.href = 'contact.html';
                  </script>";
        } else {
            echo "<script>
                    alert('Error: Could not process your request.');
                    window.location.href = 'contact.html';
                  </script>";
        }
        
        // Close statement
        $stmt->close();
    } else {
        echo "<script>
                alert('Error: Could not prepare statement.');
                window.location.href = 'contact.html';
              </script>";
    }
}

// Close the database connection
$conn->close();
?>
