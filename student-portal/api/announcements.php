<?php
/**
 * API: Announcements Endpoint
 * GET /api/announcements.php
 */
require_once __DIR__ . '/db.php';

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;

$sql = "SELECT * FROM announcements WHERE is_active = 1 ORDER BY published_date DESC";
if ($limit > 0) {
    $sql .= " LIMIT " . $limit;
}

$rows = dbFetchAll($sql);
$data = [];

foreach ($rows as $r) {
    $data[] = [
        "announcement_id" => (int)$r['announcement_id'],
        "title" => $r['title'],
        "description" => $r['content'],
        "content" => $r['content'],
        "date" => date('M d', strtotime($r['published_date'])),
        "published_date" => $r['published_date'],
        "target_audience" => $r['target_audience']
    ];
}

sendJsonResponse(true, "Announcements retrieved", $data);
