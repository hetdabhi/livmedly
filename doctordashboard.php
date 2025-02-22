<?php
session_start();
include 'config.php';

// Check if the doctor is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first!'); window.location.href='doctor_login.html';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch doctor details from the database
$stmt = $conn->prepare("SELECT name, email, phone_number, gender, blood_group, specialization, address FROM doctor WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $gender, $blood_group, $specialization, $address);
$stmt->fetch();
$stmt->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LivMedly - Doctor Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,600,800');

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
            position: fixed;
            height: 100vh;
        }

        .doctor-profile {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .doctor-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #fff;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: #004E92;
        }

        .doctor-name {
            font-weight: 600;
            margin: 10px 0 5px;
        }

        .doctor-specialty {
            font-size: 14px;
            opacity: 0.8;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin: 5px 0;
            padding: 12px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .sidebar-menu li:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 30px;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .today-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            color: #004E92;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: #283779;
        }

        .appointments-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .upcoming-appointments, .patient-requests {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .appointment-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .appointment-time {
            color: #004E92;
            font-weight: 600;
        }

        .appointment-patient {
            flex: 1;
            margin: 0 20px;
        }

        .appointment-type {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            background: #e8f5ff;
            color: #004E92;
        }

        .status-urgent {
            background: #ffe8e8;
            color: #d92626;
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
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="doctor-profile">
                <div class="doctor-avatar">👨‍⚕️</div>
                <div class="doctor-name">Dr.  <?php echo htmlspecialchars($name); ?></div>
                <div class="doctor-specialty"><?php echo htmlspecialchars($specialization); ?></div>
            </div>
            <ul class="sidebar-menu">
                <li>Dashboard</li>
                <li >Appointments</li>
                <li>Patient Records</li>
                <li>Prescriptions</li>
                <li>Lab Results</li>
                <li>Messages</li>
                <li>Schedule Management</li>
                <li>Profile Settings</li>
                <li onclick="window.location.href='logout.php'">logout</li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="header">
                <div>
                    <h2>Welcome back,</h2><h1> Dr.<?php echo htmlspecialchars($name); ?>!</h1>
                    <p>Today's Schedule - Monday, Feb 6, 2025</p>
                </div>
                <button class="button">+ New Appointment</button>
            </div>

            <div class="today-stats">
                <div class="stat-card">
                    <h3>Today's Appointments</h3>
                    <div class="stat-value">8</div>
                </div>
                <div class="stat-card">
                    <h3>Pending Reports</h3>
                    <div class="stat-value">3</div>
                </div>
                <div class="stat-card">
                    <h3>New Messages</h3>
                    <div class="stat-value">5</div>
                </div>
                <div class="stat-card">
                    <h3>Total Patients</h3>
                    <div class="stat-value">1,247</div>
                </div>
            </div>

            <div class="appointments-section">
                <div class="upcoming-appointments">
                    <h2>Today's Schedule</h2>
                    <div class="appointment-item">
                        <div class="appointment-time">09:00 AM</div>
                        <div class="appointment-patient">
                            <strong>Sarah Johnson</strong>
                            <div>Follow-up Consultation</div>
                        </div>
                        <span class="appointment-type">Check-up</span>
                    </div>
                    <div class="appointment-item">
                        <div class="appointment-time">10:30 AM</div>
                        <div class="appointment-patient">
                            <strong>Michael Brown</strong>
                            <div>Heart Examination</div>
                        </div>
                        <span class="appointment-type status-urgent">Urgent</span>
                    </div>
                    <div class="appointment-item">
                        <div class="appointment-time">02:00 PM</div>
                        <div class="appointment-patient">
                            <strong>Emily Davis</strong>
                            <div>Initial Consultation</div>
                        </div>
                        <span class="appointment-type">New Patient</span>
                    </div>
                </div>

                <div class="patient-requests">
                    <h2>Patient Requests</h2>
                    <div class="appointment-item">
                        <div class="appointment-patient">
                            <strong>Robert Smith</strong>
                            <div>Prescription Renewal</div>
                        </div>
                        <button class="button">Review</button>
                    </div>
                    <div class="appointment-item">
                        <div class="appointment-patient">
                            <strong>Lisa Anderson</strong>
                            <div>Lab Results Review</div>
                        </div>
                        <button class="button">Review</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>