<?php
/**
 * API: Student Feedback Endpoint
 * POST /api/feedback.php
 */
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, "Method not allowed. Use POST.", null, 405);
}

$input = getApiInput();
$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$appId = trim($input['application_id'] ?? '');
$category = trim($input['category'] ?? 'Mobile Experience');
$rating = intval($input['rating'] ?? 5);
$comments = trim($input['comments'] ?? $input['message'] ?? '');
$studentId = !empty($input['student_id']) ? intval($input['student_id']) : null;

if (empty($name) || empty($comments)) {
    sendJsonResponse(false, "Name and comments are required.");
}

try {
    dbQuery(
        "INSERT INTO feedback (student_id, name, email, application_id, category, rating, comments, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
        [$studentId, $name, !empty($email) ? $email : 'feedback.user@gov.in', !empty($appId) ? $appId : null, $category, $rating, $comments]
    );

    sendJsonResponse(true, "Thank you for your feedback!", [
        "feedback_id" => (int)dbLastId()
    ]);
} catch (Exception $e) {
    error_log("API Feedback Error: " . $e->getMessage());
    sendJsonResponse(false, "Failed to submit feedback.");
}
