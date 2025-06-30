<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    exit('Giriş yapmalısınız.');
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['message'])) {
    
    $message = trim($_POST['message']);
    
    $conversation_id = $_SESSION['user_id'];
    $sender_id = $_SESSION['user_id'];
    $sender_role = 'user'; 

    $stmt = $conn->prepare("INSERT INTO chat_messages (conversation_id, sender_id, sender_role, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $conversation_id, $sender_id, $sender_role, $message);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
}
?>