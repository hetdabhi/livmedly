<?php
include 'config.php'; // Ensure database connection

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("UPDATE users SET status = 0 WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['recover_id'])) {
    $recover_id = intval($_GET['recover_id']);
    $stmt = $conn->prepare("UPDATE users SET status = 1 WHERE id = ?");
    $stmt->bind_param("i", $recover_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['permanent_delete_id'])) {
    $permanent_delete_id = intval($_GET['permanent_delete_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $permanent_delete_id);
    $stmt->execute();
    $stmt->close();
}

$sql = "SELECT id, fullname, username, email, bloodgroup, gender, phone, address, role, status FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users</title>
    
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
            overflow-x: auto;
        }
        h2 {
            color: #000;
            font-size: 28px;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 14px;
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
        .delete-button, .recover-button, .permanent-delete-button {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            color: white;
        }
        .delete-button { background: #dc3545; }
        .delete-button:hover { background: #c82333; }
        .recover-button { background: #28a745; }
        .recover-button:hover { background: #218838; }
        .permanent-delete-button { background: #000; }
        .permanent-delete-button:hover { background: #333; }
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
        .home-button:hover { background: #218838; }
    </style>
</head>

<body>

    <div class="container">
        <h2>All User Details</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Blood Group</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Role</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["id"] . "</td>
                            <td>" . $row["fullname"] . "</td>
                            <td>" . $row["username"] . "</td>
                            <td>" . $row["email"] . "</td>
                            <td>" . $row["bloodgroup"] . "</td>
                            <td>" . $row["gender"] . "</td>
                            <td>" . $row["phone"] . "</td>
                            <td>" . $row["address"] . "</td>
                            <td>" . ucfirst($row["role"]) . "</td>
                            <td>" . ($row["status"] == 1 ? 'Active' : 'Inactive') . "</td>
                            <td>";
                    if ($row["status"] == 1) {
                        echo "<a href='?delete_id=" . $row["id"] . "' class='delete-button'>Deactivate</a>";
                    } else {
                        echo "<a href='?recover_id=" . $row["id"] . "' class='recover-button'>Activate</a> ";
                        echo "<a href='?permanent_delete_id=" . $row["id"] . "' class='permanent-delete-button'>Delete</a>";
                    }
                    echo "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='11'>No users found</td></tr>";
            }
            $conn->close();
            ?>
        </table>
        <a href="admindashboard.html" class="home-button">Home</a>
    </div>

</body>
</html>
