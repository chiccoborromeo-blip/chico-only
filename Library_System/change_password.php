<?php
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/');
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];
$success = "";
$error   = "";

if (isset($_POST['change_password'])) {
    $current = trim($_POST['current_password']);
    $new     = trim($_POST['new_password']);
    $confirm = trim($_POST['confirm_password']);

    // Get current password
    $sql  = "SELECT password FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);

    if (!password_verify($current, $user['password'])) {
        $error = "Current password is incorrect!";
    } elseif (strlen($new) < 6) {
        $error = "New password must be at least 6 characters!";
    } elseif ($new !== $confirm) {
        $error = "New passwords do not match!";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $sql    = "UPDATE users SET password = ? WHERE id = ?";
        $stmt   = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $hashed, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Password changed successfully!";
        } else {
            $error = "Failed to change password.";
        }
    }
}

// Back link based on role
$back_link = $role === 'admin' ? 'dashboard.php' : 'profile.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password — Library System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navbar based on role -->
<?php if ($role === 'admin'): ?>
<nav class="navbar">
    <span style="font-weight:600; font-size:18px;">📚 Library System</span>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="books.php">Books</a>
        <a href="members.php">Members</a>
        <a href="borrow_requests.php">Borrow Requests</a>
        <a href="return_requests.php">Return Requests</a>
        <a href="borrow.php">Borrow</a>
        <a href="return.php">Return</a>
        <a href="logout.php" style="color:#ff6b6b;">Logout</a>
    </div>
</nav>
<?php else: ?>
<nav class="navbar">
    <span style="font-weight:600; font-size:18px;">📚 Library System</span>
    <div>
        <a href="user_dashboard.php">Home</a>
        <a href="brows_books.php">Browse Books</a>
        <a href="borrow_request.php">My Requests</a>
        <a href="my_borrowed.php">My Books</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php" style="color:#ff6b6b;">Logout</a>
    </div>
</nav>
<?php endif; ?>

<div class="container">
    <h2 style="margin-bottom:20px;">Change Password</h2>

    <div style="max-width:480px;">
        <div class="table-box">
            <h3 style="margin-bottom:20px;">Update Your Password</h3>

            <?php if ($success): ?>
                <div style="background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; padding:12px 16px; border-radius:8px; margin-bottom:20px;">
                    <?= $success ?>
                    <br>
                    <a href="<?= $back_link ?>" style="color:#2e7d32; font-weight:500;">Go back</a>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form method="POST">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required
                        placeholder="Enter your current password">
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required
                        placeholder="At least 6 characters">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required
                        placeholder="Re-enter new password">
                </div>
                <div style="display:flex; gap:12px; align-items:center;">
                    <button type="submit" name="change_password" class="btn-primary"
                        style="width:auto; padding:10px 24px;">
                        Change Password
                    </button>
                    <a href="<?= $back_link ?>" style="color:#888;">Cancel</a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>