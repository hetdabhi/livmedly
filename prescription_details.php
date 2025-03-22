<?php
// Include database connection
include 'config.php';
session_start();

// Check if user is logged in (strict check for patient access only)
if (!isset($_SESSION['user_id'])) {
    die("<script>alert('Please log in first.'); window.location.href='login.php';</script>");
}

// Get user ID from session
$user_id = $_SESSION['user_id'];
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';

// Fetch prescription data for the logged-in user
$stmt = $conn->prepare("SELECT p.id, p.medicine_name, p.quantity, p.morning, p.afternoon, p.evening, p.night, p.notes, d.name AS doctor_name 
                        FROM prescriptions p 
                        JOIN doctor d ON p.doctor_id = d.id 
                        WHERE p.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Details</title>

    
    <!-- ===============================================-->
    <!--Favicons-->
    <!-- ===============================================-->

    <link rel="icon" type="image/png" href="favicon.ico" sizes="16x16">
    
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #f6f5f7;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 900px;
            padding: 20px;
            text-align: center;
        }

        h1 {
            margin-bottom: 15px;
            font-weight: bold;
        }

        .welcome-text {
            margin-bottom: 20px;
            color: #333;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #5DB2FF;
            color: white;
        }

        .home-btn-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .home-btn {
            background-color: #28a745;
            color: white;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .home-btn:hover {
            background-color: #218838;
        }

        .no-prescriptions {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Prescription Details</h1>
        <p class="welcome-text">Welcome, <?php echo htmlspecialchars($user_name); ?>!</p>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Medicine</th>
                        <th>Quantity</th>
                        <th>Morning</th>
                        <th>Afternoon</th>
                        <th>Evening</th>
                        <th>Night</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['medicine_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td><?php echo $row['morning'] ? '✔' : '✖'; ?></td>
                            <td><?php echo $row['afternoon'] ? '✔' : '✖'; ?></td>
                            <td><?php echo $row['evening'] ? '✔' : '✖'; ?></td>
                            <td><?php echo $row['night'] ? '✔' : '✖'; ?></td>
                            <td><?php echo htmlspecialchars($row['notes']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-prescriptions">
                <p>No prescriptions found. Please consult with a doctor to get prescriptions.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Home Button Below the Container - Always go to user dashboard for patient view -->
    <div class="home-btn-container">
        <a href="userdashboard.php" class="home-btn">Back to Dashboard</a>
    </div>

    <!-- Session management script -->
    <script>
    function keepSessionAlive() {
        fetch('keep_session.php')
            .then(response => response.text())
            .catch(error => console.error('Error:', error));
    }
    
    window.onload = function() {
        keepSessionAlive();
        setInterval(keepSessionAlive, 300000); // Every 5 minutes
    };
    </script>
</body>

</html>