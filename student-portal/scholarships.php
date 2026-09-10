<?php
require_once __DIR__ . '/includes/auth.php';

// Filter parameters
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? 'all');

$sql = "SELECT * FROM scholarships WHERE status = 'Active'";
$params = [];

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR course LIKE ? OR description LIKE ? OR eligibility LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($category) && $category !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY last_date ASC";

$scholarships = dbFetchAll($sql, $params);

// Category badge helper
function getCategoryBadgeClass($cat) {
    switch (strtoupper($cat)) {
        case 'SC':
            return 'bg-success';
        case 'ST':
            return 'bg-secondary';
        case 'OBC':
            return 'bg-info text-dark';
        case 'MINORITY':
            return 'bg-warning text-dark';
        case 'GENERAL':
        default:
            return 'bg-primary';
    }
}

include 'includes/header.php';
?>

<!-- Header -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-5 fw-bold mb-2">Government Scholarship Catalog</h1>
        <p class="lead mb-0 text-white-50">Browse and filter active Central, State, and Institutional scholarship programs</p>
    </div>
</section>

<!-- Filter Section -->
<section class="container mt-5">
    <div class="card border-0 shadow-sm p-4 bg-white mb-4">
        <form method="GET" action="scholarships.php" class="row g-3">
            <div class="col-md-5">
                <label for="searchScholarship" class="form-label fw-semibold text-primary">Search Schemes</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchScholarship" name="search" class="form-control border-start-0" placeholder="Type scholarship title, course or keywords..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-5">
                <label for="categoryFilter" class="form-label fw-semibold text-primary">Filter Category</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-filter text-muted"></i></span>
                    <select id="categoryFilter" name="category" class="form-select border-start-0" onchange="this.form.submit()">
                        <option value="all" <?php echo ($category === 'all' || empty($category)) ? 'selected' : ''; ?>>All Categories</option>
                        <option value="General" <?php echo ($category === 'General') ? 'selected' : ''; ?>>General Category</option>
                        <option value="OBC" <?php echo ($category === 'OBC') ? 'selected' : ''; ?>>OBC (Other Backward Classes)</option>
                        <option value="SC" <?php echo ($category === 'SC') ? 'selected' : ''; ?>>SC (Scheduled Castes)</option>
                        <option value="ST" <?php echo ($category === 'ST') ? 'selected' : ''; ?>>ST (Scheduled Tribes)</option>
                        <option value="Minority" <?php echo ($category === 'Minority') ? 'selected' : ''; ?>>Minority Programs</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-gov-primary w-100 py-2"><i class="fas fa-filter me-1"></i> Apply Filter</button>
            </div>
        </form>
    </div>
</section>

<!-- Cards List Grid -->
<section class="container mb-5">
    <div class="row g-4" id="scholarshipsGrid">
        <?php if (!empty($scholarships)): ?>
            <?php foreach ($scholarships as $item): 
                $badgeClass = getCategoryBadgeClass($item['category']);
                $formattedDate = date('M d, Y', strtotime($item['last_date']));
            ?>
                <div class="col-lg-4 col-md-6 scholarship-item">
                    <div class="gov-card p-4 d-flex flex-column justify-content-between h-100">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($item['category']); ?></span>
                                <span class="text-danger small fw-bold"><i class="far fa-calendar-alt me-1"></i> <?php echo $formattedDate; ?></span>
                            </div>
                            <h5 class="card-title fw-bold text-dark mb-3"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <ul class="list-unstyled small mb-4">
                                <li class="mb-2"><i class="fas fa-user-check text-success me-2"></i><strong>Eligibility:</strong> <?php echo htmlspecialchars(mb_strimwidth($item['eligibility'] ?? 'Refer to scheme guidelines', 0, 75, '...')); ?></li>
                                <li class="mb-2"><i class="fas fa-wallet text-success me-2"></i><strong>Benefit:</strong> ₹ <?php echo number_format($item['benefit_amount']); ?> / Year</li>
                                <li class="mb-2"><i class="fas fa-university text-success me-2"></i><strong>Course:</strong> <?php echo htmlspecialchars($item['course'] ?? 'All eligible courses'); ?></li>
                            </ul>
                        </div>
                        <a href="details.php?id=<?php echo $item['scholarship_id']; ?>" class="btn btn-gov-primary w-100">View Scheme Guidelines</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fs-1 text-muted mb-3 d-block"></i>
                <h4 class="text-muted fw-bold">No Matching Scholarships Found</h4>
                <p class="text-muted">No active schemes match your filter query. Try selecting "All Categories" or searching with other keywords.</p>
                <a href="scholarships.php" class="btn btn-outline-primary mt-2">Reset Filters</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
