<?php
/**
 * API: Scholarship Detail Endpoint
 * GET /api/scholarship_detail.php?id=X
 */
require_once __DIR__ . '/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    sendJsonResponse(false, "Invalid scholarship ID.");
}

$r = dbFetchOne("SELECT * FROM scholarships WHERE scholarship_id = ?", [$id]);

if (!$r) {
    sendJsonResponse(false, "Scholarship not found.");
}

$data = [
    "id" => (string)$r['scholarship_id'],
    "scholarship_id" => (int)$r['scholarship_id'],
    "name" => $r['name'],
    "category" => $r['category'],
    "amount" => "₹ " . number_format($r['benefit_amount']) . " / Yr",
    "benefit_amount" => (float)$r['benefit_amount'],
    "deadline" => date('M d, Y', strtotime($r['last_date'])),
    "last_date" => $r['last_date'],
    "course" => $r['course'],
    "education_level" => $r['education_level'],
    "scholarship_type" => $r['scholarship_type'],
    "description" => $r['description'],
    "eligibility" => $r['eligibility'],
    "status" => $r['status']
];

sendJsonResponse(true, "Scholarship details retrieved", $data);
