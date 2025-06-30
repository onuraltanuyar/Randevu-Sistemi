<?php
require_once 'db.php';

$is_logged_in = isset($_SESSION['user_id']);
$redirect_url = '';
$redirect_message = '';
$username = '';

if ($is_logged_in) {
    $username = htmlspecialchars($_SESSION['username']);
    $is_admin = isset($_SESSION['role']) && $_SESSION['role'] == 'admin';

    if ($is_admin) {
        $redirect_url = 'admin/tum-randevular.php'; 
        $redirect_message = 'Yönetici paneline yönlendiriliyorsunuz...';
    } else {
        $redirect_url = 'anasayfa.php'; 
        $redirect_message = 'Kontrol panelinize yönlendiriliyorsunuz...';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php if ($is_logged_in): ?>
    <meta http-equiv="refresh" content="3;url=<?php echo $redirect_url; ?>">
    <?php endif; ?>

    <title>Yönlendiriliyorsunuz...</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        :root {
            --dark-bg: #0a192f;
            --light-bg: #112240;
            --text-primary: #ccd6f6;
            --text-secondary: #8892b0;
            --accent: #64ffda;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-primary);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
        }
        .content-wrapper {
            opacity: 0;
            animation: fadeIn 0.5s forwards;
        }
        @keyframes fadeIn {
            to { opacity: 1; }
        }
        h1 {
            color: var(--accent);
            margin-bottom: 15px;
            font-size: 2.5em;
            font-weight: 600;
        }
        p {
            color: var(--text-secondary);
            margin-bottom: 30px;
            font-size: 1.1em;
            max-width: 500px;
        }
        .action-links a {
            color: var(--accent);
            text-decoration: none;
            font-size: 1.2em;
            margin: 0 15px;
            padding: 10px 20px;
            border: 1px solid var(--accent);
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        .action-links a:hover {
            background-color: rgba(100, 255, 218, 0.1);
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 5px solid var(--border-color);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 40px auto 0;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <div class="content-wrapper">
        <?php if ($is_logged_in): ?>
            <h1>Hoş Geldin, <?php echo $username; ?>!</h1>
            <p><?php echo $redirect_message; ?></p>
            <div class="loader"></div>

        <?php else: ?>
            <h1><i class="fas fa-notes-medical"></i> DoctorAltan | Hoş geldin!</h1>
            <p>Lütfen devam etmek için giriş yapın veya platformumuzu keşfetmek için yeni bir hesap oluşturun.</p>
            <div class="action-links">
                <a href="giris.php"><i class="fas fa-sign-in-alt"></i> Giriş Yap</a>
                <a href="kayit.php"><i class="fas fa-user-plus"></i> Kayıt Ol</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>