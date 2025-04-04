<?php
session_start();
// Include database connection (if needed)
include 'config.php';
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first!'); window.location.href='login.html';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT fullname, email, phone, bloodgroup FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fullname, $email, $phone, $bloodgroup);
$stmt->fetch();
$stmt->close();

// Store fetched values in session (optional)
$_SESSION['fullname'] = $fullname;
$_SESSION['email'] = $email;
$_SESSION['phone'] = $phone;
$_SESSION['bloodgroup'] = $bloodgroup;
$_SESSION['profile_image'] = !empty($profile_image) ? $profile_image : 'img/user.png'; // Default image

// Fetch user's appointment details
$stmt = $conn->prepare("SELECT doctor, specialization, department, date, time, status FROM appointments WHERE email = ? AND date >= CURDATE() ORDER BY date ASC");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

$stmt->close();

// Initialize daysRemaining
$daysRemaining = null; // Default value

// Check if there are any upcoming appointments
if (!empty($appointments)) {
    // Get the first upcoming appointment (assuming sorted by date)
    $nextAppointment = $appointments[0];

    // Convert appointment date to a timestamp
    $appointmentDate = strtotime($nextAppointment['date']);
    $currentDate = strtotime(date('Y-m-d')); // Get today's date without time

    // Calculate days remaining
    $daysRemaining = ($appointmentDate - $currentDate) / (60 * 60 * 24);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LivMedly Patient Dashboard</title>

    <!-- ===============================================-->
    <!--Favicons-->
    <!-- ===============================================-->

    <link rel="icon" type="image/png" href="favicon.ico" sizes="16x16">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f0f2f5;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(to bottom, #5DB2FF, #004E92);
            color: white;
            padding: 20px;
        }

        .profile-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .sidebar-menu li:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quick-actions {
            display: flex;
            gap: 10px;
        }

        .button {
            border-radius: 20px;
            border: 1px solid #1e7bb0;
            background-color: #283779;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 25px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
            cursor: pointer;
        }

        .button:hover {
            transform: scale(1.05);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            /* Left 2/3, Right 1/3 */
            gap: 20px;
            align-items: start;
            max-width: 100%;
            /* Ensure full width */
            margin: auto;
            padding: 20px;
        }


        /* Main section (Left Side) */
        .main-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Side Section (Right Side) */
        .side-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            /* Match padding with .header */
            border-radius: 10px;
            /* Match border-radius */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0. 1);
            /* Match shadow */
            margin-bottom: 20px;
            width: 100%;
            /* Ensure full width */
            display: flex;
            flex-direction: column;
        }


        /* Health Metrics Layout */
        .health-metrics {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }



        .appointment-item,
        .prescription-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .appointment-item:last-child,
        .prescription-item:last-child {
            border-bottom: none;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-upcoming {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .status-active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .health-metrics {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .metric-card {
            padding: 15px;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #283779;
            margin: 5px 0;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>LivMedly</h2>
            <div class="profile-section">

                <div class="profile-image">
                    <input type="file" id="imageUpload" accept="image/*" style="display: none;" onchange="previewImage(event)">
                    <div class="image-container" onclick="document.getElementById('imageUpload').click()">
                        <img src="default-profile.jpg" id="profilePic" alt="User Profile" width="80" height="80">
                    </div>
                </div>


                <style>
                    .profile-image {
                        text-align: center;
                        cursor: pointer;
                        position: relative;
                        display: inline-block;
                    }

                    .image-container {
                        position: relative;
                        display: inline-block;
                    }

                    .profile-image img {
                        width: 80px;
                        height: 80px;
                        border-radius: 50%;
                        object-fit: cover;
                        transition: opacity 0.3s;
                    }

                    .profile-image img:hover {
                        opacity: 0.8;
                    }

                    #removeIcon {
                        position: absolute;
                        top: -5px;
                        right: -5px;
                        background: red;
                        color: white;
                        border-radius: 50%;
                        width: 20px;
                        height: 20px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 14px;
                        cursor: pointer;
                        transition: background 0.3s;
                    }

                    #removeIcon:hover {
                        background: darkred;
                    }

                    button[name="update_metrics"] {
                        background-color: #1e3a8a;
                        /* Dark blue */
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        font-size: 16px;
                        font-weight: bold;
                        border-radius: 8px;
                        cursor: pointer;
                        transition: background 0.3s ease-in-out;
                    }

                    button[name="update_metrics"]:hover {
                        background-color: #1a237e;
                        /* Slightly darker blue */
                    }

                    .health-metrics input[type="text"] {
                        width: 100%;
                        padding: 8px;
                        font-size: 16px;
                        border: 2px solid #ccc;
                        border-radius: 6px;
                        outline: none;
                        transition: border-color 0.3s ease-in-out;
                    }

                    .health-metrics input[type="text"]:focus {
                        border-color: #1e3a8a;
                        /* Blue border on focus */
                        box-shadow: 0 0 5px rgba(30, 58, 138, 0.5);
                    }
                </style>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const userId = "<?php echo $_SESSION['user_id']; ?>"; // Get user ID from PHP session
                        const savedImage = localStorage.getItem("profileImage_" + userId); // Fetch user-specific image

                        if (savedImage) {
                            document.getElementById("profilePic").src = savedImage;
                        } else {
                            document.getElementById("profilePic").src = "img/user.png"; // Default image
                        }
                    });

                    function previewImage(event) {
                        const userId = "<?php echo $_SESSION['user_id']; ?>"; // Get user ID from PHP session
                        const file = event.target.files[0];

                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const imageSrc = e.target.result;
                                document.getElementById("profilePic").src = imageSrc;
                                localStorage.setItem("profileImage_" + userId, imageSrc); // Save per user
                            };
                            reader.readAsDataURL(file);
                        }
                    }

                    function removeProfileImage() {
                        const userId = "<?php echo $_SESSION['user_id']; ?>"; // Get user ID from PHP session
                        document.getElementById("profilePic").src = "img/user.png"; // Reset to default image
                        localStorage.removeItem("profileImage_" + userId); // Remove only for this user
                    }

                    function logout() {
                        const userId = "<?php echo $_SESSION['user_id']; ?>"; // Get user ID
                        localStorage.removeItem("profileImage_" + userId); // Clear only current user's image on logout
                        window.location.href = "logout.php"; // Redirect to logout page
                    }
                </script>


                <h3><?php echo $_SESSION['fullname'] ?></h3>
                <p>Patient_id: <?php echo $_SESSION['user_id']; ?></p>
            </div>
            <ul class="sidebar-menu">
                <li onclick="window.location.href='userdashboard.php'">Dashboard</li>
                <li onclick="window.location.href='display-appointment.php'">My Appointment</li>
                <li>Medical Records</li>
                <li onclick="window.location.href='prescription_details.php'">Prescriptions</li>
                <li onclick="window.location.href='user_chat.html'">Messages</li>
                <li onclick="window.location.href='chatbot/chatbot.html'">Chat Bot</li>
                <li onclick="window.location.href='bmi/bmi.html'">BMI Calculator</li>
                <li onclick="window.location.href='user-settings.php'">Settings</li>
                <li onclick="window.location.href='logout.php'">Logout</li>
            </ul>
        </div>


        <div class="main-content">
            <div class="header">
                <div>
                    <p>
                    <h1>Hello, <?php echo $_SESSION['fullname']; ?>! Welcome back.</h1>
                    </p>

                    <?php
                    if ($daysRemaining !== null) {
                        $roundedDays = round($daysRemaining, 1); // Rounds to one decimal place

                        if ($roundedDays > 1) {
                            echo "Your next appointment is in <strong>" . $roundedDays . " days</strong>.";
                        } elseif ($roundedDays == 1) {
                            echo "Your appointment is <strong>tomorrow</strong>.";
                        } elseif ($roundedDays == 0) {
                            echo "Your appointment is <strong>today</strong>!";
                        } else {
                            echo "Your appointment date has passed.";
                        }
                    } else {
                        echo "You have no upcoming appointments.";
                    }
                    ?>

                    </>
                </div>
                <div class="quick-actions">
                    <button class="button" onclick="window.location.href='spinWheel/spinWheel.html'">
                        Spin Wheel
                    </button>

                    <button class="button" onclick="window.location.href='appointment.php'">Book Appointment</button>
                    <button class="button">Message Doctor</button>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="main-section">
                    <div class="card">
                        <h2>Upcoming Appointments</h2>
                        <?php foreach ($appointments as $row) { ?>
                            <div class="appointment-item">
                                <div>
                                    <h4><?php echo "Dr. " . $row['doctor']; ?></h4>
                                    <p><strong>Specialization:</strong> <?php echo $row['specialization']; ?></p>
                                    <p><strong>Department:</strong> <?php echo $row['department']; ?></p>
                                    <p><strong>Date & Time:</strong>
                                        <?php echo date("F j, Y - g:i A", strtotime($row['date'] . " " . $row['time'])); ?>
                                    </p>
                                </div>
                                <span class="status-badge status-upcoming"><?php echo ucfirst($row['status']); ?></span>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="card">
                        <h2>Current Prescriptions</h2>

                        <?php
                        include 'config.php';
                        // Start session only if it's not already started
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }

                        // Include database connection (if needed)
                        include 'config.php';

                        // Ensure user is logged in
                        if (!isset($_SESSION['user_id'])) {
                            die("<script>alert('Please log in first.'); window.location.href='login.php';</script>");
                        }

                        $user_id = $_SESSION['user_id'];

                        // Fetch prescriptions from the database
                        $stmt = $conn->prepare("SELECT p.medicine_name, p.quantity, p.morning, p.afternoon, p.evening, p.night, p.notes, d.name AS doctor_name 
                            FROM prescriptions p 
                            JOIN doctor d ON p.doctor_id = d.id 
                            WHERE p.user_id = ?");
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Determine dosage times
                                $dosage = [];
                                if ($row['morning']) $dosage[] = "Morning";
                                if ($row['afternoon']) $dosage[] = "Afternoon";
                                if ($row['evening']) $dosage[] = "Evening";
                                if ($row['night']) $dosage[] = "Night";

                                $dosage_text = !empty($dosage) ? implode(", ", $dosage) : "As prescribed";
                        ?>
                                <div class="prescription-item">
                                    <div>
                                        <h4><?php echo htmlspecialchars($row['medicine_name']); ?> (<?php echo $row['quantity']; ?>)</h4>
                                        <p>Take: <?php echo $dosage_text; ?></p>
                                        <p>Prescribed by: Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></p>
                                        <?php if (!empty($row['notes'])) { ?>
                                            <p><strong>Notes:</strong> <?php echo htmlspecialchars($row['notes']); ?></p>
                                        <?php } ?>
                                    </div>
                                    <span class="status-badge status-active">Active</span>
                                </div>
                        <?php
                            }
                        } else {
                            echo "<p>No active prescriptions found.</p>";
                        }

                        $stmt->close();
                        $conn->close();
                        ?>
                    </div>
                    <div class="side-section">
                        <div class="card">
                            <h2>Health Metrics</h2>
                            <form method="POST" action="update-metrics.php">
                                <div class="health-metrics">
                                    <div class="metric-card">
                                        <h4>Blood Pressure</h4>
                                        <input type="text" name="blood_pressure" value="<?php echo isset($_SESSION['bloodpressure']) ? $_SESSION['bloodpressure'] : ''; ?>" required>
                                        <p>Last checked: Today</p>
                                    </div>
                                    <div class="metric-card">
                                        <h4>Heart Rate</h4>
                                        <input type="text" name="heart_rate" value="<?php echo isset($_SESSION['heartrate']) ? $_SESSION['heartrate'] : ''; ?>" required>
                                        <p>Last checked: Today</p>
                                    </div>
                                    <div class="metric-card">
                                        <h4>Blood Group</h4>
                                        <input type="text" name="blood_group" value="<?php echo isset($_SESSION['bloodgroup']) ? $_SESSION['bloodgroup'] : ''; ?>" required>
                                        <p>Last checked: Today</p>
                                    </div>
                                    <div class="metric-card">
                                        <h4>Weight</h4>
                                        <input type="text" name="weight" value="<?php echo isset($_SESSION['weight']) ? $_SESSION['weight'] : ''; ?>" required>
                                        <p>Last checked: Yesterday</p>
                                    </div>
                                </div><br>
                                <center><button type="submit" name="update_metrics">Update Metrics</button> </center>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <h2>Recent Messages</h2>
                        <div class="appointment-item">
                            <div>
                                <h4>Dr. Sarah Smith</h4>
                                <p>Test results are ready</p>
                            </div>
                            <p>1h ago</p>
                        </div>
                        <div class="appointment-item">
                            <div>
                                <h4>Clinic Reception</h4>
                                <p>Appointment confirmation</p>
                            </div>
                            <p>3h ago</p>
                        </div>
                    </div>
                </div>
            </div>
</body>

</html>