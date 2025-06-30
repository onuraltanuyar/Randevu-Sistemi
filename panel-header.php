<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: giris.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Kontrol Paneli'; ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
        :root {
            --dark-bg: #0a192f; --light-bg: #112240; --text-primary: #ccd6f6;
            --text-secondary: #8892b0; --accent: #64ffda; --border-color: #30415d;
            --error-bg: rgba(255, 82, 82, 0.1); --error-border: #ff5252;
            --success-bg: rgba(100, 255, 218, 0.1); --success-border: var(--accent);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--dark-bg); color: var(--text-primary); }
        .navbar { background-color: var(--light-bg); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); }
        .navbar .logo a { font-size: 1.5em; font-weight: 600; color: var(--accent); text-decoration: none;}
        .navbar .user-info { display: flex; align-items: center; }
        .navbar .user-info span { margin-right: 20px; }
        .navbar .user-info a { color: var(--text-primary); text-decoration: none; background-color: var(--border-color); padding: 8px 15px; border-radius: 4px; transition: all 0.3s ease; }
        .navbar .user-info a:hover { background-color: var(--accent); color: var(--dark-bg); }
        .container { padding: 40px; max-width: 1200px; margin: 0 auto; }
        .page-title { color: var(--accent); margin-bottom: 25px; font-weight: 600; }

        form { background-color: var(--light-bg); padding: 25px; border-radius: 8px; border: 1px solid var(--border-color); }
        form label { display: block; margin-bottom: 8px; color: var(--text-secondary); }
        form input, form select, form textarea { width: 100%; padding: 12px; margin-bottom: 20px; background-color: var(--dark-bg); border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-primary); font-size: 1em; }
        form button { padding: 12px 25px; background-color: var(--accent); color: var(--dark-bg); border: none; border-radius: 4px; cursor: pointer; font-size: 1.1em; font-weight: 600; }

        table { width: 100%; border-collapse: collapse; background-color: var(--light-bg); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid var(--border-color); }
        th { color: var(--accent); }
        tr:hover { background-color: #1a2d4f; }
        
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .message.error { background-color: var(--error-bg); border: 1px solid var(--error-border); color: var(--error-border); }
        .message.success { background-color: var(--success-bg); border: 1px solid var(--success-border); color: var(--success-border); }

        .btn-cancel { background-color: #c9302c; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; }

        .system-announcement {
            background-color: #ffc107;
            color: #333;
            text-align: center;
            padding: 15px;
            font-size: 1.1em;
            border-bottom: 1px solid #e0a800;
            font-weight: 500;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo"><a href="anasayfa.php">DoctorAltan</a></div>
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