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

// Get counts
$activeSchemesCount = (int)dbFetchOne("SELECT COUNT(*) as count FROM scholarships WHERE status = 'Active'")['count'];
$appliedCount = (int)dbFetchOne("SELECT COUNT(*) as count FROM applications WHERE student_id = ?", [$studentId])['count'];
$pendingCount = (int)dbFetchOne("SELECT COUNT(*) as count FROM applications WHERE student_id = ? AND status = 'Pending'", [$studentId])['count'];
$approvedCount = (int)dbFetchOne("SELECT COUNT(*) as count FROM applications WHERE student_id = ? AND status = 'Approved'", [$studentId])['count'];
$rejectedCount = (int)dbFetchOne("SELECT COUNT(*) as count FROM applications WHERE student_id = ? AND status = 'Rejected'", [$studentId])['count'];
$activeAnnouncementsCount = (int)dbFetchOne("SELECT COUNT(*) as count FROM announcements WHERE is_active = 1")['count'];

// Fetch real applications submitted by this student
$applications = dbFetchAll(
    "SELECT a.application_id, a.scholarship_id, a.application_date, a.status, a.remarks, a.approved_amount,
            s.name AS scholarship_name, s.category AS scholarship_category, s.benefit_amount
     FROM applications a
     JOIN scholarships s ON a.scholarship_id = s.scholarship_id
     WHERE a.student_id = ?
     ORDER BY a.application_date DESC",
    [$studentId]
);

