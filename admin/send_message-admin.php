<?php
session_start();
$conn = new mysqli("localhost", "root", "", "livmedly");

// Check for connection error
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Define a fixed admin ID (change this to your actual admin ID)
$admin_id = 1; // Ensure this is the correct admin user ID

// Handle admin message sending
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient_id = $_POST['recipient_id'];
    $recipient_type = $_POST['recipient_type'];
    $message = $_POST['message'];

    // Insert message into database with fixed sender_id (admin)
    $sql = "INSERT INTO messages (sender_id, recipient_id, message, recipient_type) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $admin_id, $recipient_id, $message, $recipient_type);

    if ($stmt->execute()) {
        header("Location: send_message-admin.php?success=1");
        exit();
    } else {
        echo "<script>alert('Error sending message: " . $conn->error . "');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Send Message</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Montserrat', sans-serif; }
        body { background: #f6f5f7; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background-color: #fff; border-radius: 10px; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); width: 500px; padding: 30px; text-align: center; }
        h1 { margin-bottom: 20px; }
        input, select, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 10px; border: none; background: #283779; color: white; font-size: 16px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #1e7bb0; }
        .success { color: green; font-weight: bold; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Send Message</h1>

        <?php if (isset($_GET['success'])): ?>
            <p class="success">Message sent successfully!</p>
        <?php endif; ?>

        <form action="send_message-admin.php" method="POST">
            <select name="recipient_type" id="recipient_type" required>
                <option value="">Select Recipient Type</option>
                <option value="doctor">Doctor</option>
                <option value="patient">Patient</option>
            </select>
            <select name="recipient_id" id="recipient_id" required>
                <option value="">Select Recipient</option>
            </select>
            <textarea name="message" placeholder="Type your message here..." required></textarea>
            <button type="submit">Send Message</button>
        </form>
    </div>

    <script>
        document.getElementById('recipient_type').addEventListener('change', function() {
            var recipientType = this.value;
            var recipientDropdown = document.getElementById('recipient_id');

            recipientDropdown.innerHTML = '<option value="">Select Recipient</option>';

            if (recipientType) {
                fetch('fetch_recipients.php?type=' + recipientType)
                .then(response => response.json())
                .then(data => {
                    data.forEach(recipient => {
                        var option = document.createElement('option');
                        option.value = recipient.id;
                        option.textContent = recipient.name;
                        recipientDropdown.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
            }
        });
    </script>
</body>
</html>
