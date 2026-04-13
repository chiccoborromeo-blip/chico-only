<?php
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/');
session_start();
require 'includes/db_connect.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit();
}


$error_admin = "";
$error_user  = "";

// ADMIN LOGIN
if (isset($_POST['admin_login'])) {
    $email    = trim($_POST['admin_email']);
    $password = trim($_POST['admin_password']);

    $sql  = "SELECT * FROM users WHERE email = ? AND role = 'admin'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role']      = $user['role'];
        $_SESSION['toast']     = [
            'type'    => 'success',
            'message' => 'Welcome back, ' . $user['name'] . '! You are logged in as Admin.'
        ];
        header("Location: dashboard.php");
        exit();
    } else {
        $error_admin = "Invalid admin email or password.";
    }
}

// USER LOGIN
if (isset($_POST['user_login'])) {
    $email    = trim($_POST['user_email']);
    $password = trim($_POST['user_password']);

    $sql  = "SELECT * FROM users WHERE email = ? AND role = 'user'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role']      = $user['role'];
        $_SESSION['toast']     = [
            'type'    => 'success',
            'message' => 'Welcome back, ' . $user['name'] . '! Enjoy your reading. 📚'
        ];
        header("Location: user_dashboard.php");
        exit();
    } else {
        $error_user = "Invalid user email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Library System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f0f2f5;
            padding: 40px 20px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .login-header h1 {
            font-size: 32px;
            color: #1a1a2e;
            margin-bottom: 8px;
        }
        .login-header p {
            color: #888;
            font-size: 15px;
        }
        .login-panels {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            width: 100%;
            max-width: 860px;
        }
        .login-panel {
            background: white;
            padding: 36px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .panel-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .panel-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto 12px;
        }
        .admin-icon { background: #e8eaf6; }
        .user-icon  { background: #e1f5ee; }
        .panel-header h2 { font-size: 20px; margin-bottom: 4px; }
        .admin-title { color: #3f51b5; }
        .user-title  { color: #1D9E75; }
        .panel-header p { color: #888; font-size: 13px; }
        .btn-admin {
            width: 100%;
            padding: 12px;
            background: #3f51b5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-admin:hover { background: #303f9f; }
        .btn-user {
            width: 100%;
            padding: 12px;
            background: #1D9E75;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-user:hover { background: #0F6E56; }
        @media (max-width: 600px) {
            .login-panels { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="login-wrapper">

    <div class="login-header">
        <h1>📚 Library System</h1>
        <p>Select your account type to sign in</p>
    </div>

    <div class="login-panels">

        <!-- ADMIN PANEL -->
        <div class="login-panel">
            <div class="panel-header">
                <div class="panel-icon admin-icon">🔐</div>
                <h2 class="admin-title">Admin Login</h2>
                <p>Access the admin dashboard</p>
            </div>

            <?php if ($error_admin): ?>
                <div class="alert error"><?= htmlspecialchars($error_admin) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Admin Email</label>
                    <input type="email" name="admin_email" required placeholder="Enter admin email">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="admin_password" required placeholder="Enter password">
                </div>
                <button type="submit" name="admin_login" class="btn-admin">
                    Login as Admin
                </button>
            </form>
        </div>

        <!-- USER PANEL -->
        <div class="login-panel">
            <div class="panel-header">
                <div class="panel-icon user-icon">👤</div>
                <h2 class="user-title">Member Login</h2>
                <p>Access your member account</p>
            </div>

            <?php if ($error_user): ?>
                <div class="alert error"><?= htmlspecialchars($error_user) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Member Email</label>
                    <input type="email" name="user_email" required placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="user_password" required placeholder="Enter password">
                </div>
                <button type="submit" name="user_login" class="btn-user">
                    Login as Member
                </button>
            </form>

            <p style="text-align:center; margin-top:16px; font-size:13px; color:#888;">
                Don't have an account?
                <a href="register.php" style="color:#1D9E75; font-weight:500;">Register here</a>
            </p>
        </div>

    </div>
</div>

<?php require 'includes/toast.php'; ?>
</body>
</html>