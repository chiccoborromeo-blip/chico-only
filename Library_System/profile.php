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
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $course  = trim($_POST['course']);
    $year    = trim($_POST['year']);
    $section = trim($_POST['section']);

    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND id != ?");
    mysqli_stmt_bind_param($check, "si", $email, $user_id);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $error = "Email already used by another account!";
    } else {
        $sql  = "UPDATE users SET name=?, email=?, course=?, year=?, section=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $name, $email, $course, $year, $section, $user_id);
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
    <style>
        .info-card {
            margin-bottom: 14px;
            padding: 14px 16px;
            background: #f8f9ff;
            border-radius: 8px;
        }
        .info-card label { font-size: 11px; color: #888; display: block; margin-bottom: 4px; }
        .info-card p     { font-size: 15px; font-weight: 500; color: #333; margin: 0; }
        .badge-course {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            background: #e8eaf6;
            color: #3f51b5;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="user_dashboard.php" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
        <img src="includes/images/Logo12.png" alt="Logo" style="width:100px; height:100px; object-fit:contain;">
        <span style="font-weight:300; font-size:18px; color:white;">Library System</span>
    </a>
    <div>
        <a href="user_dashboard.php">Home</a>
        <a href="brows_books.php">Browse Books</a>
        <a href="borrow_request.php">My Requests</a>
        <a href="my_borrowed.php">My Books</a>
        <a href="profile.php">Profile</a>
        <a href="#" onclick="confirmLogout(); return false;" style="color:#ff6b6b;">Logout</a>
    </div>
</nav>

<div class="container">
    <h2 style="margin-bottom:24px;">My Profile</h2>

    <?php if ($success): ?>
        <div style="background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; padding:10px 14px; border-radius:8px; margin-bottom:16px;">
            ✅ <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">

        <!-- EDIT PROFILE -->
        <div class="table-box">
            <h3 style="margin-bottom:20px;">✏️ Edit Profile</h3>
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
                    <label>Course</label>
                    <select name="course" required style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                        <option value="">Select course</option>
                        <option value="BSTM" <?= ($user['course'] === 'BSTM') ? 'selected' : '' ?>>Bachelor of Science in Tourism Management (BSTM)</option>
                        <option value="BSOA" <?= ($user['course'] === 'BSOA') ? 'selected' : '' ?>>Bachelor of Science in Office Administration (BSOA)</option>
                        <option value="BIRT" <?= ($user['course'] === 'BIRT') ? 'selected' : '' ?>>Bachelor of Industrial & Restaurant Technology (BIRT)</option>
                        <option value="CIT"  <?= ($user['course'] === 'CIT')  ? 'selected' : '' ?>>Bachelor of Industrial Computer Information Technology (CIT)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Year Level</label>
                    <select name="year" required style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                        <option value="">Select year level</option>
                        <option value="1st Year" <?= ($user['year'] === '1st Year') ? 'selected' : '' ?>>1st Year</option>
                        <option value="2nd Year" <?= ($user['year'] === '2nd Year') ? 'selected' : '' ?>>2nd Year</option>
                        <option value="3rd Year" <?= ($user['year'] === '3rd Year') ? 'selected' : '' ?>>3rd Year</option>
                        <option value="4th Year" <?= ($user['year'] === '4th Year') ? 'selected' : '' ?>>4th Year</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Section</label>
                    <input type="text" name="section" placeholder="e.g. A, 1A"
                        value="<?= htmlspecialchars($user['section'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <input type="text" value="<?= ucfirst($user['role']) ?>" disabled
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
            <h3 style="margin-bottom:20px;">👤 Account Info</h3>

            <div class="info-card">
                <label>Full Name</label>
                <p><?= htmlspecialchars($user['name']) ?></p>
            </div>

            <div class="info-card">
                <label>Email</label>
                <p><?= htmlspecialchars($user['email']) ?></p>
            </div>

            <div class="info-card">
                <label>Course</label>
                <p>
                    <?php if (!empty($user['course'])): ?>
                        <span class="badge-course"><?= htmlspecialchars($user['course']) ?></span>
                    <?php else: ?>
                        <span style="color:#bbb;">Not set</span>
                    <?php endif; ?>
                </p>
            </div>

            <div class="info-card">
                <label>Year Level</label>
                <p><?= !empty($user['year']) ? htmlspecialchars($user['year']) : '<span style="color:#bbb;">Not set</span>' ?></p>
            </div>

            <div class="info-card">
                <label>Section</label>
                <p><?= !empty($user['section']) ? htmlspecialchars($user['section']) : '<span style="color:#bbb;">Not set</span>' ?></p>
            </div>

            <div class="info-card">
                <label>Member Since</label>
                <p><?= date('F d, Y', strtotime($user['created_at'])) ?></p>
            </div>

            <a href="change_password.php"
                style="display:block; text-align:center; padding:12px; background:#5c6bc0; color:white; border-radius:8px; text-decoration:none; font-size:15px; margin-top:8px;">
                🔒 Change Password
            </a>
        </div>

    </div>
</div>

<?php require 'includes/toast.php'; ?>
</body>
</html>
