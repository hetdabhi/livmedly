<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_POST['sender_id'] ?? null;
    $receiver_id = $_POST['receiver_id'] ?? null;
    $message = $_POST['message'] ?? null;

    // Debugging: Print received data
    file_put_contents("debug_log.txt", "Received: sender_id=$sender_id, receiver_id=$receiver_id, message=$message\n", FILE_APPEND);

    if (!$sender_id || !$receiver_id || empty($message)) {
        echo json_encode(["error" => "⚠️ Missing sender_id, receiver_id, or message"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    
    if (!$stmt) {
        echo json_encode(["error" => "⚠️ SQL prepare failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => "✅ Message stored successfully"]);
    } else {
        echo json_encode(["error" => "❌ SQL execution failed: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>
