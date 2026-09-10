<?php
/**
 * API: Student Profile View & Update Endpoint
 * GET / POST /api/profile.php
 */
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $studentId = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
    if ($studentId <= 0) {
        sendJsonResponse(false, "student_id is required.");
    }

    $student = dbFetchOne("SELECT * FROM students WHERE student_id = ?", [$studentId]);
    if (!$student) {
        sendJsonResponse(false, "Student not found.");
    }

    unset($student['password']);
    sendJsonResponse(true, "Profile retrieved", $student);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getApiInput();
    $studentId = intval($input['student_id'] ?? 0);

    if ($studentId <= 0) {
        sendJsonResponse(false, "student_id is required.");
    }

    $fullName = trim($input['full_name'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $category = trim($input['category'] ?? '');
    $annualIncome = floatval($input['annual_income'] ?? 0);
    $institution = trim($input['institution'] ?? '');
    $bankName = trim($input['bank_name'] ?? '');
    $accountNumber = trim($input['account_number'] ?? '');
    $ifscCode = trim($input['ifsc_code'] ?? '');
    $address = trim($input['address'] ?? '');

    if (empty($fullName) || empty($phone)) {
        sendJsonResponse(false, "Name and phone cannot be empty.");
    }

    try {
        dbQuery(
            "UPDATE students 
             SET full_name = ?, phone = ?, category = ?, annual_income = ?, institution = ?,
                 bank_name = ?, account_number = ?, ifsc_code = ?, address = ?, updated_at = NOW()
             WHERE student_id = ?",
            [$fullName, $phone, $category, $annualIncome, $institution, $bankName, $accountNumber, $ifscCode, $address, $studentId]
        );

        $updated = dbFetchOne("SELECT * FROM students WHERE student_id = ?", [$studentId]);
        unset($updated['password']);

        sendJsonResponse(true, "Profile updated successfully", $updated);
    } catch (Exception $e) {
        error_log("API Profile Update Error: " . $e->getMessage());
        sendJsonResponse(false, "Failed to update profile.");
    }
} else {
    sendJsonResponse(false, "Method not allowed.", null, 405);
}
