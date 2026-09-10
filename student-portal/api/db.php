<?php
/**
 * REST API Common Database & Response Handler
 * Government Scholarship Portal
 */

// Allow cross-origin requests & standard JSON headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../includes/db.php';

/**
 * Send uniform JSON response and terminate
 */
function sendJsonResponse($success, $message, $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    $response = [
        "success" => (bool)$success,
        "message" => (string)$message
    ];
    if ($data !== null) {
        $response["data"] = $data;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Parse incoming JSON payload or standard POST
 */
function getApiInput() {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true);
    if (is_array($jsonData)) {
        return array_merge($_POST, $jsonData);
    }
    return $_POST;
}
