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
    die("You must be logged in to update profile.");
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT fullname, username, email, phone, gender, bloodgroup, address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $address = trim($_POST['address']);

    if (!empty($fullname) && !empty($username) && !empty($address)) {
        $update_sql = "UPDATE users SET fullname = ?, username = ?, address = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $fullname, $username, $address, $user_id);

        if ($update_stmt->execute()) {
            echo "<script>alert('Profile updated successfully!'); window.location.href='userdashboard.php';</script>";
        } else {
            echo "<script>alert('Error updating profile.');</script>";
        }
    } else {
        echo "<script>alert('Please fill all fields!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Settings</title>

    
    <!-- ===============================================-->
    <!--Favicons-->
    <!-- ===============================================-->

    <link rel="icon" type="image/png" href="favicon.ico" sizes="16x16">

    
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
    <style>
        body {
            background: #f6f5f7;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            display: flex;
            width: 90%;
            max-width: 900px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .form-container {
            flex: 0.6; /* Reduced size of the form container */
            padding: 20px; /* Reduced padding */
        }
        .welcome-panel {
            flex: 0.4; /* Size of the blue panel */
            background: linear-gradient(to right, #5DB2FF, #004E92);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            flex-direction: column;
            padding: 20px;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        input, textarea {
            background-color: #eee;
            border: none;
            padding: 10px;
            width: calc(100% - 20px); /* Adjust width to account for padding */
            font-family: 'Montserrat', sans-serif;
            border-radius: 5px;
            margin-top: 5px;
        }
        .readonly {
            background: #e9ecef;
            cursor: not-allowed;
        }
        button {
            border-radius: 20px;
            border: 1px solid #1e7bb0;
            background-color: #283779;
            color: #FFFFFF;
            font-size: 14px;
            font-weight: bold;
            padding: 10px;
            text-transform: uppercase;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
        }
        .scrollable {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Update Profile</h1>
            <div class="scrollable">
                <form method="POST" action="">
                    <label>Full Name:</label>
                    <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>

                    <label>Username:</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                    <label>Email (Cannot Change):</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="readonly" readonly>

                    <label>Phone (Cannot Change):</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['phone']); ?>" class="readonly" readonly>

                    <label>Gender (Cannot Change):</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['gender']); ?>" class="readonly" readonly>

                    <label>Blood Group (Cannot Change):</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['bloodgroup']); ?>" class="readonly" readonly>

                    <label>Address:</label>
                    <textarea name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>

                    <button type="submit">Update Profile</button>
                </form>
            </div>
        </div>
        <div class="welcome-panel">
            <h1>Welcome!</h1>
            <p>Update your profile information here.</p>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>