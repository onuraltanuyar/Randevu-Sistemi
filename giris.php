<?php
require_once 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Kullanıcı adı ve şifre boş bırakılamaz.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                header("Location: anasayfa.php");
                exit();
            } 
             else {
                $error = "Kullanıcı adı veya şifre hatalı.";
            }
        } else {
            $error = "Kullanıcı adı veya şifre hatalı.";
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
    <title>Giriş Yap</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 300px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        input { width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: red; margin-bottom: 10px; }
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
        <h2>Giriş Yap</h2>
        
        <?php if ($error): ?>
            <div class="error"><p><?php echo $error; ?></p></div>
        <?php endif; ?>

        <form action="giris.php" method="post">
            <label for="username">Kullanıcı Adı:</label>
            <input type="text" name="username" id="username" required>
            
            <label for="password">Şifre:</label>
            <input type="password" name="password" id="password" required>
            
            <button type="submit">Giriş Yap</button>
        </form>
        <p style="text-align: center; margin-top: 15px;">Hesabın yok mu? <a href="kayit.php">Kayıt Ol</a></p>
    </div>
</body>
</html>