<?php
require_once 'config/config.php';
require_once 'helpers/session_helper.php';

// Log the logout activity if user is logged in
if(isLoggedIn()) {
    logActivity($_SESSION['user_id'], 'logout', 'User logged out');
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
redirect('login.php');
?>