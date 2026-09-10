<?php
require_once __DIR__ . '/includes/auth.php';

$uploadSuccess = '';
$uploadError = '';
$studentId = isLoggedIn() ? (int)$_SESSION['student_id'] : null;

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document_file'])) {
    if (!$studentId) {
        $uploadError = "You must be logged in to upload verification documents.";
    } else {
        $documentType = trim($_POST['document_type'] ?? '');
        $file = $_FILES['document_file'];

        if (empty($documentType)) {
            $uploadError = "Please select a valid document category.";
        } elseif ($file['error'] !== UPLOAD_ERR_OK) {
            $uploadError = "File upload failed with error code: " . $file['error'];
        } else {
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $allowedMimes = ['application/pdf', 'image/jpeg', 'image/pjpeg', 'image/png'];
            $maxFileSize = 2 * 1024 * 1024; // 2MB

            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if ($file['size'] > $maxFileSize) {
                $uploadError = "File size exceeds the 2 MB limit.";
            } elseif (!in_array($fileExt, $allowedExtensions) || !in_array($mimeType, $allowedMimes)) {
                $uploadError = "Invalid file type ({$fileExt}). Only clear scanned copies in PDF, JPG, or PNG format are permitted.";
            } else {
                // Ensure upload directory exists
                $uploadDir = __DIR__ . '/uploads/documents/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Generate safe unique filename
                $cleanType = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($documentType));
                $safeFileName = "doc_{$studentId}_{$cleanType}_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $fileExt;
                $targetPath = $uploadDir . $safeFileName;
                $relativePath = "uploads/documents/" . $safeFileName;

                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    try {
                        dbQuery(
                            "INSERT INTO documents (student_id, document_type, file_name, file_path, upload_date, verification_status)
                             VALUES (?, ?, ?, ?, NOW(), 'Pending')",
                            [$studentId, $documentType, $file['name'], $relativePath]
                        );
                        $uploadSuccess = "Successfully uploaded <strong>" . htmlspecialchars($documentType) . "</strong>. Status: Pending Verification.";
                    } catch (Exception $e) {
                        error_log("Document DB Save Error: " . $e->getMessage());
                        $uploadError = "Document uploaded to server, but database recording failed.";
                    }
                } else {
                    $uploadError = "Failed to move uploaded document to storage directory.";
                }
            }
        }
    }
}

// Fetch existing uploaded documents if logged in
$userDocuments = [];
if ($studentId) {
    $userDocuments = dbFetchAll(
        "SELECT * FROM documents WHERE student_id = ? ORDER BY upload_date DESC",
        [$studentId]
    );
}

include 'includes/header.php';
?>

<!-- Header -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-5 fw-bold mb-2">Required Document Checklist</h1>
        <p class="lead mb-0 text-white-50">
            Standardized verification guidelines and digital upload specifications
        </p>
    </div>
</section>

