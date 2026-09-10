<?php
/**
 * API: Student Login Endpoint
 * POST /api/login.php
 */
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, "Method not allowed. Use POST.", null, 405);
}

$input = getApiInput();
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if (empty($email) || empty($password)) {
    sendJsonResponse(false, "Please provide both email and password.");
}

$student = dbFetchOne("SELECT * FROM students WHERE email = ?", [$email]);

if (!$student) {
    sendJsonResponse(false, "Invalid email address or password.");
}

// Check bcrypt hash or plain text demo password
if (password_verify($password, $student['password']) || $password === $student['password']) {
    // If stored password was plaintext, re-hash it automatically
    if ($password === $student['password']) {
        $rehash = password_hash($password, PASSWORD_BCRYPT);
        dbQuery("UPDATE students SET password = ? WHERE student_id = ?", [$rehash, $student['student_id']]);
    }

    // Do not return password hash
    unset($student['password']);

    sendJsonResponse(true, "Login successful", [
        "student_id" => (int)$student['student_id'],
        "full_name" => $student['full_name'],
        "email" => $student['email'],
        "phone" => $student['phone'],
        "course" => $student['course'],
        "category" => $student['category'],
        "verification_status" => $student['verification_status'],
        "aadhaar_number" => $student['aadhaar_number'],
        "bank_name" => $student['bank_name'],
        "account_number" => $student['account_number'],
        "ifsc_code" => $student['ifsc_code'],
        "annual_income" => (float)$student['annual_income'],
        "institution" => $student['institution']
    ]);
} else {
    sendJsonResponse(false, "Invalid email address or password.");
}
