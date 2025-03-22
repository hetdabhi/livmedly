<?php
// Include database connection
include 'config.php';
session_start();

// Check if the doctor is logged in
if (!isset($_SESSION['doctor_id'])) {
    die("<script>alert('Unauthorized access. Please log in.'); window.location.href='login.html';</script>");
}

$doctor_id = $_SESSION['doctor_id']; // Doctor's ID from session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_email = trim($_POST['email']);
    $notes = trim($_POST['notes']);

    // Verify if the doctor exists
    $stmt = $conn->prepare("SELECT id FROM doctor WHERE id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $stmt->close();
        die("<script>alert('Error: Doctor ID does not exist.'); window.history.back();</script>");
    }
    $stmt->close();

    // Get patient ID using email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $patient_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("<script>alert('Error: Patient not found.'); window.history.back();</script>");
    }

    $patient = $result->fetch_assoc();
    $patient_id = $patient['id'];

    // Insert prescription details
    $stmt = $conn->prepare("INSERT INTO prescriptions (doctor_id, user_id, medicine_name, quantity, morning, afternoon, evening, night, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("<script>alert('Error: Failed to prepare SQL statement. " . mysqli_error($conn) . "'); window.history.back();</script>");
    }

    foreach ($_POST['medicine_name'] as $index => $medicine_name) {
        $quantity = $_POST['quantity'][$index];
        $morning = isset($_POST['morning'][$index]) ? 1 : 0;
        $afternoon = isset($_POST['afternoon'][$index]) ? 1 : 0;
        $evening = isset($_POST['evening'][$index]) ? 1 : 0;
        $night = isset($_POST['night'][$index]) ? 1 : 0;

        $stmt->bind_param("iisiiiiis", $doctor_id, $patient_id, $medicine_name, $quantity, $morning, $afternoon, $evening, $night, $notes);
        
        if (!$stmt->execute()) {
            die("<script>alert('Error: Failed to insert prescription. " . mysqli_error($conn) . "'); window.history.back();</script>");
        }
    }

    $stmt->close();
    $conn->close();

    echo "<script>alert('Prescription saved successfully!'); window.location.href='prescription.php';</script>";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Form</title>

    
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
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 800px;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
            color: black;
        }

        input, textarea {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            padding: 12px;
            width: 100%;
            font-family: 'Montserrat', sans-serif;
            border-radius: 5px;
            margin-bottom: 10px;
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
            background-color: #283779;
            color: white;
        }

        button {
            border-radius: 5px;
            border: none;
            padding: 12px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #283779;
            color: #FFFFFF;
            width: 100%;
            margin-top: 10px;
            transition: 0.3s;
        }

        button:hover {
            background-color: #1e4b91;
        }

        .add-medicine-btn {
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            border: none;
            padding: 8px;
            cursor: pointer;
            margin-top: 10px;
        }

        .add-medicine-btn:hover {
            background-color: #0056b3;
        }

        .remove-medicine-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px;
            cursor: pointer;
            border-radius: 5px;
        }

        .remove-medicine-btn:hover {
            background-color: #b02a37;
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
    </style>
</head>

<body>
    <div class="container">
        <h1>Write a Prescription</h1>
        <form action="prescription.php" method="POST">
            <input type="email" name="email" placeholder="Patient Email" required>

            <table id="medicineTable">
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Quantity</th>
                        <th>Morning</th>
                        <th>Afternoon</th>
                        <th>Evening</th>
                        <th>Night</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="medicine_name[]" required></td>
                        <td><input type="number" name="quantity[]" required></td>
                        <td><input type="checkbox" name="morning[0]" value="1"></td>
                        <td><input type="checkbox" name="afternoon[0]" value="1"></td>
                        <td><input type="checkbox" name="evening[0]" value="1"></td>
                        <td><input type="checkbox" name="night[0]" value="1"></td>
                        <td><button type="button" class="remove-medicine-btn" onclick="removeRow(this)">✖</button></td>
                    </tr>
                </tbody>
            </table>

            <button type="button" class="add-medicine-btn" onclick="addMedicineRow()">+ Add Medicine</button>

            <textarea name="notes" placeholder="Doctor's Notes (optional)"></textarea>

            <button type="submit">Submit Prescription</button>
        </form>

        <div class="home-btn-container">
            <a href="doctordashboard.php" class="home-btn">Home</a>
        </div>
    </div>
</body>
</html>
