<?php
// Start the session
session_start();

// Check if the user is NOT logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect them back to the login page
    header("Location: login.php");
    exit(); 
}
?>
