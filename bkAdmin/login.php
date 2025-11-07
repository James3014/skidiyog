<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit();
}

// Handle login form submission
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Admin credentials (must be provided via environment variables)
    $envReader = function($key) {
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return $value;
        }
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return $_ENV[$key];
        }
        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return $_SERVER[$key];
        }
        return null;
    };

    $valid_username = $envReader('ADMIN_USERNAME');
    $valid_password_hash = $envReader('ADMIN_PASSWORD_HASH');

    if (!$valid_username || !$valid_password_hash) {
        $error_message = '尚未設定 ADMIN_USERNAME / ADMIN_PASSWORD_HASH，請於部署環境變數設定後再登入。';
        $error_message .= '<br>DEBUG: USERNAME=' . ($valid_username ?: 'NOT SET') . ', HASH=' . ($valid_password_hash ? 'SET' : 'NOT SET');
    } elseif ($username === $valid_username && password_verify($password, $valid_password_hash)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['login_time'] = time();

        // Redirect to original requested page or index
        $redirect = $_GET['redirect'] ?? 'index.php';
        header('Location: ' . $redirect);
        exit();
    } else {
        $error_message = '帳號或密碼錯誤';
        // Debug info
        $debug = '<br>DEBUG: ';
        $debug .= 'ENV_USER=' . ($valid_username ?: 'NOT SET') . ', ';
        $debug .= 'ENV_HASH=' . ($valid_password_hash ? substr($valid_password_hash, 0, 20) . '...' : 'NOT SET') . ', ';
        $debug .= 'INPUT_USER=' . $username . ', ';
        $debug .= 'USER_MATCH=' . ($username === $valid_username ? 'YES' : 'NO') . ', ';
        if ($valid_password_hash) {
            $debug .= 'PWD_VERIFY=' . (password_verify($password, $valid_password_hash) ? 'YES' : 'NO');
        }
        $error_message .= $debug;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>SKIDIY 後台登入</title>

    <!--Import materialize.css-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .login-title {
            color: #667eea;
            margin-bottom: 30px;
            text-align: center;
        }
        .login-icon {
            font-size: 64px;
            color: #667eea;
            text-align: center;
            margin-bottom: 20px;
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col s12 m6 offset-m3">
                <div class="card login-card">
                    <div class="login-icon">
                        <i class="material-icons">lock_outline</i>
                    </div>
                    <h4 class="login-title">SKIDIY 管理員後台</h4>

                    <?php if ($error_message): ?>
                    <div class="error-message">
                        <i class="material-icons tiny">error_outline</i>
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="input-field">
                            <i class="material-icons prefix">account_circle</i>
                            <input id="username" name="username" type="text" required autofocus>
                            <label for="username">帳號</label>
                        </div>

                        <div class="input-field">
                            <i class="material-icons prefix">vpn_key</i>
                            <input id="password" name="password" type="password" required>
                            <label for="password">密碼</label>
                        </div>

                        <button class="btn waves-effect waves-light btn-large blue darken-2" type="submit" style="width: 100%; margin-top: 20px;">
                            登入
                            <i class="material-icons right">send</i>
                        </button>
                    </form>

                    <div style="margin-top: 30px; text-align: center; color: #999; font-size: 12px;">
                        © 2024 diy.ski - 管理員後台系統
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Import jQuery and materialize.js-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
</body>
</html>
