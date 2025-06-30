<?php
$page_title = 'Duyuru Önerisi Yap';
require_once 'panel-header.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_proposal'])) {
    
    $proposal_message = trim($_POST['proposal_message']);

    if(empty($proposal_message)) {
        $message = '<div class="message error">Mesaj alanı boş bırakılamaz.</div>';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO announcements (message, submitted_by_user_id, is_active, is_proposal) 
             VALUES (?, ?, 0, 1)"
        );
        $stmt->bind_param("si", $proposal_message, $user_id); 
        
        if ($stmt->execute()) {
            $message = '<div class="message success">Duyuru öneriniz başarıyla yönetici onayına gönderildi. Teşekkür ederiz!</div>';
        } else {
            $message = '<div class="message error">Öneriniz gönderilirken bir hata oluştu.</div>';
        }
        $stmt->close();
    }
}
?>

<div class="container">
    <h1 class="page-title"><i class="fas fa-lightbulb"></i> Yöneticiye Duyuru Önerisi Gönder</h1>
    <p class="page-subtitle" style="text-align:left; max-width:100%;">
        Buradan göndereceğiniz mesajlar doğrudan yayınlanmaz. Yönetici tarafından incelendikten sonra uygun görülürse tüm sitede duyurulur. Lütfen fikir ve önerilerinizi bizimle paylaşın.
    </p>

    <?php echo $message; ?>

    <form method="POST" action="duyuru-yap.php" style="padding: 25px; border: 1px solid var(--border-color); border-radius: 8px;">
        <label for="proposal_message" style="font-size:1.2em; color:var(--text-secondary);">Öneri Metni:</label>
        <br><br>
        <textarea name="proposal_message" id="proposal_message" rows="5" style="width:100%; background-color:var(--light-bg); color:var(--text-primary); border:1px solid var(--border-color); padding:10px;" placeholder="Yayınlanmasını istediğiniz duyuru metnini veya önerinizi buraya yazın..." required></textarea>
        <br><br>
        <button type="submit" name="submit_proposal" style="width:100%;">Öneriyi Onaya Gönder</button>
    </form>
</div>

</body>
</html>