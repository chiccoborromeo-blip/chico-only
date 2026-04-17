    <?php
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/');
session_start();
require 'includes/db_connect.php';

if (isset($_SESSION['user_id'])) {
    header("Location: user_dashboard.php");
    exit();
}

$success = "";
$error   = "";

if (isset($_POST['register'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        // Check if email exists
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "Email already registered!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $role   = 'user';
            $sql    = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt   = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashed, $role);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Account created successfully! You can now login.";
            } else {
                $error = "Failed to register. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Library System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .register-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f0f2f5;
            padding: 40px 20px;
        }
        .register-container {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 480px;
        }
        .register-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .register-header .icon {
            width: 60px;
            height: 60px;
            background: #e1f5ee;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto 12px;
        }
        .register-header h2 {
            font-size: 22px;
            color: #1a1a2e;
            margin-bottom: 4px;
        }
        .register-header p {
            color: #888;
            font-size: 13px;
        }
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 6px;
            transition: all 0.3s;
        }
        .strength-weak   { background: #e74c3c; width: 33%; }
        .strength-medium { background: #f39c12; width: 66%; }
        .strength-strong { background: #2ecc71; width: 100%; }
    </style>
</head>
<body>
<div class="register-wrapper">
    <div class="register-container">

        <div class="register-header">
            <div class="icon">👤</div>
            <h2>Create Account</h2>
            <p>Register as a library member</p>
        </div>

        <?php if ($success): ?>
            <div style="background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; padding:12px 16px; border-radius:8px; margin-bottom:20px; text-align:center;">
                <?= $success ?>
                <br>
                <a href="login.php" style="color:#2e7d32; font-weight:500;">Click here to Login</a>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required
                    placeholder="Enter your full name"
                    value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required
                    placeholder="Enter your email"
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password" required
                    placeholder="At least 6 characters"
                    oninput="checkStrength(this.value)">
                <div id="strength-bar" class="password-strength" style="display:none;"></div>
                <small id="strength-text" style="font-size:12px; color:#888;"></small>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required
                    placeholder="Re-enter your password">
            </div>
            <button type="submit" name="register" class="btn-primary">
                Create Account
            </button>
        </form>
        <?php endif; ?>

        <p style="text-align:center; margin-top:20px; font-size:13px; color:#888;">
            Already have an account?
            <a href="login.php" style="color:#1D9E75; font-weight:500;">Login here</a>
        </p>
    </div>
</div>

<script>
function checkStrength(password) {
    const bar  = document.getElementById('strength-bar');
    const text = document.getElementById('strength-text');
    bar.style.display = 'block';

    if (password.length === 0) {
        bar.style.display = 'none';
        text.textContent = '';
        return;
    }

    let strength = 0;
    if (password.length >= 6)  strength++;
    if (password.length >= 10) strength++;
    if (/[A-Z]/.test(password) && /[0-9]/.test(password)) strength++;

    bar.className = 'password-strength';
    if (strength === 1) {
        bar.classList.add('strength-weak');
        text.textContent = 'Weak password';
        text.style.color = '#e74c3c';
    } else if (strength === 2) {
        bar.classList.add('strength-medium');
        text.textContent = 'Medium password';
        text.style.color = '#f39c12';
    } else {
        bar.classList.add('strength-strong');
        text.textContent = 'Strong password';
        text.style.color = '#2ecc71';
    }
}
</script>
</body>
</html>
