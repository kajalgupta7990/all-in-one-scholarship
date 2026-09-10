<?php
/**
 * API: Support / Contact Query Endpoint
 * POST /api/contact.php
 */
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, "Method not allowed. Use POST.", null, 405);
}

$input = getApiInput();
$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$subject = trim($input['subject'] ?? 'Mobile App Support Ticket');
$message = trim($input['message'] ?? $input['query'] ?? '');
$studentId = !empty($input['student_id']) ? intval($input['student_id']) : null;

if (empty($name) || empty($message)) {
    sendJsonResponse(false, "Name and message are required fields.");
}

if (empty($email)) {
    $email = "mobile.user@gov.in";
}

try {
    dbQuery(
        "INSERT INTO contacts (student_id, name, email, phone, subject, message, status, created_at)
         VALUES (?, ?, ?, ?, ?, ?, 'Open', NOW())",
        [$studentId, $name, $email, $phone, $subject, $message]
    );
    $ticketId = dbLastId();

    sendJsonResponse(true, "Support ticket created successfully", [
        "ticket_id" => "NSP-MOB-" . $ticketId,
        "query_id" => (int)$ticketId,
        "status" => "Open"
    ]);
} catch (Exception $e) {
    error_log("API Contact Error: " . $e->getMessage());
    sendJsonResponse(false, "Failed to submit support query.");
}
