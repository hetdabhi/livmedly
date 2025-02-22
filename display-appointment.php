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

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view appointments.");
}

$user_id = $_SESSION['user_id']; // Get logged-in user's ID
date_default_timezone_set("Asia/Kolkata"); // Standard Indian timezone

$current_date = date("Y-m-d");
$current_time = date("H:i:s");

// Fetch appointments
$sql = "SELECT id, name, email, phone, department, doctor, specialization, date, time, notes, status 
        FROM appointments 
        WHERE user_id = ?
        ORDER BY date DESC, time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f0f2f5;
            padding: 20px;
        }

        .appointment-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: left;
        }

        .appointment-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 350px;
        }

        .appointment-card h3 {
            margin: 0;
            color: #283779;
        }

        .appointment-card p {
            margin: 8px 0;
            color: #555;
        }

        .status {
            font-weight: bold;
            padding: 5px 20px;
            border-radius: 5px;
            display: inline-block;
        }

        .status-upcoming {
            background: #4CAF50;
            color: white;
        }

        .status-completed {
            background: #2196F3;
            color: white;
        }

        .status-canceled {
            background: #f44336;
            color: white;
        }
        
        .cancel-btn {
            background: #f44336;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        
        .cancel-btn:hover {
            background: #d32f2f;
        }

    </style>
</head>
<body>

    <h2>My Appointments</h2>
    <div class="appointment-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $appointment_date = $row['date'];
                $appointment_time = $row['time'];
                $status = $row['status'];

                // Automatically determine appointment status
                if ($status !== "canceled") { 
                    if ($appointment_date < $current_date || ($appointment_date == $current_date && $appointment_time < $current_time)) {
                        $status = "completed";
                    } else {
                        $status = "upcoming";
                    }
                }

                echo "<div class='appointment-card'>
                        <h3>Dr. " . htmlspecialchars($row['doctor']) . "</h3>
                        <p><strong>Specialization:</strong> " . htmlspecialchars($row['specialization']) . "</p>
                        <p><strong>Department:</strong> " . htmlspecialchars($row['department']) . "</p>
                        <p><strong>Date:</strong> " . date("F j, Y", strtotime($row['date'])) . "</p>
                        <p><strong>Time:</strong> " . date("g:i A", strtotime($row['time'])) . "</p>
                        <p><strong>Notes:</strong> " . htmlspecialchars($row['notes']) . "</p>
                        <p class='status status-" . strtolower($status) . "'>" . ucfirst($status) . "</p>";

                if ($status === "upcoming") {
                    echo "<form method='POST' action='cancel_appointment.php'>
                            <input type='hidden' name='appointment_id' value='" . $row['id'] . "'>
                            <button type='submit' class='cancel-btn'>Cancel</button>
                          </form>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>No appointments found.</p>";
        }
        ?>
    </div>

</body>
</html>

<?php
$conn->close();
?>
