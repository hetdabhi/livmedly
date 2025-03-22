<?php
include 'config.php'; // Ensure this file connects to your database

$sql = "SELECT id, name, phone, department, doctor, date, time, status FROM appointments";
$result = $conn->query($sql);

// Check if the connection and query are successful
if (!$result) {
    die("Error in SQL query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment List</title>

    <!-- ===============================================-->
    <!--Favicons-->
    <!-- ===============================================-->

    <link rel="icon" type="image/png" href="favicon.ico" sizes="16x16">

    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            background: white;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        h1 {
            color: rgb(0, 0, 0);
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 16px;
        }

        th {
            background: #004E92;
            color: white;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        tr:hover {
            background: #e3f2fd;
        }

        .home-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            border-radius: 5px;
            transition: 0.3s;
        }

        .home-button:hover {
            background: #218838;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Appointment List</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Patient Name</th>
                <th>Phone</th>
                <th>Department</th>
                <th>Doctor Name</th>
                <th>Appointment Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . $row["id"] . "</td>
                        <td>" . $row["name"] . "</td>
                        <td>" . $row["phone"] . "</td>
                        <td>" . $row["department"] . "</td>
                        <td>" . $row["doctor"] . "</td>
                        <td>" . $row["date"] . "</td>
                        <td>" . $row["time"] . "</td>
                        <td>" . $row["status"] . "</td>
                      </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No appointments found</td></tr>";
            }
            $conn->close();
            ?>
        </table>
        <a href="admindashboard.html" class="home-button">Home</a>
    </div>

</body>

</html>