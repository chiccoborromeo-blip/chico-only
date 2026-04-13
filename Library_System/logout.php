<?php
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/');
session_start();
session_unset();
session_destroy();

// Restart session to pass toast
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/');
session_start();
$_SESSION['toast'] = [
    'type'    => 'info',
    'message' => 'You have been logged out successfully.'
];
session_write_close();

header("Location: login.php");
exit();
?>