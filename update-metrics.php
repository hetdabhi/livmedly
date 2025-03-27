<?php
session_start();
include('config.php'); // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure the user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("Error: User not logged in.");
    }

    $user_id = $_SESSION['user_id'];

    // Get and sanitize input values
    $blood_pressure = isset($_POST['blood_pressure']) ? trim($_POST['blood_pressure']) : '';
    $heart_rate = isset($_POST['heart_rate']) ? trim($_POST['heart_rate']) : '';
    $blood_group = isset($_POST['blood_group']) ? trim($_POST['blood_group']) : '';
    $weight = isset($_POST['weight']) ? trim($_POST['weight']) : '';  // ✅ Added weight

    // Ensure required fields are not empty
    if ($blood_pressure === "" || $heart_rate === "" || $blood_group === "" || $weight === "") {
        die("Error: All fields are required.");
    }

    // ✅ Update the database securely, including weight
    $sql = "UPDATE users SET blood_pressure=?, heart_rate=?, blood_group=?, weight=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $blood_pressure, $heart_rate, $blood_group, $weight, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        // Fetch updated data from the database
        $fetch_sql = "SELECT blood_pressure, heart_rate, blood_group, weight FROM users WHERE id=?";
        $fetch_stmt = mysqli_prepare($conn, $fetch_sql);
        mysqli_stmt_bind_param($fetch_stmt, "i", $user_id);
        mysqli_stmt_execute($fetch_stmt);
        $result = mysqli_stmt_get_result($fetch_stmt);
        $updated_data = mysqli_fetch_assoc($result);

        // ✅ Update session with new values including weight
        $_SESSION['bloodpressure'] = $updated_data['blood_pressure'];
        $_SESSION['heartrate'] = $updated_data['heart_rate'];
        $_SESSION['bloodgroup'] = $updated_data['blood_group'];
        $_SESSION['weight'] = $updated_data['weight']; // ✅ Added weight to session

        // Close statements
        $stmt->close();
        $fetch_stmt->close();
        $conn->close();

        // Redirect to user dashboard with success message
        echo "<script>
                alert('Health data updated successfully!');
                window.location.href = 'userdashboard.php';
              </script>";
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }
}

// Close connection
$conn->close();
?>
