<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: giris.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'] ?? 'user';


$admin_error_message = '';
if (isset($_POST['admin_login_submit'])) {
    if ($role != 'admin') {
        $password_to_verify = $_POST['admin_password'];
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user_data && password_verify($password_to_verify, $user_data['password'])) {
            $_SESSION['admin_auth_success'] = true;
            header("Location: anasayfa.php");
            exit();
        } else {
            $admin_error_message = "Şifre hatalı. Lütfen tekrar deneyin.";
        }
    } else {
        $admin_error_message = "Bu alana erişim yetkiniz bulunmamaktadır.";
    }
}

$next_appointment_sql = "SELECT appointment_date, status FROM appointments 
                         WHERE user_id = ? AND appointment_date >= NOW() AND status IN ('pending', 'confirmed') 
                         ORDER BY appointment_date ASC LIMIT 1";
$stmt_next = $conn->prepare($next_appointment_sql);
$stmt_next->bind_param("i", $user_id);
$stmt_next->execute();
$next_appointment = $stmt_next->get_result()->fetch_assoc();
$stmt_next->close();

$stats = ['upcoming' => 0, 'total' => 0];
$stmt_upcoming = $conn->prepare("SELECT COUNT(id) as count FROM appointments WHERE user_id = ? AND appointment_date >= NOW() AND status != 'canceled'");
$stmt_upcoming->bind_param("i", $user_id);
$stmt_upcoming->execute();
$stats['upcoming'] = $stmt_upcoming->get_result()->fetch_assoc()['count'] ?? 0;
$stmt_upcoming->close();

