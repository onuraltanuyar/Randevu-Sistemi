<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php'; 
if (!isset($conn) || !$conn) {
    http_response_code(500); 
    exit('Veritabanı bağlantı nesnesi ($conn) bulunamadı. db.php dosyasını kontrol edin.');
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); 
    exit('Giriş yapmalısınız. Oturum bilgisi bulunamadı.');
}

$conversation_id = $_SESSION['user_id'];

$sql = "SELECT sender_role, message, timestamp FROM chat_messages WHERE conversation_id = ? ORDER BY timestamp ASC";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    exit('SQL sorgusu hazırlanamadı: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $conversation_id);

if (!$stmt->execute()) {
    http_response_code(500);
    exit('SQL sorgusu çalıştırılamadı: ' . htmlspecialchars($stmt->error));
}

$result = $stmt->get_result();

$output = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $message_class = ($row['sender_role'] == 'user') ? 'message-user' : 'message-admin';
        $output .= '<div class="message-bubble ' . $message_class . '">';
        $output .= htmlspecialchars($row['message']);
        $output .= '</div>';
    }
} else {
    $output = "<p style='text-align:center; color: var(--text-secondary);'>Sohbet geçmişi bulunamadı. İlk mesajı siz gönderin!</p>";
}

echo $output;

$stmt->close();
?>