// Fetch recent announcements
$recentAnnouncements = dbFetchAll(
    "SELECT * FROM announcements WHERE is_active = 1 ORDER BY published_date DESC LIMIT 3"
);

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
                    <a href="dashboard.php" class="btn btn-primary text-start small fw-semibold"><i class="fas fa-chart-line me-2"></i> Dashboard Overview</a>
                    <a href="profile.php" class="btn btn-outline-secondary border-0 text-start small"><i class="fas fa-user-edit me-2"></i> Edit My Profile</a>
                    <a href="scholarships.php" class="btn btn-outline-secondary border-0 text-start small"><i class="fas fa-list me-2"></i> Available Schemes</a>
                    <a href="documents.php" class="btn btn-outline-secondary border-0 text-start small"><i class="fas fa-file-upload me-2"></i> Upload Documents</a>
                    <a href="logout.php" class="btn btn-outline-danger border-0 text-start small mt-3"><i class="fas fa-sign-out-alt me-2"></i> Logout Portal</a>
                </div>
            </div>
        </div>

        <!-- Dashboard Widgets -->
        <div class="col-lg-9">
            <!-- Alert Banner -->
            <?php if (!empty($applications)): 
                $latestApp = $applications[0];
            ?>
            <div class="alert <?php echo $latestApp['status'] === 'Approved' ? 'alert-success' : ($latestApp['status'] === 'Rejected' ? 'alert-danger' : 'alert-info'); ?> border-0 shadow-sm p-3 mb-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas <?php echo $latestApp['status'] === 'Approved' ? 'fa-check-circle text-success' : ($latestApp['status'] === 'Rejected' ? 'fa-times-circle text-danger' : 'fa-info-circle text-info'); ?> fs-3"></i>
                    <div class="small">
                        <strong>Application Status Update:</strong> Your application for "<strong><?php echo htmlspecialchars($latestApp['scholarship_name']); ?></strong>" is currently 
                        <span class="badge <?php echo $latestApp['status'] === 'Approved' ? 'bg-success' : ($latestApp['status'] === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark'); ?>"><?php echo htmlspecialchars($latestApp['status']); ?></span>.
                        <?php if (!empty($latestApp['remarks'])): ?>
                            <br><small class="text-muted">Remarks: <?php echo htmlspecialchars($latestApp['remarks']); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php else: ?>
            <div class="alert alert-primary border-0 shadow-sm p-3 mb-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-graduation-cap text-primary fs-3"></i>
                    <div class="small">
                        <strong>Welcome to the National Scholarship Portal, <?php echo htmlspecialchars($student['full_name']); ?>!</strong> You have not applied for any scholarships yet. Explore available programs and submit your applications.
                    </div>
                </div>
                <a href="scholarships.php" class="btn btn-sm btn-primary px-3">Browse Schemes</a>
            </div>
            <?php endif; ?>

            <!-- Stats Widgets -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-3 bg-white border-start border-primary border-4">
                        <small class="text-muted text-uppercase d-block mb-1 font-weight-bold" style="font-size: 0.65rem;">Active Schemes</small>
                        <h4 class="fw-bold m-0 text-primary"><?php echo $activeSchemesCount; ?></h4>
                        <span class="small text-muted">Currently open</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-3 bg-white border-start border-success border-4">
                        <small class="text-muted text-uppercase d-block mb-1 font-weight-bold" style="font-size: 0.65rem;">Applied Schemes</small>
                        <h4 class="fw-bold m-0 text-success"><?php echo $appliedCount; ?></h4>
                        <span class="small text-muted"><?php echo $pendingCount; ?> Pending Review</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-3 bg-white border-start border-warning border-4">
                        <small class="text-muted text-uppercase d-block mb-1 font-weight-bold" style="font-size: 0.65rem;">Approved Grants</small>
                        <h4 class="fw-bold m-0 text-warning"><?php echo $approvedCount; ?></h4>
                        <span class="small text-muted"><?php echo $rejectedCount; ?> Rejected</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-3 bg-white border-start border-info border-4">
                        <small class="text-muted text-uppercase d-block mb-1 font-weight-bold" style="font-size: 0.65rem;">Notices & Alerts</small>
                        <h4 class="fw-bold m-0 text-info"><?php echo $activeAnnouncementsCount; ?></h4>
                        <span class="small text-muted">Active notices</span>
                    </div>
                </div>
            </div>

            <!-- Applied Scheme Summary -->
            <div class="card border-0 shadow-sm p-4 bg-white mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0 text-primary">Applied Scholarship Status</h5>
                    <a href="scholarships.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus me-1"></i> Apply for More Schemes</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table align-middle text-nowrap">
                        <thead class="bg-light">
                            <tr class="small text-muted text-uppercase">
                                <th>App ID</th>
                                <th>Scheme Name</th>
                                <th>Submission Date</th>
                                <th>Benefit Amount</th>
                                <th>Current Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="small text-muted">
                            <?php if (!empty($applications)): ?>
                                <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td class="fw-bold">#APP-<?php echo $app['application_id']; ?></td>
                                        <td class="fw-bold text-dark text-wrap" style="max-width: 260px;"><?php echo htmlspecialchars($app['scholarship_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($app['application_date'])); ?></td>
                                        <td class="fw-bold text-success">₹ <?php echo number_format($app['approved_amount'] > 0 ? $app['approved_amount'] : $app['benefit_amount']); ?></td>
                                        <td>
                                            <?php if ($app['status'] === 'Approved'): ?>
                                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Approved</span>
                                            <?php elseif ($app['status'] === 'Rejected'): ?>
                                                <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Rejected</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending Verification</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="details.php?id=<?php echo $app['scholarship_id']; ?>" class="btn btn-outline-primary btn-sm px-3">View Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-file-alt fs-3 d-block mb-2 text-secondary"></i>
                                        You have not applied to any scholarship schemes yet.
                                        <div class="mt-2">
                                            <a href="scholarships.php" class="btn btn-sm btn-primary">Browse Available Schemes</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activities & Timeline -->
            <div class="card border-0 shadow-sm p-4 bg-white">
                <h5 class="fw-bold mb-4 text-primary">Live Portal Notices & Announcements</h5>
                <div class="timeline">
                    <?php if (!empty($recentAnnouncements)): ?>
                        <?php foreach ($recentAnnouncements as $ann): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($ann['title']); ?></h6>
                                <small class="text-muted d-block mb-1"><i class="far fa-calendar-alt me-1"></i> <?php echo date('F d, Y', strtotime($ann['published_date'])); ?> • Audience: <?php echo htmlspecialchars($ann['target_audience']); ?></small>
                                <p class="small text-muted mb-0"><?php echo htmlspecialchars($ann['content']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <h6 class="fw-bold mb-0">No announcements posted currently.</h6>
                            <p class="small text-muted mb-0">Check back later for state and central scholarship updates.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
