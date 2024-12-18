<?php
// Start the session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Remove the user_email cookie if it exists
if (isset($_COOKIE['user_email'])) {
    setcookie('user_email', '', time() - 3600, "/"); // Expire the cookie
}

// Redirect to login page or homepage
header("Location: login.php");
exit();
?>
