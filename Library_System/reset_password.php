<?php
require 'includes/db_connect.php';

$token = $_GET['token'] ?? '';
$message = "";
$error = "";

// 🚫 Block access if no token
if (!$token) {
    die("Invalid access.");
}

// ✅ STEP 1: Check if token is valid BEFORE form
$sql = "SELECT * FROM users 
        WHERE reset_token=? AND reset_expiry > NOW()";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $token);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    die("Token is invalid or expired.");
}

// ✅ STEP 2: Handle password reset
if (isset($_POST['reset'])) {
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $update = "UPDATE users 
                   SET password=?, reset_token=NULL, reset_expiry=NULL 
                   WHERE id=?";

        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, "si", $hashed, $user['id']);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $message = "Password successfully updated!";
        } else {
            $error = "Something went wrong. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.card {
    border-radius: 18px;
}
.btn-primary {
    background: linear-gradient(135deg, #5c6bc0, #3f51b5);
    border: none;
}
</style>
</head>

<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card shadow-lg p-4">
                <h3 class="text-center mb-2">🔒 Reset Password</h3>
                <p class="text-center text-muted mb-4">
                    Enter your new password
                </p>

                <!-- SUCCESS -->
                <?php if ($message): ?>
                    <div class="alert alert-success text-center">
                        <?= $message ?><br>
                        <a href="login.php" class="btn btn-success btn-sm mt-2">Go to Login</a>
                    </div>
                <?php endif; ?>

                <!-- ERROR -->
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <!-- FORM -->
                <?php if (!$message): ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" id="password"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm" id="confirm"
                               class="form-control" required>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" onclick="togglePassword()">
                        <label class="form-check-label">Show Password</label>
                    </div>

                    <button type="submit" name="reset" class="btn btn-primary w-100">
                        Reset Password
                    </button>
                </form>
                <?php endif; ?>

                <div class="text-center mt-3">
                    <a href="login.php">← Back to Login</a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function togglePassword() {
    const p = document.getElementById('password');
    const c = document.getElementById('confirm');

    p.type = p.type === 'password' ? 'text' : 'password';
    c.type = c.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>