<!-- Content Grid -->
<section class="container mt-5 mb-5">

    <?php if (!empty($uploadSuccess)): ?>
        <div class="alert alert-success alert-dismissible fade show p-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle fs-5 me-2"></i><?php echo $uploadSuccess; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($uploadError)): ?>
        <div class="alert alert-danger alert-dismissible fade show p-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle fs-5 me-2"></i><?php echo htmlspecialchars($uploadError); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!isLoggedIn()): ?>
        <div class="alert alert-warning border-0 shadow-sm p-3 mb-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-exclamation-triangle text-warning fs-3"></i>
                <div class="small">
                    <strong>Authentication Required:</strong> Please <a href="login.php" class="fw-bold alert-link">Log In</a> to upload and link mandatory certificates with your scholarship profile.
                </div>
            </div>
            <a href="login.php" class="btn btn-warning btn-sm fw-bold">Student Login</a>
        </div>
    <?php endif; ?>

    <div class="alert alert-info border-0 shadow-sm p-3 mb-5 d-flex align-items-center gap-3">
        <i class="fas fa-info-circle text-info fs-4"></i>
        <div class="small text-muted mb-0">
            <strong>Important Upload Guidelines:</strong>
            All uploaded files must be clear scanned copies of original certificates.
            Allowed formats are <strong>PDF, JPG, or PNG</strong>.
            Maximum file size is <strong>2 MB</strong> per document.
        </div>
    </div>

    <!-- Upload Grid Cards -->
    <div class="row g-4 mb-5">

        <!-- Aadhaar Card -->
        <div class="col-md-6 col-lg-4">
            <div class="gov-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <i class="fas fa-id-card text-primary fs-2"></i>
                        <span class="badge bg-danger">Mandatory</span>
                    </div>

                    <h5 class="fw-bold text-dark">Aadhaar Card</h5>
                    <p class="small text-muted">
                        Used for identity validation and seeding bank accounts.
                        Ensure your name matches your class marksheet exactly.
                    </p>
                </div>

                <div class="border-top pt-3 mt-3">
                    <span class="small text-muted d-block mb-2">Format: PDF / JPG (Max 2MB)</span>
                    <form action="documents.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="document_type" value="Aadhaar Card">
                        <div class="input-group input-group-sm mb-2">
                            <input type="file" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                        </div>
                        <button type="submit" class="btn btn-gov-primary btn-sm w-100" <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                            <i class="fas fa-upload me-1"></i> Upload Aadhaar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Income Certificate -->
        <div class="col-md-6 col-lg-4">
            <div class="gov-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <i class="fas fa-file-invoice-dollar text-success fs-2"></i>
                        <span class="badge bg-danger">Mandatory</span>
                    </div>

                    <h5 class="fw-bold text-dark">Income Certificate</h5>
                    <p class="small text-muted">
                        Issued by a competent state revenue officer (Tahsildar/SDM).
                        Must be valid for the current financial year.
                    </p>
                </div>

                <div class="border-top pt-3 mt-3">
                    <span class="small text-muted d-block mb-2">Format: PDF / JPG (Max 2MB)</span>
                    <form action="documents.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="document_type" value="Income Certificate">
                        <div class="input-group input-group-sm mb-2">
                            <input type="file" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                        </div>
                        <button type="submit" class="btn btn-gov-primary btn-sm w-100" <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                            <i class="fas fa-upload me-1"></i> Upload Income Cert
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Caste Certificate -->
        <div class="col-md-6 col-lg-4">
            <div class="gov-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <i class="fas fa-users-cog text-warning fs-2"></i>
                        <span class="badge bg-secondary">Optional (SC/ST/OBC)</span>
                    </div>

                    <h5 class="fw-bold text-dark">Caste Certificate</h5>
                    <p class="small text-muted">
                        Required for students claiming SC, ST, or OBC reservation quota funding.
                        Issued by a District Magistrate or Revenue Officer.
                    </p>
                </div>

                <div class="border-top pt-3 mt-3">
                    <span class="small text-muted d-block mb-2">Format: PDF / JPG (Max 2MB)</span>
                    <form action="documents.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="document_type" value="Caste Certificate">
                        <div class="input-group input-group-sm mb-2">
                            <input type="file" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                        </div>
                        <button type="submit" class="btn btn-gov-primary btn-sm w-100" <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                            <i class="fas fa-upload me-1"></i> Upload Caste Cert
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Domicile Certificate -->
        <div class="col-md-6 col-lg-4">
            <div class="gov-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <i class="fas fa-home text-info fs-2"></i>
                        <span class="badge bg-danger">Mandatory for State</span>
                    </div>

                    <h5 class="fw-bold text-dark">Domicile Certificate</h5>
                    <p class="small text-muted">
                        Proof of permanent residency in the state offering state-sponsored scholarship programs.
                    </p>
                </div>

                <div class="border-top pt-3 mt-3">
                    <span class="small text-muted d-block mb-2">Format: PDF / JPG (Max 2MB)</span>
                    <form action="documents.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="document_type" value="Domicile Certificate">
                        <div class="input-group input-group-sm mb-2">
                            <input type="file" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                        </div>
                        <button type="submit" class="btn btn-gov-primary btn-sm w-100" <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                            <i class="fas fa-upload me-1"></i> Upload Domicile
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bank Passbook -->
        <div class="col-md-6 col-lg-4">
            <div class="gov-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <i class="fas fa-university text-primary fs-2"></i>
                        <span class="badge bg-danger">Mandatory</span>
                    </div>

                    <h5 class="fw-bold text-dark">Bank Passbook Copy</h5>
                    <p class="small text-muted">
                        Front page containing account number, beneficiary name and IFSC code for DBT disbursements.
                    </p>
                </div>

                <div class="border-top pt-3 mt-3">
                    <span class="small text-muted d-block mb-2">Format: PDF / JPG (Max 2MB)</span>
                    <form action="documents.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="document_type" value="Bank Passbook Copy">
                        <div class="input-group input-group-sm mb-2">
                            <input type="file" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                        </div>
                        <button type="submit" class="btn btn-gov-primary btn-sm w-100" <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                            <i class="fas fa-upload me-1"></i> Upload Bank Passbook
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Previous Marksheet -->
        <div class="col-md-6 col-lg-4">
            <div class="gov-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <i class="fas fa-file-signature text-secondary fs-2"></i>
                        <span class="badge bg-danger">Mandatory</span>
                    </div>

                    <h5 class="fw-bold text-dark">Previous Year Marksheet</h5>
                    <p class="small text-muted">
                        Official final marksheet of the qualifying class or course (e.g. Class XII for undergraduate entry).
                    </p>
                </div>

                <div class="border-top pt-3 mt-3">
                    <span class="small text-muted d-block mb-2">Format: PDF / JPG (Max 2MB)</span>
                    <form action="documents.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="document_type" value="Previous Year Marksheet">
                        <div class="input-group input-group-sm mb-2">
                            <input type="file" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                        </div>
                        <button type="submit" class="btn btn-gov-primary btn-sm w-100" <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                            <i class="fas fa-upload me-1"></i> Upload Marksheet
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Uploaded Documents Table (Only for logged-in students) -->
    <?php if (isLoggedIn()): ?>
    <div class="card border-0 shadow-sm p-4 bg-white mt-4">
        <h5 class="fw-bold mb-3 text-primary"><i class="fas fa-folder-open me-2"></i>My Uploaded Documents in Database</h5>
        <div class="table-responsive">
            <table class="table align-middle text-nowrap">
                <thead class="bg-light">
                    <tr class="small text-muted text-uppercase">
                        <th>Doc ID</th>
                        <th>Document Type</th>
                        <th>File Name</th>
                        <th>Upload Date</th>
                        <th>Verification Status</th>
                    </tr>
                </thead>
                <tbody class="small text-muted">
                    <?php if (!empty($userDocuments)): ?>
                        <?php foreach ($userDocuments as $doc): ?>
                            <tr>
                                <td class="fw-bold">#DOC-<?php echo $doc['document_id']; ?></td>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($doc['document_type']); ?></td>
                                <td><i class="fas fa-paperclip me-1 text-primary"></i> <?php echo htmlspecialchars($doc['file_name']); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($doc['upload_date'])); ?></td>
                                <td>
                                    <?php if ($doc['verification_status'] === 'Verified'): ?>
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Verified</span>
                                    <?php elseif ($doc['verification_status'] === 'Rejected'): ?>
                                        <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Rejected</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending Verification</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No documents have been uploaded to your profile yet. Use the cards above to submit mandatory certificates.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</section>

<?php include 'includes/footer.php'; ?>