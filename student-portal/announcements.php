<?php
require_once __DIR__ . '/includes/auth.php';

$announcements = dbFetchAll(
    "SELECT * FROM announcements WHERE is_active = 1 ORDER BY published_date DESC"
);

include 'includes/header.php';
?>

<!-- Header -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-5 fw-bold mb-2">Live Announcements & Notices</h1>
        <p class="lead mb-0 text-white-50">Stay updated with official scholarship updates, guidelines and disbursement notices</p>
    </div>
</section>

<!-- Content -->
<section class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="timeline">
                <?php if (!empty($announcements)): ?>
                    <?php 
                    $markerColors = ['bg-danger', 'bg-success', 'bg-warning', 'bg-primary'];
                    $idx = 0;
                    foreach ($announcements as $ann): 
                        $markerColor = $markerColors[$idx % count($markerColors)];
                        $idx++;
                    ?>
                        <div class="timeline-item">
                            <div class="timeline-marker <?php echo $markerColor; ?>"></div>
                            <div class="card border-0 shadow-sm p-4 bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-primary text-uppercase"><?php echo htmlspecialchars($ann['target_audience']); ?></span>
                                    <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> <?php echo date('F d, Y', strtotime($ann['published_date'])); ?></small>
                                </div>
                                <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($ann['title']); ?></h5>
                                <p class="small text-muted mb-0"><?php echo nl2br(htmlspecialchars($ann['content'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card border-0 shadow-sm p-5 text-center bg-white">
                        <i class="fas fa-bullhorn fs-1 text-muted mb-3"></i>
                        <h4 class="text-muted fw-bold">No Active Announcements</h4>
                        <p class="text-muted mb-0">There are no circulars or notices published at this moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
