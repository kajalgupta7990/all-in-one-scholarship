<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$student = getCurrentStudent();
if (!$student) {
    logoutStudent();
    header("Location: login.php");
    exit();
}

$studentId = (int)$student['student_id'];
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $annualIncome = floatval($_POST['annual_income'] ?? 0);
    $institution = trim($_POST['institution'] ?? '');
    $bankName = trim($_POST['bank_name'] ?? '');
    $accountNumber = trim($_POST['account_number'] ?? '');
    $ifscCode = trim($_POST['ifsc_code'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($fullName) || empty($phone) || empty($category)) {
        $errorMessage = "Full Name, Mobile Number, and Category cannot be empty.";
    } elseif (!preg_match('/^[0-9]{10}$/', preg_replace('/[^0-9]/', '', $phone))) {
        $errorMessage = "Mobile number must be 10 digits.";
    } else {
        try {
            dbQuery(
                "UPDATE students 
                 SET full_name = ?, phone = ?, category = ?, annual_income = ?, institution = ?, 
                     bank_name = ?, account_number = ?, ifsc_code = ?, address = ?, updated_at = NOW() 
                 WHERE student_id = ?",
                [$fullName, $phone, $category, $annualIncome, $institution, $bankName, $accountNumber, $ifscCode, $address, $studentId]
            );

            // Update session name
            $_SESSION['student_name'] = $fullName;
            $_SESSION['student_category'] = $category;

            $successMessage = "Your student profile details have been successfully updated in the database!";
            // Reload updated student data
            $student = getCurrentStudent();
        } catch (Exception $e) {
            error_log("Profile Update Error: " . $e->getMessage());
            $errorMessage = "An error occurred while saving your profile changes.";
        }
    }
}

// Initials generator
$nameParts = explode(' ', trim($student['full_name']));
$initials = '';
foreach ($nameParts as $part) {
    if (!empty($part)) {
        $initials .= strtoupper($part[0]);
        if (strlen($initials) >= 2) break;
    }
}
if (empty($initials)) $initials = 'ST';

include 'includes/header.php';
?>

