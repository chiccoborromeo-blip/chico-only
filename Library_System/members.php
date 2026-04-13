<?php
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/');
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success = "";
$error   = "";

// ADD MEMBER
if (isset($_POST['add_member'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role     = 'user';

    // Check if email already exists
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $error = "Email already exists!";
    } else {
        $sql  = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $password, $role);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Member added successfully!";
        } else {
            $error = "Failed to add member.";
        }
    }
}

// DELETE MEMBER
if (isset($_GET['delete'])) {
    $id   = (int)$_GET['delete'];
    $sql  = "DELETE FROM users WHERE id = ? AND role = 'user'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: members.php?deleted=1");
    exit();
}

// EDIT MEMBER - load data
$edit_member = null;
if (isset($_GET['edit'])) {
    $id   = (int)$_GET['edit'];
    $sql  = "SELECT * FROM users WHERE id = ? AND role = 'user'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $edit_member = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// UPDATE MEMBER
if (isset($_POST['update_member'])) {
    $id    = (int)$_POST['id'];
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (!empty($_POST['password'])) {
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        $sql  = "UPDATE users SET name=?, email=?, password=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $password, $id);
    } else {
        $sql  = "UPDATE users SET name=?, email=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $id);
    }

    if (mysqli_stmt_execute($stmt)) {
        header("Location: members.php?updated=1");
        exit();
    }
}

// GET ALL MEMBERS
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search) {
    $sql   = "SELECT * FROM users WHERE role='user' AND (name LIKE ? OR email LIKE ?) ORDER BY id DESC";
    $stmt  = mysqli_prepare($conn, $sql);
    $like  = "%$search%";
    mysqli_stmt_bind_param($stmt, "ss", $like, $like);
    mysqli_stmt_execute($stmt);
    $members = mysqli_stmt_get_result($stmt);
} else {
    $members = mysqli_query($conn, "SELECT * FROM users WHERE role='user' ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members — Library System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <span style="font-weight:600; font-size:18px;">📚 Library System</span>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="books.php">Books</a>
        <a href="members.php">Members</a>
        <a href="borrow.php">Borrow</a>
        <a href="return.php">Return</a>
        <a href="logout.php" style="color:#ff6b6b;">Logout</a>
    </div>
</nav>

<div class="container">
    <h2 style="margin-bottom:20px;">Manage Members</h2>

    <?php if ($success): ?>
        <div class="alert" style="background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; padding:10px 14px; border-radius:8px; margin-bottom:16px;">
            <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert" style="background:#fdecea; color:#c0392b; border:1px solid #f5c6cb; padding:10px 14px; border-radius:8px; margin-bottom:16px;">
            Member deleted successfully.
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
        <div class="alert" style="background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; padding:10px 14px; border-radius:8px; margin-bottom:16px;">
            Member updated successfully.
        </div>
    <?php endif; ?>

    <!-- ADD / EDIT FORM -->
    <div class="table-box" style="margin-bottom:24px;">
        <h3 style="margin-bottom:16px;"><?= $edit_member ? 'Edit Member' : 'Add New Member' ?></h3>
        <form method="POST">
            <?php if ($edit_member): ?>
                <input type="hidden" name="id" value="<?= $edit_member['id'] ?>">
            <?php endif; ?>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required placeholder="Full name"
                        value="<?= $edit_member ? htmlspecialchars($edit_member['name']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="Email address"
                        value="<?= $edit_member ? htmlspecialchars($edit_member['email']) : '' ?>">
                </div>
                <div class="form-group">
                    <label><?= $edit_member ? 'New Password (leave blank to keep)' : 'Password' ?></label>
                    <input type="password" name="password" <?= $edit_member ? '' : 'required' ?> placeholder="Password">
                </div>
            </div>
            <?php if ($edit_member): ?>
                <button type="submit" name="update_member" class="btn-primary" style="width:auto; padding:10px 24px;">Update Member</button>
                <a href="members.php" style="margin-left:12px; color:#888;">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add_member" class="btn-primary" style="width:auto; padding:10px 24px;">Add Member</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- SEARCH -->
    <div style="margin-bottom:16px;">
        <form method="GET" style="display:flex; gap:10px;">
            <input type="text" name="search" placeholder="Search by name or email..."
                value="<?= htmlspecialchars($search) ?>"
                style="padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; width:300px;">
            <button type="submit" class="btn-primary" style="width:auto; padding:10px 20px;">Search</button>
            <?php if ($search): ?>
                <a href="members.php" style="padding:10px 16px; color:#888; text-decoration:none;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- MEMBERS TABLE -->
    <div class="table-box">
        <h3 style="margin-bottom:16px;">All Members (<?= mysqli_num_rows($members) ?>)</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($members) > 0): ?>
                    <?php while ($member = mysqli_fetch_assoc($members)): ?>
                    <tr>
                        <td><?= $member['id'] ?></td>
                        <td><?= htmlspecialchars($member['name']) ?></td>
                        <td><?= htmlspecialchars($member['email']) ?></td>
                        <td><?= date('M d, Y', strtotime($member['created_at'])) ?></td>
                        <td>
                            <a href="members.php?edit=<?= $member['id'] ?>" class="btn-sm btn-edit">Edit</a>
                            <a href="members.php?delete=<?= $member['id'] ?>"
                               class="btn-sm btn-delete"
                               onclick="return confirm('Delete this member?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; color:#888;">No members found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>