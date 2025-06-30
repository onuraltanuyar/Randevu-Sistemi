<?php
$page_title = 'Randevu Al'; 
require_once 'panel-header.php'; 

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_datetime_str = $_POST['appointment_date'] . ' ' . $_POST['appointment_time'];
    $notes = $_POST['notes'];

    $appointment_timestamp = strtotime($appointment_datetime_str);
    if ($appointment_timestamp < time()) {
        $message = '<div class="message error">Geçmiş bir tarih veya saate randevu alamazsınız.</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO appointments (user_id, appointment_date, notes) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $appointment_datetime_str, $notes);
        
        if ($stmt->execute()) {
            $message = '<div class="message success">Randevunuz başarıyla oluşturuldu. "Güncel Randevular" sayfasından kontrol edebilirsiniz.</div>';
        } else {
            $message = '<div class="message error">Randevu oluşturulurken bir hata oluştu.</div>';
        }
        $stmt->close();
    }
}
?>

<div class="container">
    <h1 class="page-title">Yeni Randevu Oluştur</h1>

    <?php echo $message; ?>

    <form action="randevu-al.php" method="post">
        <label for="appointment_date">Randevu Tarihi:</label>
        <input type="date" id="appointment_date" name="appointment_date" required min="<?php echo date('Y-m-d'); ?>">

        <label for="appointment_time">Randevu Saati:</label>
        <input type="time" id="appointment_time" name="appointment_time" required>

        <label for="notes">Ek Notlar (isteğe bağlı):</label>
        <textarea id="notes" name="notes" rows="4"></textarea>

        <button type="submit">Randevu Al</button>
    </form>
</div>

</body>
</html>