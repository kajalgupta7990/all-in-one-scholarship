<?php
/**
 * API: Submit Scholarship Application Endpoint
 * POST /api/apply.php
 */
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, "Method not allowed. Use POST.", null, 405);
}

$input = getApiInput();
$studentId = intval($input['student_id'] ?? 0);
$scholarshipId = intval($input['scholarship_id'] ?? 0);

if ($studentId <= 0 || $scholarshipId <= 0) {
    sendJsonResponse(false, "Valid student_id and scholarship_id are required.");
}

// Verify student exists
$student = dbFetchOne("SELECT student_id FROM students WHERE student_id = ?", [$studentId]);
if (!$student) {
    sendJsonResponse(false, "Student record not found.");
}

// Verify scholarship exists
$scholarship = dbFetchOne("SELECT * FROM scholarships WHERE scholarship_id = ?", [$scholarshipId]);
if (!$scholarship) {
    sendJsonResponse(false, "Scholarship scheme not found.");
}

// Check for duplicate application
$existing = dbFetchOne(
    "SELECT application_id, status FROM applications WHERE student_id = ? AND scholarship_id = ?",
    [$studentId, $scholarshipId]
);

if ($existing) {
    sendJsonResponse(false, "You have already applied for this scholarship (Application Reference: #APP-{$existing['application_id']}). Current Status: {$existing['status']}.");
}

try {
    dbQuery(
        "INSERT INTO applications (student_id, scholarship_id, application_date, status, remarks, approved_amount)
         VALUES (?, ?, NOW(), 'Pending', 'Submitted via mobile application', 0.00)",
        [$studentId, $scholarshipId]
    );
    $appId = dbLastId();

    sendJsonResponse(true, "Application submitted successfully!", [
        "application_id" => (int)$appId,
        "reference_id" => "NSP-APP-" . $appId,
        "scholarship_name" => $scholarship['name'],
        "status" => "Pending",
        "applied_date" => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    error_log("API Apply Error: " . $e->getMessage());
    sendJsonResponse(false, "Failed to submit application. Please try again.");
}
