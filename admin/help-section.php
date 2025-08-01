<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Requests</title>

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
            max-width: 1000px;
            background: white;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow-x: auto;
        }

        h2 {
            color: #000;
            font-size: 28px;
            font-weight: 600;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th, td {
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

        td {
            word-wrap: break-word;
            overflow-wrap: break-word;
            max-width: 200px;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        tr:hover {
            background: #e3f2fd;
        }

        .delete-button {
            padding: 6px 12px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }

        .delete-button:hover {
            background: #c82333;
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
        <h2>Help Requests</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Submitted At</th>
                    <th>Action</th>
                </tr>
                <?php
                include 'config.php';
                if (isset($_GET['delete_id'])) {
                    $delete_id = intval($_GET['delete_id']);
                    $stmt = $conn->prepare("DELETE FROM help WHERE id = ?");
                    $stmt->bind_param("i", $delete_id);
                    $stmt->execute();
                    $stmt->close();
                }
                
                $sql = "SELECT id, name, email, message, submitted_at FROM help";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row["id"] . "</td>
                                <td>" . $row["name"] . "</td>
                                <td>" . $row["email"] . "</td>
                                <td style='max-width: 300px; word-wrap: break-word; overflow-wrap: break-word; text-align: left;'>" . $row["message"] . "</td>
                                <td>" . $row["submitted_at"] . "</td>
                                <td><a href='?delete_id=" . $row["id"] . "' class='delete-button'>Delete</a></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No help requests found</td></tr>";
                }
                $conn->close();
                ?>
            </table>
        </div>
        <a href="admindashboard.html" class="home-button">Home</a>
    </div>

</body>

</html>