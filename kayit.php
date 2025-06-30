<?php
require_once 'db.php';

$errors = [];
$successMessage = '';

// Form gönderildi mi kontrol et
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basit doğrulama
    if (empty($username)) {
        $errors[] = "Kullanıcı adı boş bırakılamaz.";
    }
    if (empty($email)) {
        $errors[] = "E-posta boş bırakılamaz.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Geçersiz e-posta formatı.";
    }
    if (empty($password)) {
        $errors[] = "Şifre boş bırakılamaz.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Şifre en az 6 karakter olmalıdır.";
    }

    // Kullanıcı adı veya email veritabanında var mı kontrol et
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Bu kullanıcı adı veya e-posta zaten kullanımda.";
        }
        $stmt->close();
    }
    
    // Hata yoksa kullanıcıyı kaydet
    if (empty($errors)) {
        // Şifreyi GÜVENLİ bir şekilde hash'le
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $successMessage = "Kayıt başarılı! Şimdi giriş yapabilirsiniz.";
        } else {
            $errors[] = "Kayıt sırasında bir hata oluştu: " . $stmt->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 300px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        input { width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: red; margin-bottom: 10px; }
        .success { color: green; margin-bottom: 10px; }
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        :root {
            --dark-bg: #0a192f;        
            --light-bg: #112240;      
            --text-primary: #ccd6f6;  
            --text-secondary: #8892b0; 
            --accent: #64ffda;         
            --accent-dark: #000d20;    
            --border-color: #30415d;  
            --error-bg: rgba(255, 82, 82, 0.1);
            --error-border: #ff5252;
            --success-bg: rgba(100, 255, 218, 0.1);
            --success-border: var(--accent);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-primary);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-size: 16px;
        }

        .container {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background-color: var(--light-bg);
            border-radius: 8px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
            border: 1px solid var(--border-color);
        }

        .content-wrapper {
            text-align: center;
        }

        h1, h2 {
            color: var(--accent);
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
        }

        p {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        a {
            color: var(--accent);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        a:hover {
            text-decoration: underline;
        }

        form label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-secondary);
            font-size: 0.9em;
            font-weight: 400;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            background-color: var(--dark-bg);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            color: var(--text-primary);
            font-size: 1em;
            transition: all 0.3s ease;
        }

        form input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 5px rgba(100, 255, 218, 0.3);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--accent);
            color: var(--accent-dark);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        button:hover {
            opacity: 0.85;
        }

        .error, .success {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .error {
            background-color: var(--error-bg);
            border: 1px solid var(--error-border);
            color: var(--error-border);
        }

        .success {
            background-color: var(--success-bg);
            border: 1px solid var(--success-border);
            color: var(--success-border);
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Kayıt Ol</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($successMessage): ?>
            <div class="success">
                <p><?php echo $successMessage; ?></p>
                <p><a href="giris.php">Giriş Yap</a></p>
            </div>
        <?php else: ?>
            <form action="kayit.php" method="post">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" name="username" id="username" required>
                
                <label for="email">E-posta:</label>
                <input type="email" name="email" id="email" required>
                
                <label for="password">Şifre:</label>
                <input type="password" name="password" id="password" minlength="6" required>
                
                <button type="submit">Kayıt Ol</button>
            </form>
        <?php endif; ?>
        <p style="text-align: center; margin-top: 15px;">Zaten bir hesabın var mı? <a href="giris.php">Giriş Yap</a></p>
    </div>
</body>
</html>