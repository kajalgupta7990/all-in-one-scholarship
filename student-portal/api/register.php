<?php
/**
 * API: Student Registration Endpoint
 * POST /api/register.php
 */
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, "Method not allowed. Use POST.", null, 405);
}

$input = getApiInput();
$name = trim($input['name'] ?? $input['full_name'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$course = trim($input['course'] ?? '');
$category = trim($input['category'] ?? '');
$address = trim($input['address'] ?? '');
$password = $input['password'] ?? '';

if (empty($name) || empty($email) || empty($phone) || empty($password)) {
    sendJsonResponse(false, "Name, email, phone, and password are required fields.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJsonResponse(false, "Please provide a valid email address.");
}

if (strlen($password) < 6) {
    sendJsonResponse(false, "Password must be at least 6 characters long.");
}

// Check if email already exists
$existing = dbFetchOne("SELECT student_id FROM students WHERE email = ?", [$email]);
if ($existing) {
    sendJsonResponse(false, "An account with this email address already exists.");
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

try {
    dbQuery(
        "INSERT INTO students (full_name, email, phone, password, course, category, address, verification_status, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())",
        [$name, $email, $phone, $hashedPassword, $course, $category, $address]
    );
    $newStudentId = dbLastId();

    sendJsonResponse(true, "Profile registered successfully! You can now log in.", [
        "student_id" => (int)$newStudentId,
        "full_name" => $name,
        "email" => $email
    ]);
} catch (Exception $e) {
    error_log("API Register Error: " . $e->getMessage());
    sendJsonResponse(false, "Database error during registration. Please try again.");
}