$stmt_total = $conn->prepare("SELECT COUNT(id) as count FROM appointments WHERE user_id = ?");
$stmt_total->bind_param("i", $user_id);
$stmt_total->execute();
$stats['total'] = $stmt_total->get_result()->fetch_assoc()['count'] ?? 0;
$stmt_total->close();

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoctorAltan - Kontrol Paneli</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
        :root {
            --dark-bg: #0a192f; --light-bg: #112240; --text-primary: #ccd6f6;
            --text-secondary: #8892b0; --accent: #64ffda; --border-color: #30415d;
            --info-color: #3B82F6; --warning-color: #F59E0B;
            --error-border: #ff5252;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--dark-bg); color: var(--text-primary); }
        .navbar { background-color: var(--light-bg); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); }
        .navbar .logo { font-size: 1.5em; font-weight: 600; color: var(--accent); }
        .navbar .user-info { display: flex; align-items: center; }
        .navbar .user-info span { margin-right: 20px; }
        .navbar .user-info a { color: var(--text-primary); text-decoration: none; background-color: var(--border-color); padding: 8px 15px; border-radius: 4px; transition: all 0.3s ease; }
        .navbar .user-info a:hover { background-color: var(--accent); color: var(--dark-bg); }
        .system-announcement { background-color: #ffc107; color: #333; text-align: center; padding: 15px; font-size: 1.1em; border-bottom: 1px solid #e0a800; font-weight: 500; }
        .container { padding: 40px; }
        .stats-cards { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-card { background-color: var(--light-bg); border: 1px solid var(--border-color); padding: 25px; border-radius: 8px; flex-grow: 1; text-align: center; }
        .stat-card .icon { font-size: 2.5em; margin-bottom: 15px; }
        .stat-card .icon.upcoming { color: var(--info-color); }
        .stat-card .icon.total { color: var(--accent); }
        .stat-card .number { font-size: 2em; font-weight: 600; display: block; }
        .stat-card .text { color: var(--text-secondary); }
        .next-appointment-panel { background: linear-gradient(45deg, var(--info-color), var(--accent)); color: var(--dark-bg); padding: 25px; border-radius: 8px; text-align: center; margin-bottom: 30px;}
        .next-appointment-panel .icon { font-size: 3em; margin-bottom: 10px; }
        .next-appointment-panel h3 { font-size: 1.5em; }
        .next-appointment-panel p { font-size: 1.2em; font-weight: 600; color: var(--dark-bg); }
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
        .panel { background-color: var(--light-bg); border: 1px solid var(--border-color); border-radius: 8px; padding: 25px; }
        .panel-title { color: var(--accent); font-size: 1.8em; font-weight: 500; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px; }
        .panel-buttons { display: flex; flex-wrap: wrap; gap: 15px; }
        .panel-buttons a { flex-grow: 1; text-align: center; background-color: var(--dark-bg); color: var(--text-primary); text-decoration: none; padding: 20px; border-radius: 6px; border: 1px solid var(--border-color); transition: all 0.3s ease; min-width: 200px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .panel-buttons a:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-3px); box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        .admin-login-form p { color: var(--text-secondary); margin-bottom: 15px; }
        .admin-login-form form { background-color: transparent; padding: 0; border: none; }
        .admin-login-form label { display: block; margin-bottom: 8px; color: var(--text-secondary); }
        .admin-login-form input { width: 100%; padding: 12px; margin-bottom: 20px; background-color: var(--dark-bg); border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-primary); font-size: 1em; }
        .admin-login-form button { width: 100%; padding: 12px; background-color: var(--accent); color: var(--dark-bg); border: none; border-radius: 4px; cursor: pointer; font-size: 1.1em; font-weight: 600; }
        .error-message { color: var(--error-border); margin-bottom: 15px; text-align: center;}
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">DoctorAltan</div>
        <div class="user-info">
            <span>Hoş Geldin, <strong><?php echo htmlspecialchars($username); ?></strong>!</span>
            <a href="cikis.php">Çıkış Yap</a>
        </div>
    </nav>

    <?php
    $announcement_result = $conn->query("SELECT message FROM announcements WHERE is_active = 1 ORDER BY id DESC LIMIT 1");
    if ($announcement_result && $announcement_result->num_rows > 0):
        $announcement = $announcement_result->fetch_assoc();
    ?>
    <div class="system-announcement">
        <strong>Duyuru:</strong> <?php echo htmlspecialchars($announcement['message']); ?>
    </div>
    <?php endif; ?>

    <div class="container">
        <div class="stats-cards">
            <div class="stat-card">
                <div class="icon upcoming"><i class="fas fa-calendar-check"></i></div>
                <span class="number"><?php echo $stats['upcoming']; ?></span>
                <span class="text">Yaklaşan Randevu</span>
            </div>
            <div class="stat-card">
                <div class="icon total"><i class="fas fa-calendar-day"></i></div>
                <span class="number"><?php echo $stats['total']; ?></span>
                <span class="text">Toplam Randevu</span>
            </div>
        </div>

        <?php if ($next_appointment): $next_date = new DateTime($next_appointment['appointment_date']); ?>
            <div class="next-appointment-panel">
                <div class="icon"><i class="fas fa-bell fa-shake"></i></div>
                <h3>Sıradaki Randevunuz</h3>
                <p><?php echo $next_date->format('d F Y, H:i'); ?></p>
            </div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="panel" id="user-panel">
                <h2 class="panel-title"><i class="fas fa-user-cog"></i> Kullanıcı Paneli</h2>
                <div class="panel-buttons">
                    <a href="randevu-al.php"><i class="fas fa-plus"></i> Randevu Al</a>
                    <a href="guncel-randevular.php"><i class="fas fa-list"></i> Güncel Randevularım</a>
                    <a href="gecmis-randevular.php"><i class="fas fa-history"></i> Geçmiş Randevularım</a>
                </div>
            </div>

            <div class="panel" id="support-panel">
                <h2 class="panel-title"><i class="fas fa-headset"></i> Yardım & Destek</h2>
                <div class="panel-buttons">
                    <a href="canli-destek.php"><i class="fas fa-comments"></i> Canlı Desteğe Bağlan</a>
                </div>
            </div>

           <?php if ($role != 'admin'): ?>
            <div class="panel" id="admin-panel">
                <h2 class="panel-title"><i class="fas fa-user-shield"></i> Yönetici Paneli</h2>
                <?php if (isset($_SESSION['admin_auth_success']) && $_SESSION['admin_auth_success'] === true): ?>
                    <div class="panel-buttons">
                        <a href="randevu-takvimi.php"><i class="fas fa-users-cog"></i> Randevuları Yönet</a>
                        <a href="duyuru-yap.php"><i class="fas fa-bullhorn"></i> Duyuru Yap</a>
                        <a href="../anasayfa.php#bakim"><i class="fas fa-inbox"></i> Destek Talepleri</a>
                    </div>
                <?php else: ?>
                    <div class="admin-login-form">
                        <p>Yönetici paneline erişmek için lütfen şifrenizle kimliğinizi doğrulayın.</p>
                        <?php if (!empty($admin_error_message)): ?>
                            <p class="error-message"><?php echo $admin_error_message; ?></p>
                        <?php endif; ?>
                        <form action="anasayfa.php" method="post">
                            <label for="admin_password">Şifreniz:</label>
                            <input type="password" id="admin_password" name="admin_password" required>
                            <button type="submit" name="admin_login_submit">Panele Eriş</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>