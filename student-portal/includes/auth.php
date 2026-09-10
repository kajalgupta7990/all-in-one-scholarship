<?php
/**
 * Authentication & Session Management Helper
 * Government Scholarship Portal
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

/**
 * Check if student is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true && !empty($_SESSION['student_id']);
}

/**
 * Enforce login for protected pages
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['auth_error'] = "Please log in to access this page.";
        header("Location: login.php");
        exit();
    }
}

/**
 * Get currently logged-in student's full data from database
 */
function getCurrentStudent() {
    if (!isLoggedIn()) {
        return null;
    }
    $studentId = (int)$_SESSION['student_id'];
    return dbFetchOne("SELECT * FROM students WHERE student_id = ?", [$studentId]);
}

/**
 * Log out student and destroy session
 */
function logoutStudent() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
