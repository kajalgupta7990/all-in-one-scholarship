<?php
/**
 * Student Portal Logout
 */
require_once __DIR__ . '/includes/auth.php';

logoutStudent();

// Redirect to login with logout message
session_start();
$_SESSION['auth_success'] = "You have been logged out successfully.";
header("Location: login.php");
exit();
