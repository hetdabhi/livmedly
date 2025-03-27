<?php
session_start();
include('config.php'); // Database connection

$doctor_id = $_SESSION['doctor_id'] ?? 0;

if (!$doctor_id) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    $target_dir = "uploads/"; // Directory where images are saved
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Create folder if not exists
    }

    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . time() . "_" . $image_name; // Unique filename

    // Check if the file is an actual image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Update database with new image path
            $sql = "UPDATE doctors SET profile_image=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $target_file, $doctor_id);
            mysqli_stmt_execute($stmt);

            echo json_encode(["success" => true, "image_url" => $target_file]);
        } else {
            echo json_encode(["success" => false, "message" => "Error uploading file."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid image file."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No image uploaded."]);
}
?>
