<?php
session_start();
$conn = new mysqli("localhost", "root", "", "livmedly");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];
$messages = [];

// Fetch messages where logged-in user is the recipient or sender
$sql = "SELECT id, sender_id, recipient_id, message, sent_at FROM messages WHERE sender_id = ? OR recipient_id = ? ORDER BY sent_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Montserrat', sans-serif; }
        body { background: #f6f5f7; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background-color: #fff; border-radius: 10px; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); width: 600px; padding: 20px; }
        h1 { text-align: center; margin-bottom: 20px; }
        .chat-box { max-height: 400px; overflow-y: auto; padding: 10px; border: 1px solid #ccc; border-radius: 5px; background: #f9f9f9; }
        .message { display: flex; justify-content: flex-start; margin-bottom: 10px; }
        .message.sent { justify-content: flex-end; }
        .message p { max-width: 70%; padding: 10px; border-radius: 5px; }
        .sent p { background: #283779; color: white; }
        .received p { background: #ddd; color: black; }
        .message-meta { font-size: 12px; color: gray; text-align: right; margin-top: 5px; }
        .reply-form { display: flex; margin-top: 15px; }
        textarea { flex: 1; padding: 8px; border-radius: 5px; border: 1px solid #ccc; resize: none; }
        button { background-color: #28a745; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 5px; margin-left: 10px; }
        button:hover { background-color: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Messages</h1>

        <div class="chat-box">
            <?php if (empty($messages)): ?>
                <p style="text-align: center; color: gray;">No messages found.</p>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message <?= ($msg['sender_id'] == $user_id) ? 'sent' : 'received' ?>">
                        <p><?= htmlspecialchars($msg['message']) ?></p>
                    </div>
                    <p class="message-meta"><?= htmlspecialchars($msg['sent_at']) ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Reply Form -->
        <form method="POST" action="send_reply.php" class="reply-form">
            <textarea name="reply_message" placeholder="Type your reply..." required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
</body>
</html>
