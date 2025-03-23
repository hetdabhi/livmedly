<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Payment</title>

    <link rel="icon" type="image/png" href="favicon.ico" sizes="16x16">
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
            max-width: 1200px;
            background: white;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        h2 {
            color: #000;
            font-size: 28px;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 16px;
            word-wrap: break-word;
        }

        th {
            background: #004E92;
            color: white;
            font-weight: 600;
            white-space: nowrap;
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

        @media screen and (max-width: 768px) {
            .container {
                width: 95%;
                padding: 15px;
            }

            h2 {
                font-size: 24px;
            }

            table {
                font-size: 14px;
            }

            .home-button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Revenue Payment Details</h2>
        <table>
            <tr>
                <th>Payment ID</th>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Amount (₹)</th>
                <th>Payment Date</th>
                <th>Status</th>
            </tr>

            <?php
            include 'config.php';

            $sql = "SELECT p.payment_id, 
               COALESCE(u.fullname, 'Unknown Patient') AS patient_name, 
               COALESCE(d.name, 'Unknown Doctor') AS doctor_name, 
               p.amount, 
               p.payment_date, 
               p.status 
        FROM payments p
        LEFT JOIN users u ON p.patient_id = u.id
        LEFT JOIN doctor d ON p.doctor_id = d.id
        ORDER BY p.payment_date DESC";


            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["payment_id"] . "</td>
                            <td>" . $row["patient_name"] . "</td>
                            <td>" . $row["doctor_name"] . "</td>
                            <td>₹" . $row["amount"] . "</td>
                            <td>" . $row["payment_date"] . "</td>
                            <td>" . ($row["status"] == 1 ? 'Paid' : 'Pending') . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No payments found</td></tr>";
            }

            $conn->close();
            ?>

        </table>
        <a href="admindashboard.html" class="home-button">Home</a>
    </div>

</body>

</html>