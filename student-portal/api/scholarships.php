<?php
/**
 * API: Scholarships Listing Endpoint
 * GET /api/scholarships.php
 */
require_once __DIR__ . '/db.php';

$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;

$sql = "SELECT * FROM scholarships WHERE status = 'Active'";
$params = [];

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR course LIKE ? OR description LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if (!empty($category) && $category !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY last_date ASC";

if ($limit > 0) {
    $sql .= " LIMIT " . $limit;
}

$rows = dbFetchAll($sql, $params);
$data = [];

foreach ($rows as $r) {
    $data[] = [
        "id" => (string)$r['scholarship_id'],
        "scholarship_id" => (int)$r['scholarship_id'],
        "name" => $r['name'],
        "category" => $r['category'],
        "amount" => "₹ " . number_format($r['benefit_amount']) . " / Yr",
        "benefit_amount" => (float)$r['benefit_amount'],
        "deadline" => date('M d', strtotime($r['last_date'])),
        "last_date" => $r['last_date'],
        "course" => $r['course'],
        "description" => $r['description'],
        "eligibility" => $r['eligibility'],
        "status" => $r['status']
    ];
}

sendJsonResponse(true, "Scholarships retrieved successfully", $data);