<section class="container mt-5 mb-5">
    <div class="row g-4">
        <!-- Sidebar Navigation Menu -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm p-4 bg-white text-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                    <span class="fs-3 fw-bold"><?php echo htmlspecialchars($initials); ?></span>
                </div>
                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($student['full_name']); ?></h5>
                <p class="small text-muted mb-2"><?php echo htmlspecialchars($student['email']); ?></p>
                <p class="small text-muted mb-3">ID: NSP-STU-<?php echo str_pad($student['student_id'], 4, '0', STR_PAD_LEFT); ?></p>
                
                <?php if ($student['verification_status'] === 'Verified'): ?>
                    <div class="badge bg-success mb-4"><i class="fas fa-check-circle me-1"></i> Profile Verified</div>
                <?php elseif ($student['verification_status'] === 'Rejected'): ?>
                    <div class="badge bg-danger mb-4"><i class="fas fa-times-circle me-1"></i> Verification Rejected</div>
                <?php else: ?>
                    <div class="badge bg-warning text-dark mb-4"><i class="fas fa-clock me-1"></i> Verification Pending</div>
                <?php endif; ?>
                
                <div class="d-flex flex-column gap-2 text-start">
                    <a href="dashboard.php" class="btn btn-outline-secondary border-0 text-start small"><i class="fas fa-chart-line me-2"></i> Dashboard Overview</a>
                    <a href="profile.php" class="btn btn-primary text-start small fw-semibold"><i class="fas fa-user-edit me-2"></i> Edit My Profile</a>
                    <a href="scholarships.php" class="btn btn-outline-secondary border-0 text-start small"><i class="fas fa-list me-2"></i> Available Schemes</a>
                    <a href="documents.php" class="btn btn-outline-secondary border-0 text-start small"><i class="fas fa-file-upload me-2"></i> Upload Documents</a>
                    <a href="logout.php" class="btn btn-outline-danger border-0 text-start small mt-3"><i class="fas fa-sign-out-alt me-2"></i> Logout Portal</a>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm p-4 bg-white">
                <h4 class="fw-bold mb-3 text-primary"><i class="fas fa-user-circle me-2"></i>Manage Student Profile</h4>
                <p class="text-muted small mb-4">View and update your registered personal, institutional, and Direct Benefit Transfer (DBT) bank information.</p>
                
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success alert-dismissible fade show small" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($successMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($errorMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="profile.php" method="POST">
                    <!-- Section 1: Academic & Personal -->
                    <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2"><i class="fas fa-graduation-cap me-2"></i>1. Academic & Personal Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profName" class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="profName" name="full_name" class="form-control" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="profEmail" class="form-label small fw-semibold">Registered Email</label>
                            <input type="email" id="profEmail" class="form-control bg-light" value="<?php echo htmlspecialchars($student['email']); ?>" disabled>
                            <div class="form-text text-muted" style="font-size: 0.7rem;">Primary email cannot be altered once registered.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profPhone" class="form-label small fw-semibold">Mobile Number <span class="text-danger">*</span></label>
                            <input type="tel" id="profPhone" name="phone" class="form-control" value="<?php echo htmlspecialchars($student['phone']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="profCourse" class="form-label small fw-semibold">Enrollment Course</label>
                            <input type="text" id="profCourse" class="form-control bg-light" value="<?php echo htmlspecialchars($student['course'] ?? 'Not Specified'); ?>" disabled>
                            <div class="form-text text-muted" style="font-size: 0.7rem;">Course changes require nodal officer re-validation.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profCategory" class="form-label small fw-semibold">Reservation Category <span class="text-danger">*</span></label>
                            <select id="profCategory" name="category" class="form-select" required>
                                <option value="General" <?php echo ($student['category'] === 'General') ? 'selected' : ''; ?>>General</option>
                                <option value="OBC" <?php echo ($student['category'] === 'OBC') ? 'selected' : ''; ?>>OBC (Other Backward Classes)</option>
                                <option value="SC" <?php echo ($student['category'] === 'SC') ? 'selected' : ''; ?>>SC (Scheduled Castes)</option>
                                <option value="ST" <?php echo ($student['category'] === 'ST') ? 'selected' : ''; ?>>ST (Scheduled Tribes)</option>
                                <option value="Minority" <?php echo ($student['category'] === 'Minority') ? 'selected' : ''; ?>>Minority Communities</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="profIncome" class="form-label small fw-semibold">Annual Family Income (₹)</label>
                            <input type="number" step="0.01" id="profIncome" name="annual_income" class="form-control" value="<?php echo htmlspecialchars($student['annual_income'] ?? 0); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profInstitution" class="form-label small fw-semibold">Enrolled Institution / College</label>
                            <input type="text" id="profInstitution" name="institution" class="form-control" placeholder="e.g. Indian Institute of Technology, Delhi" value="<?php echo htmlspecialchars($student['institution'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="profAddress" class="form-label small fw-semibold">Permanent Home Address</label>
                            <input type="text" id="profAddress" name="address" class="form-control" placeholder="Street, City, State, Pincode" value="<?php echo htmlspecialchars($student['address'] ?? ''); ?>">
                        </div>
                    </div>

                    <!-- Section 2: Financial Details -->
                    <h6 class="fw-bold mb-3 mt-4 text-secondary border-bottom pb-2"><i class="fas fa-piggy-bank me-2"></i>2. Bank Account Details (For DBT Direct Disbursement)</h6>
                    <div class="alert alert-warning border-0 p-2.5 mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-warning fs-5"></i>
                        <small class="text-muted" style="font-size: 0.75rem;">Account details must belong to the applicant. Direct Benefit Transfer (DBT) will fail if account name or Aadhaar is mismatched.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profBankName" class="form-label small fw-semibold">Bank Name</label>
                            <input type="text" id="profBankName" name="bank_name" class="form-control" placeholder="e.g. State Bank of India" value="<?php echo htmlspecialchars($student['bank_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="profAccNo" class="form-label small fw-semibold">Bank Account Number</label>
                            <input type="text" id="profAccNo" name="account_number" class="form-control" placeholder="e.g. 30291827364" value="<?php echo htmlspecialchars($student['account_number'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profIfsc" class="form-label small fw-semibold">Bank IFSC Code</label>
                            <input type="text" id="profIfsc" name="ifsc_code" class="form-control" placeholder="e.g. SBIN0001824" value="<?php echo htmlspecialchars($student['ifsc_code'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-semibold">Aadhaar Mapping Status</label>
                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($student['aadhaar_number'] ?? 'Seeded with UIDAI'); ?>" disabled>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gov-primary px-4 py-2 mt-3 fw-bold"><i class="fas fa-save me-1"></i> Save Profile Details</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
