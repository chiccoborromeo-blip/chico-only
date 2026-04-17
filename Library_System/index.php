<?php
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/');
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>
