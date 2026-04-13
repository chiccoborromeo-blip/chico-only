<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar">
    <span style="font-weight:600; font-size:18px;">📚 Library System</span>
    <div>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="books.php">Books</a>
            <a href="members.php">Members</a>
            <a href="borrow.php">Borrow</a>
            <a href="return.php">Return</a>
        <?php else: ?>
            <a href="user_dashboard.php">Home</a>
            <a href="brows_books.php">Browse Books</a>
            <a href="my_borrowed.php">My Books</a>
            <a href="profile.php">Profile</a>
        <?php endif; ?>
        <a href="logout.php" style="color:#ff6b6b;">Logout</a>
    </div>
</nav>