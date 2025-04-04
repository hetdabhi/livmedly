<?php
session_start(); // Must be the first line
include 'config.php';

// Check if doctor is logged in
if (!isset($_SESSION['doctor_id']) || empty($_SESSION['doctor_id'])) {
    die("<script>alert('Error: Doctor session lost. Please login again.'); window.location.href='login.html';</script>");
}

// Fetch doctor details
$doctor_id = $_SESSION['doctor_id'];
$name = "Unknown";
$specialization = "Not Set";
$profile_image = "img/default-profile.jpg"; // Default profile image

// Fetch doctor details from database
$stmt = $conn->prepare("SELECT name, specialization, profile_image FROM doctor WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $name = $row['name'];
    $specialization = $row['specialization'];
    if (!empty($row['profile_image'])) {
        $profile_image = $row['profile_image']; // Use stored image if available
    }
} else {
    die("<script>alert('Doctor details not found.'); window.history.back();</script>");
}

$stmt->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LivMedly - Doctor Dashboard</title>


    <!-- ===============================================-->
    <!--Favicons-->
    <!-- ===============================================-->

    <link rel="icon" type="image/png" href="favicon.ico" sizes="16x16">


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

        /* Profile Image Container */
        .profile-image {
            position: relative;
            width: 100px;
            height: 100px;
            margin: auto;
        }

        .image-container {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .image-container img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
            transition: 0.3s ease-in-out;
        }

        .image-container img:hover {
            opacity: 0.8;
        }

        #removeIcon {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            font-size: 16px;
            width: 24px;
            height: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            cursor: pointer;
            opacity: 0;
            transition: 0.3s ease-in-out;
        }

        .image-container:hover #removeIcon {
            opacity: 1;
        }

        .doctor-name {
            font-weight: 600;
            margin-top: 10px;
            font-size: 18px;
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

        .upcoming-appointments,
        .patient-requests {
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

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-img {
            display: block;
            width: 100px;
            /* Adjust size */
            height: 100px;
            /* Adjust size */
            border-radius: 50%;
            /* Makes it circular */
            margin: 10px auto;
            /* Centers the image */
        }

        .profile-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 20px 0;
        }

        .image-container {
            position: relative;
            display: inline-block;
        }

        #profilePic {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .doctor-info {
            margin-top: 10px;
        }

        .doctor-name {
            font-size: 20px;
            font-weight: bold;
            color: white;
        }

        .doctor-specialty {
            font-size: 14px;
            color: #d1d1d1;
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
                        <img src="img/doctor.png" id="profilePic" alt="Doctor Profile">
                    </div>
                </div>

                <div class="doctor-info">
                    <div class="doctor-name">Dr. <?php echo htmlspecialchars($name); ?></div>
                    <div class="doctor-specialty"><?php echo htmlspecialchars($specialization); ?></div>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li>Dashboard</li>
                <li>Appointments</li>
                <li>Patient Records</li>
                <li onclick="window.location.href='prescription.php'">Prescriptions</li>
                <!-- <li>Lab Results</li> -->
                <li onclick="window.location.href='doctor_chat.html'">Messages</li>
                <!-- <li>Schedule Management</li> -->
                <li onclick="window.location.href='doctor-settings.php'">Profile Settings</li>
                <li onclick="window.location.href='logout.php'">Logout</li>
            </ul>
        </div>

        <div class="main-content">
            <div class="header">
                <div>
                    <h2>Welcome back,</h2>
                    <h1> Dr.<?php echo htmlspecialchars($name); ?>!</h1>
                    <p>Today's Schedule - Monday, Feb 6, 2025</p>
                </div>
                <button class="button" onclick="window.location.href='doctorappointment.php'">+ New Appointment</button>
            </div>

            <div class="today-stats">
                <div class="stat-card">
                    <h3>Today's Appointments</h3>
                    <div class="stat-value">
                        <p id="appointments">0</p>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>Pending Reports</h3>
                    <div class="stat-value">
                        <p id="pending_reports">0</p>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>New Messages</h3>
                    <div class="stat-value">
                        <p id="new_messages">0</p>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>Total Patients</h3>
                    <div class="stat-value">
                        <p id="total_patients">0</p>
                    </div>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get Doctor ID from PHP session
            const doctorId = "<?php echo isset($_SESSION['doctor_id']) ? $_SESSION['doctor_id'] : ''; ?>";

            if (!doctorId) {
                console.error("Doctor session not found!");
                return;
            }

            const profilePicElement = document.getElementById("profilePic");
            if (!profilePicElement) {
                console.error("Profile picture element not found!");
                return;
            }

            // Try to get saved profile image from localStorage
            const savedImage = localStorage.getItem("doctorProfileImage_" + doctorId);
            if (savedImage) {
                profilePicElement.src = savedImage;
            } else {
                // If no saved image in localStorage, fallback to the database image
                fetch("fetch_doctor_image.php?doctor_id=" + doctorId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.profile_image) {
                            profilePicElement.src = data.profile_image;
                        } else {
                            profilePicElement.src = "img/doctor.png"; // Default doctor profile image
                        }
                    })
                    .catch(error => console.error("Error fetching profile image:", error));
            }
        });

        function previewImage(event) {
            const doctorId = "<?php echo isset($_SESSION['doctor_id']) ? $_SESSION['doctor_id'] : ''; ?>";

            if (!doctorId) {
                console.error("Doctor session not found!");
                return;
            }

            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageSrc = e.target.result;
                    document.getElementById("profilePic").src = imageSrc;
                    localStorage.setItem("doctorProfileImage_" + doctorId, imageSrc); // Save per doctor
                };
                reader.readAsDataURL(file);
            }
        }

        function removeProfileImage() {
            const doctorId = "<?php echo isset($_SESSION['doctor_id']) ? $_SESSION['doctor_id'] : ''; ?>";

            if (!doctorId) {
                console.error("Doctor session not found!");
                return;
            }

            document.getElementById("profilePic").src = "img/doctor-default.png"; // Reset to default doctor image
            localStorage.removeItem("doctorProfileImage_" + doctorId); // Remove only for this doctor
        }

        function logout() {
            const doctorId = "<?php echo isset($_SESSION['doctor_id']) ? $_SESSION['doctor_id'] : ''; ?>";

            if (doctorId) {
                localStorage.removeItem("doctorProfileImage_" + doctorId); // Clear only current doctor's image on logout
            }

            window.location.href = "logout.php"; // Redirect to logout page
        }
    </script>

    <script>
        // Fetch dashboard data from the PHP API
        fetch("http://localhost/livmedly/doctordashboard.php")
            .then(response => response.json())
            .then(data => {
                document.getElementById("appointments").textContent = data.appointments;
                document.getElementById("pending_reports").textContent = data.pending_reports;
                document.getElementById("new_messages").textContent = data.new_messages;
                document.getElementById("total_patients").textContent = data.total_patients.toLocaleString();
            })
            .catch(error => console.error("Error fetching data:", error));
    </script>
</body>

</html>