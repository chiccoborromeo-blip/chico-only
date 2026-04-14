<?php
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/');
session_start();

// Save name before destroying session
$name = $_SESSION['user_name'] ?? 'User';

// Destroy session
session_unset();
session_destroy();

// Start new session just for toast
session_start();
$_SESSION['toast'] = [
    'type'    => 'info',
    'message' => 'Goodbye, ' . $name . '! You have been logged out successfully. 👋'
];
session_write_close();

header("Location: login.php");
exit();
?>
