<?php
session_start();
require 'includes/db_connect.php';

// ✅ FIX TIMEZONE (important for expiry)
date_default_timezone_set('Asia/Manila');

$message = "";
$error = "";

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);

    // ✅ Check if email exists FIRST
    $check = "SELECT id FROM users WHERE email=?";
    $stmt = mysqli_prepare($conn, $check);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {

        // ✅ Generate token + expiry
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // ✅ Save token
        $update = "UPDATE users SET reset_token=?, reset_expiry=? WHERE email=?";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, "sss", $token, $expiry, $email);
        mysqli_stmt_execute($stmt);

        // ✅ Create reset link
        $reset_link = "http://localhost/library_system/reset_password.php?token=" . $token;

        // 🔥 FOR TESTING ONLY (remove later when using email)
        $message = "Reset link generated:<br>
                    <a href='$reset_link'>$reset_link</a>";

    } else {
        $error = "Email not found in our system.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>

<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

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
                <h3 class="text-center mb-2">🔑 Forgot Password</h3>
                <p class="text-center text-muted mb-4">
                    Enter your email to receive a reset link
                </p>

                <!-- SUCCESS -->
                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <!-- ERROR -->
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control"
                               placeholder="Enter your email" required>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary w-100">
                        Send Reset Link
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="login.php">← Back to Login</a>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
