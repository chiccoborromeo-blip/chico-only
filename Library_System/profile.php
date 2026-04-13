<?php
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/');
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error   = "";

// UPDATE PROFILE
if (isset($_POST['update_profile'])) {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);

    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND id != ?");
    mysqli_stmt_bind_param($check, "si", $email, $user_id);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $error = "Email already used by another account!";
    } else {
        $sql  = "UPDATE users SET name=?, email=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['user_name'] = $name;
            $success = "Profile updated successfully!";
        } else {
            $error = "Failed to update profile.";
        }
    }
}

// GET USER INFO
$sql  = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile — Library System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

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

<div class="container">
    <h2 style="margin-bottom:24px;">My Profile</h2>

    <?php if ($success): ?>
        <div style="background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; padding:10px 14px; border-radius:8px; margin-bottom:16px;">
            <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">

        <!-- EDIT PROFILE -->
        <div class="table-box">
            <h3 style="margin-bottom:20px;">Edit Profile</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required
                        value="<?= htmlspecialchars($user['name']) ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required
                        value="<?= htmlspecialchars($user['email']) ?>">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <input type="text" value="<?= ucfirst($user['role']) ?>" disabled
                        style="background:#f5f5f5; color:#888;">
                </div>
                <div class="form-group">
                    <label>Member Since</label>
                    <input type="text" value="<?= date('F d, Y', strtotime($user['created_at'])) ?>" disabled
                        style="background:#f5f5f5; color:#888;">
                </div>
                <button type="submit" name="update_profile" class="btn-primary"
                    style="width:auto; padding:10px 24px;">
                    Update Profile
                </button>
            </form>
        </div>

        <!-- ACCOUNT INFO -->
        <div class="table-box">
            <h3 style="margin-bottom:20px;">Account Info</h3>

            <div style="margin-bottom:14px; padding:14px; background:#f8f9ff; border-radius:8px;">
                <p style="font-size:12px; color:#888; margin-bottom:4px;">Full Name</p>
                <p style="font-size:15px; font-weight:500; color:#333;"><?= htmlspecialchars($user['name']) ?></p>
            </div>

            <div style="margin-bottom:14px; padding:14px; background:#f8f9ff; border-radius:8px;">
                <p style="font-size:12px; color:#888; margin-bottom:4px;">Email</p>
                <p style="font-size:15px; font-weight:500; color:#333;"><?= htmlspecialchars($user['email']) ?></p>
            </div>

            <div style="margin-bottom:14px; padding:14px; background:#f8f9ff; border-radius:8px;">
                <p style="font-size:12px; color:#888; margin-bottom:4px;">Role</p>
                <p style="font-size:15px; font-weight:500; color:#333;"><?= ucfirst($user['role']) ?></p>
            </div>

            <div style="margin-bottom:20px; padding:14px; background:#f8f9ff; border-radius:8px;">
                <p style="font-size:12px; color:#888; margin-bottom:4px;">Member Since</p>
                <p style="font-size:15px; font-weight:500; color:#333;"><?= date('F d, Y', strtotime($user['created_at'])) ?></p>
            </div>

            <a href="change_password.php"
                style="display:block; text-align:center; padding:12px; background:#5c6bc0; color:white; border-radius:8px; text-decoration:none; font-size:15px;">
                🔒 Change Password
            </a>
        </div>

    </div>
</div>

</body>
</html>