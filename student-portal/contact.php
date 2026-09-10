<?php
require_once __DIR__ . '/includes/auth.php';

$successMessage = '';
$errorMessage = '';

// Pre-fill if logged in
$student = getCurrentStudent();
$name = $student ? $student['full_name'] : '';
$email = $student ? $student['email'] : '';
$phone = $student ? $student['phone'] : '';
$subject = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $studentId = isLoggedIn() ? (int)$_SESSION['student_id'] : null;

    if (empty($name) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
        $errorMessage = "Please fill in all inquiry fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address.";
    } else {
        try {
            dbQuery(
                "INSERT INTO contacts (student_id, name, email, phone, subject, message, status, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, 'Open', NOW())",
                [$studentId, $name, $email, $phone, $subject, $message]
            );
            $ticketId = dbLastId();
            $successMessage = "Your inquiry ticket has been registered successfully! Reference Ticket ID: #NSP-QRY-{$ticketId}. Our administrative nodal desk will respond shortly.";
            // Clear message
            $subject = '';
            $message = '';
        } catch (Exception $e) {
            error_log("Contact DB Error: " . $e->getMessage());
            $errorMessage = "Failed to submit inquiry to server. Please try again.";
        }
    }
}

include 'includes/header.php';
?>

<!-- Header -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-5 fw-bold mb-2">Contact Help Desk</h1>
        <p class="lead mb-0 text-white-50">Reach out to our administrative support desk for resolution of registration and verification issues</p>
    </div>
</section>

<!-- Content -->
<section class="container mt-5 mb-5">
    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success alert-dismissible fade show p-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle fs-5 me-2"></i><strong>Ticket Created:</strong> <?php echo htmlspecialchars($successMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger alert-dismissible fade show p-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle fs-5 me-2"></i><?php echo htmlspecialchars($errorMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Contact Information Column -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm p-4 bg-white h-100 d-flex flex-column justify-content-between">
                <div>
                    <h4 class="fw-bold mb-4 text-primary"><i class="fas fa-headset me-2"></i>Contact Details</h4>
                    
                    <div class="d-flex gap-3 mb-4">
                        <i class="fas fa-map-marker-alt text-danger fs-4 mt-1"></i>
                        <div>
                            <h6 class="fw-bold mb-1">NSP Administration Hub</h6>
                            <p class="small text-muted mb-0">Ministry of Education, Department of Higher Education, Room No. 203-C, Shastri Bhawan, New Delhi - 110001</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <i class="fas fa-envelope text-primary fs-4 mt-1"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Support Email</h6>
                            <p class="small text-muted mb-0">helpdesk-scholarship@gov.in</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <i class="fas fa-phone-alt text-success fs-4 mt-1"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Toll-Free Helpline</h6>
                            <p class="small text-muted mb-0">1800-111-222 (9:00 AM - 6:00 PM, Mon-Sat)</p>
                        </div>
                    </div>
                </div>

                <!-- Map Placeholder -->
                <div class="rounded-3 overflow-hidden border border-light position-relative" style="height: 200px;">
                    <div class="w-100 h-100 bg-secondary bg-opacity-10 d-flex flex-column justify-content-center align-items-center text-center p-3">
                        <i class="fas fa-map-marked-alt text-muted" style="font-size: 3rem;"></i>
                        <h6 class="fw-bold mt-2">Ministry of Education Location Map</h6>
                        <small class="text-muted d-block mb-2">Shastri Bhawan, New Delhi, India</small>
                        <a href="https://maps.google.com" target="_blank" class="btn btn-dark btn-sm px-3 rounded-pill"><i class="fas fa-external-link-alt me-1"></i> Open Google Maps</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Column -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm p-4 bg-white">
                <h4 class="fw-bold mb-3 text-primary"><i class="fas fa-paper-plane me-2"></i>Send Support Query</h4>
                <p class="small text-muted mb-4">Fill out the support form below. A ticket will be generated and stored in our support queue for review by Nodal officers.</p>
                
                <form action="contact.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contactName" class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contactName" name="name" placeholder="Enter your name" value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contactEmail" class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="contactEmail" name="email" placeholder="Enter email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contactPhone" class="form-label small fw-semibold">Mobile Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="contactPhone" name="phone" placeholder="Enter 10-digit number" value="<?php echo htmlspecialchars($phone); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contactSubject" class="form-label small fw-semibold">Subject / Query Topic <span class="text-danger">*</span></label>
                            <select class="form-select" id="contactSubject" name="subject" required>
                                <option value="" disabled <?php echo empty($subject) ? 'selected' : ''; ?>>Select Subject</option>
                                <option value="Registration Errors" <?php echo ($subject === 'Registration Errors') ? 'selected' : ''; ?>>Registration Errors</option>
                                <option value="Verification Pending status" <?php echo ($subject === 'Verification Pending status') ? 'selected' : ''; ?>>Verification Pending status</option>
                                <option value="Direct Benefit Transfer failure" <?php echo ($subject === 'Direct Benefit Transfer failure') ? 'selected' : ''; ?>>Direct Benefit Transfer failure</option>
                                <option value="Document Upload issues" <?php echo ($subject === 'Document Upload issues') ? 'selected' : ''; ?>>Document Upload issues</option>
                                <option value="Other technical difficulties" <?php echo ($subject === 'Other technical difficulties') ? 'selected' : ''; ?>>Other technical difficulties</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contactMessage" class="form-label small fw-semibold">Detailed Query Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="contactMessage" name="message" rows="5" placeholder="Provide complete details (application ID if registered, course name, error description)" required><?php echo htmlspecialchars($message); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-gov-primary w-100 py-2.5 fw-bold"><i class="fas fa-paper-plane me-2"></i> Submit Inquiry Ticket</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
