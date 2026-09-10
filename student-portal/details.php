<?php
require_once __DIR__ . '/includes/auth.php';

// Determine scheme id
$id = isset($_GET['id']) ? intval($_GET['id']) : 1;

$scheme = dbFetchOne("SELECT * FROM scholarships WHERE scholarship_id = ?", [$id]);

// Fallback to first available scholarship if id not found
if (!$scheme) {
    $scheme = dbFetchOne("SELECT * FROM scholarships ORDER BY scholarship_id ASC LIMIT 1");
    if (!$scheme) {
        header("Location: scholarships.php");
        exit();
    }
    $id = (int)$scheme['scholarship_id'];
}

$studentId = isLoggedIn() ? (int)$_SESSION['student_id'] : null;
$existingApp = null;
$applySuccess = '';
$applyError = '';

if ($studentId) {
    // Check if already applied
    $existingApp = dbFetchOne(
        "SELECT * FROM applications WHERE student_id = ? AND scholarship_id = ?",
        [$studentId, $id]
    );

    // Handle application submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply') {
        if ($existingApp) {
            $applyError = "You have already submitted an application for this scholarship scheme (Reference: #APP-{$existingApp['application_id']}).";
        } else {
            try {
                dbQuery(
                    "INSERT INTO applications (student_id, scholarship_id, application_date, status, remarks, approved_amount) 
                     VALUES (?, ?, NOW(), 'Pending', 'Submitted online via student portal', 0.00)",
                    [$studentId, $id]
                );
                $newAppId = dbLastId();
                $applySuccess = "Your application has been submitted successfully! Reference ID: #APP-{$newAppId}.";
                // Refresh existing app
                $existingApp = dbFetchOne(
                    "SELECT * FROM applications WHERE student_id = ? AND scholarship_id = ?",
                    [$studentId, $id]
                );
            } catch (Exception $e) {
                error_log("Application Submit Error: " . $e->getMessage());
                $applyError = "Failed to submit application. Please try again later.";
            }
        }
    }
}

// Category badge helper
$badgeColor = 'primary';
switch (strtoupper($scheme['category'])) {
    case 'OBC': $badgeColor = 'info text-dark'; break;
    case 'SC': $badgeColor = 'success'; break;
    case 'ST': $badgeColor = 'secondary'; break;
    case 'MINORITY': $badgeColor = 'warning text-dark'; break;
    default: $badgeColor = 'primary'; break;
}

include 'includes/header.php';
?>

<!-- Header -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <span class="badge bg-<?php echo $badgeColor; ?> mb-2"><?php echo htmlspecialchars($scheme['category']); ?> Sector</span>
        <h1 class="display-5 fw-bold mb-2"><?php echo htmlspecialchars($scheme['name']); ?></h1>
        <p class="lead mb-0 text-white-50">Official Eligibility Guidelines, Grant Details & Submission Portal</p>
    </div>
</section>

