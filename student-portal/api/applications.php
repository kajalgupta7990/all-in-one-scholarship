<?php
/**
 * API: Student Applications Tracker Endpoint
 * GET /api/applications.php?student_id=X
 */
require_once __DIR__ . '/db.php';

$studentId = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;

if ($studentId <= 0) {
    sendJsonResponse(false, "Valid student_id query parameter is required.");
}

$rows = dbFetchAll(
    "SELECT a.application_id, a.student_id, a.scholarship_id, a.application_date, a.status, a.remarks, a.approved_amount,
            s.name AS scholarship_name, s.category AS scholarship_category, s.benefit_amount
     FROM applications a
     JOIN scholarships s ON a.scholarship_id = s.scholarship_id
     WHERE a.student_id = ?
     ORDER BY a.application_date DESC",
    [$studentId]
);

$data = [];
foreach ($rows as $r) {
    $data[] = [
        "application_id" => (int)$r['application_id'],
        "reference_id" => "NSP-APP-" . $r['application_id'],
        "scholarship_id" => (int)$r['scholarship_id'],
        "scholarship_name" => $r['scholarship_name'],
        "category" => $r['scholarship_category'],
        "benefit_amount" => (float)$r['benefit_amount'],
        "approved_amount" => (float)$r['approved_amount'],
        "status" => $r['status'],
        "remarks" => $r['remarks'],
        "applied_date" => date('M d, Y', strtotime($r['application_date']))
    ];
}

sendJsonResponse(true, "Applications retrieved", $data);
