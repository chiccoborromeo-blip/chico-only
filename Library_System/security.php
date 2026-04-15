<?php
// Always start the session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * SECURITY CHECK
 * We check if 'admin_user' is set in the session. 
 * This variable is only created inside login.php after a successful login.
 */
if (!isset($_SESSION['admin_user'])) {
    
    // If not logged in, send them back to the login page
    // We add a 'status' message to show them why they were redirected
    $_SESSION['status'] = "Please Login to Access Dashboard";
    header("Location: login.php");
    exit(0);
}

// Optional: Security Hardening (Prevents Session Hijacking)
// Checks if the user's IP has changed mid-session
if (isset($_SESSION['user_ip']) && $_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR']) {
    session_destroy();
    header("Location: login.php");
    exit(0);
}
?>