<!-- Content -->
<section class="container mt-5 mb-5">
    <?php if (!empty($applySuccess)): ?>
        <div class="alert alert-success alert-dismissible fade show p-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle fs-5 me-2"></i><strong>Application Confirmed!</strong> <?php echo htmlspecialchars($applySuccess); ?>
            <a href="dashboard.php" class="alert-link ms-2">View in Dashboard</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($applyError)): ?>
        <div class="alert alert-warning alert-dismissible fade show p-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle fs-5 me-2"></i><?php echo htmlspecialchars($applyError); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Main details -->
        <div class="col-lg-8">
            <!-- Card 1: Description -->
            <div class="card border-0 shadow-sm p-4 bg-white mb-4">
                <h4 class="fw-bold mb-3 text-primary"><i class="fas fa-info-circle me-2"></i>Scheme Overview</h4>
                <p class="text-muted leading-relaxed"><?php echo nl2br(htmlspecialchars($scheme['description'] ?? '')); ?></p>
            </div>

            <!-- Card 2: Eligibility Criteria -->
            <div class="card border-0 shadow-sm p-4 bg-white mb-4">
                <h4 class="fw-bold mb-3 text-primary"><i class="fas fa-user-check me-2"></i>Eligibility Requirements</h4>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item bg-transparent text-muted px-0 py-2 border-light d-flex align-items-start">
                        <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                        <span><?php echo nl2br(htmlspecialchars($scheme['eligibility'] ?? 'Applicant must be an Indian citizen enrolled in a recognized institution.')); ?></span>
                    </li>
                    <li class="list-group-item bg-transparent text-muted px-0 py-2 border-light d-flex align-items-start">
                        <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                        <span>Enrolled Course: <strong><?php echo htmlspecialchars($scheme['course'] ?? 'Applicable for all streams'); ?></strong></span>
                    </li>
                    <li class="list-group-item bg-transparent text-muted px-0 py-2 border-light d-flex align-items-start">
                        <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                        <span>Category Requirement: <strong><?php echo htmlspecialchars($scheme['category']); ?></strong> (Valid reservation certificate required if applicable)</span>
                    </li>
                </ul>
            </div>

            <!-- Card 3: Required Documents -->
            <div class="card border-0 shadow-sm p-4 bg-white mb-4">
                <h4 class="fw-bold mb-3 text-primary"><i class="fas fa-file-alt me-2"></i>Mandatory Certificates / Uploads</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded d-flex align-items-center gap-3">
                            <i class="fas fa-id-card text-primary fs-3"></i>
                            <span class="small fw-semibold text-muted">Aadhaar Card (Bank Seeded)</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded d-flex align-items-center gap-3">
                            <i class="fas fa-file-invoice-dollar text-success fs-3"></i>
                            <span class="small fw-semibold text-muted">Valid Annual Income Certificate</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded d-flex align-items-center gap-3">
                            <i class="fas fa-file-signature text-secondary fs-3"></i>
                            <span class="small fw-semibold text-muted">Previous Year Marksheet / Transcript</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded d-flex align-items-center gap-3">
                            <i class="fas fa-university text-info fs-3"></i>
                            <span class="small fw-semibold text-muted">Active Bank Passbook Copy</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Benefits -->
            <div class="card border-0 shadow-sm p-4 bg-white mb-4">
                <h4 class="fw-bold mb-3 text-primary"><i class="fas fa-gift me-2"></i>Funding & Financial Benefits</h4>
                <div class="p-3 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3">
                    <h5 class="fw-bold text-success mb-1">Grant Amount: ₹ <?php echo number_format($scheme['benefit_amount']); ?> / Academic Year</h5>
                    <p class="mb-0 text-muted small">Disbursement is routed directly into the student's Aadhaar-linked bank account via Direct Benefit Transfer (DBT).</p>
                </div>
            </div>
        </div>

        <!-- Sidebar (Timeline & Action) -->
        <div class="col-lg-4">
            <!-- Apply UI -->
            <div class="card border-0 shadow-sm p-4 bg-white text-center mb-4 border-top border-primary border-4">
                <i class="fas fa-graduation-cap text-primary fs-1 mb-3"></i>
                
                <?php if (isLoggedIn()): ?>
                    <?php if ($existingApp): ?>
                        <div class="badge bg-success mb-2 px-3 py-2"><i class="fas fa-check-circle me-1"></i> Already Applied</div>
                        <h5 class="fw-bold">Application Status</h5>
                        <p class="small text-muted">You applied on <?php echo date('M d, Y', strtotime($existingApp['application_date'])); ?>.</p>
                        <div class="p-3 bg-light rounded mb-3">
                            <span class="d-block small text-muted">Current Status:</span>
                            <span class="badge <?php echo $existingApp['status'] === 'Approved' ? 'bg-success' : ($existingApp['status'] === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark'); ?> fs-6 mt-1">
                                <?php echo htmlspecialchars($existingApp['status']); ?>
                            </span>
                        </div>
                        <a href="dashboard.php" class="btn btn-gov-primary w-100 py-2"><i class="fas fa-chart-line me-1"></i> Track in Dashboard</a>
                    <?php else: ?>
                        <h5 class="fw-bold">Apply for this Scholarship</h5>
                        <p class="small text-muted">Logged in as <strong><?php echo htmlspecialchars($_SESSION['student_name'] ?? ''); ?></strong>. Click below to submit your digital application.</p>
                        
                        <form method="POST" action="details.php?id=<?php echo $id; ?>">
                            <input type="hidden" name="action" value="apply">
                            <button type="submit" class="btn btn-gov-primary w-100 py-2.5 fw-bold mb-2" onclick="return confirm('Confirm submission of your scholarship application for this scheme?');">
                                <i class="fas fa-paper-plane me-1"></i> Submit Application Now
                            </button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <h5 class="fw-bold">Ready to Apply?</h5>
                    <p class="small text-muted">Log in to your student portal account or register to submit your application for this scheme.</p>
                    <a href="login.php" class="btn btn-gov-primary w-100 py-2 mb-2 font-weight-bold"><i class="fas fa-sign-in-alt me-1"></i> Login to Apply</a>
                    <a href="register.php" class="btn btn-outline-secondary w-100 py-2 small"><i class="fas fa-user-plus me-1"></i> Register New Student</a>
                <?php endif; ?>
            </div>

            <!-- Timeline Card -->
            <div class="card border-0 shadow-sm p-4 bg-white">
                <h5 class="fw-bold mb-4 text-primary"><i class="far fa-calendar-alt me-2"></i>Scheme Timeline</h5>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <h6 class="fw-bold mb-0">Applications Open</h6>
                        <small class="text-muted d-block mb-1"><?php echo date('F d, Y', strtotime($scheme['created_at'])); ?></small>
                        <p class="small text-muted mb-0">Portal starts accepting student submissions.</p>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-danger" style="box-shadow: 0 0 0 2px var(--bs-danger);"></div>
                        <h6 class="fw-bold mb-0 text-danger">Applications Close</h6>
                        <small class="text-danger d-block mb-1"><?php echo date('F d, Y', strtotime($scheme['last_date'])); ?></small>
                        <p class="small text-muted mb-0">Strict deadline for online submissions.</p>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success" style="box-shadow: 0 0 0 2px var(--bs-success);"></div>
                        <h6 class="fw-bold mb-0 text-success">Institute Verification</h6>
                        <small class="text-success d-block mb-1">Ongoing Phase</small>
                        <p class="small text-muted mb-0">Institutional nodal officer review and recommendation.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
