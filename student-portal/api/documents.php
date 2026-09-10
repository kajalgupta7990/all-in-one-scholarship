<?php
/**
 * API: Documents Endpoint
 * GET / POST /api/documents.php
 */
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $studentId = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
    if ($studentId <= 0) {
        sendJsonResponse(false, "student_id is required.");
    }

    $docs = dbFetchAll("SELECT * FROM documents WHERE student_id = ? ORDER BY upload_date DESC", [$studentId]);
    sendJsonResponse(true, "Documents retrieved", $docs);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = intval($_POST['student_id'] ?? 0);
    $docType = trim($_POST['document_type'] ?? '');

    if ($studentId <= 0 || empty($docType) || !isset($_FILES['document_file'])) {
        sendJsonResponse(false, "student_id, document_type, and document_file are required.");
    }

    $file = $_FILES['document_file'];
    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        sendJsonResponse(false, "Allowed file extensions: PDF, JPG, PNG.");
    }

    $uploadDir = __DIR__ . '/../uploads/documents/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $safeName = "doc_{$studentId}_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $safeName)) {
        dbQuery(
            "INSERT INTO documents (student_id, document_type, file_name, file_path, upload_date, verification_status)
             VALUES (?, ?, ?, ?, NOW(), 'Pending')",
            [$studentId, $docType, $file['name'], "uploads/documents/" . $safeName]
        );
        sendJsonResponse(true, "Document uploaded successfully", [
            "document_id" => (int)dbLastId(),
            "document_type" => $docType,
            "status" => "Pending"
        ]);
    } else {
        sendJsonResponse(false, "Failed to save file.");
    }
} else {
    sendJsonResponse(false, "Method not allowed.", null, 405);
}
