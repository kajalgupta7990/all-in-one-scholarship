<?php include 'includes/header.php'; ?>

<!-- Hero Banner -->
<section class="hero-section text-center text-md-start">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 fade-in-up">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill font-weight-bold text-uppercase mb-3"><i class="fas fa-sparkles me-1"></i> Centralized Application System 2026-27</span>
                <h1 class="display-4 text-white font-weight-bold mb-3">Empowering Students Through Education Funding</h1>
                <p class="lead text-white-50 mb-4">A single unified digital platform where students across the nation can search, apply, and receive scholarship disbursements directly into their bank accounts. Open to primary, secondary, and professional degrees.</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-md-start">
                    <a href="register.php" class="btn btn-warning btn-lg text-dark px-4 py-2 fw-bold shadow-sm"><i class="fas fa-user-plus me-1"></i> Register Student Profile</a>
                    <a href="about.php" class="btn btn-outline-light btn-lg px-4 py-2"><i class="fas fa-info-circle me-1"></i> Explore Schemes</a>
                </div>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <i class="fas fa-landmark text-white-50" style="font-size: 15rem; opacity: 0.15; position: absolute; right: 10%; top: 20%;"></i>
                <div class="bg-white p-4 rounded-4 shadow-lg text-dark position-relative overflow-hidden" style="border-top: 6px solid var(--secondary-color);">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-danger">IMPORTANT NOTICE</span>
                        <small class="text-muted">August 2026</small>
                    </div>
                    <h5 class="fw-bold mb-2">Registration Open for Post-Matric Schemes</h5>
                    <p class="small text-muted mb-3">The application window for Post-Matric scholarship schemes is active. Make sure your Aadhaar card is linked to your bank account to qualify for DBT.</p>
                    <a href="announcements.php" class="btn btn-outline-primary btn-sm w-100">Read Official Guidelines</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search Bar UI -->
<div class="container">
    <div class="search-container">
        <form action="scholarships.php" method="GET" class="search-card">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-0" placeholder="Search by scholarship name or keyword...">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-filter"></i></span>
                        <select name="category" class="form-select border-0">
                            <option value="all">All Categories</option>
                            <option value="General">General</option>
                            <option value="OBC">OBC</option>
                            <option value="SC">SC</option>
                            <option value="ST">ST</option>
                            <option value="Minority">Minority</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-gov-primary w-100 py-2"><i class="fas fa-search me-1"></i> Find Schemes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Image Carousel (Gradient Slider) -->
<section class="container mt-5 pt-3">
    <div id="portalCarousel" class="carousel slide rounded-4 shadow overflow-hidden" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#portalCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#portalCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#portalCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <!-- Slide 1 -->
            <div class="carousel-item active" style="height: 380px; background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
                <div class="carousel-caption d-flex flex-column justify-content-center h-100 text-start" style="left: 8%; right: 8%;">
                    <div class="col-lg-6">
                        <span class="badge bg-success mb-2">Prime Minister's Initiative</span>
                        <h2 class="display-6 font-weight-bold text-white mb-2">PMSS Support Scheme</h2>
                        <p class="text-white-50 small mb-4">Dedicated scholarships for dependents and wards of Ex-servicemen, Ex-Coast Guard personnel, and deceased police personnel during operational combat duty.</p>
                        <a href="scholarships.php" class="btn btn-warning text-dark font-weight-bold">View PMSS Details</a>
                    </div>
                </div>
            </div>
            <!-- Slide 2 -->
            <div class="carousel-item" style="height: 380px; background: linear-gradient(135deg, #134e5e, #71b280);">
                <div class="carousel-caption d-flex flex-column justify-content-center h-100 text-start" style="left: 8%; right: 8%;">
                    <div class="col-lg-6">
                        <span class="badge bg-primary mb-2">Technical Fellowships</span>
                        <h2 class="display-6 font-weight-bold text-white mb-2">Pragati Scholarship for Girls</h2>
                        <p class="text-white-50 small mb-4">Designed by AICTE to encourage girls to pursue professional degree/diploma education. 50,000 scholarship spots annually.</p>
                        <a href="scholarships.php" class="btn btn-light">Read Guidelines</a>
                    </div>
                </div>
            </div>
            <!-- Slide 3 -->
            <div class="carousel-item" style="height: 380px; background: linear-gradient(135deg, #2c3e50, #000000);">
                <div class="carousel-caption d-flex flex-column justify-content-center h-100 text-start" style="left: 8%; right: 8%;">
                    <div class="col-lg-6">
                        <span class="badge bg-warning text-dark mb-2">Minority Empowerment</span>
                        <h2 class="display-6 font-weight-bold text-white mb-2">Maulana Azad National Fellowship</h2>
                        <p class="text-white-50 small mb-4">Offering standard fellowship stipends for doctoral research projects in Humanities, Social Sciences, Sciences, and Engineering courses.</p>
                        <a href="scholarships.php" class="btn btn-warning text-dark font-weight-bold">Explore Now</a>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#portalCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#portalCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>

<?php
// Query live announcements for ticker
$tickerAnnouncements = [];
$featuredScholarships = [];
if (isset($pdo)) {
    try {
        $stmtTicker = $pdo->query("SELECT title, content FROM announcements WHERE is_active = 1 ORDER BY date_posted DESC LIMIT 5");
        $tickerAnnouncements = $stmtTicker->fetchAll();

        $stmtFeat = $pdo->query("SELECT * FROM scholarships WHERE is_active = 1 ORDER BY scholarship_id ASC LIMIT 3");
        $featuredScholarships = $stmtFeat->fetchAll();
    } catch (Exception $e) { }
}
?>

<!-- Latest Announcements Ticker -->
<section class="container mt-4">
    <div class="card border-0 shadow-sm bg-primary bg-opacity-10 py-2 px-3">
        <div class="d-flex align-items-center">
            <span class="badge bg-danger text-uppercase me-3 p-2 font-weight-bold"><i class="fas fa-bullhorn me-1"></i> Live Updates</span>
            <marquee class="small fw-semibold text-primary" scrollamount="4" onmouseover="this.stop();" onmouseout="this.start();">
                <?php if (!empty($tickerAnnouncements)): ?>
                    <?php foreach ($tickerAnnouncements as $idx => $tItem): ?>
                        📢 <span class="mx-3"><strong><?php echo htmlspecialchars($tItem['title']); ?>:</strong> <?php echo htmlspecialchars($tItem['content']); ?></span> <?php if ($idx < count($tickerAnnouncements) - 1) echo '|'; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    📢 <span class="mx-3">Last date for submission of online applications for Central Sector Schemes is extended up to September 15, 2026.</span> |
                    📢 <span class="mx-3">Direct Benefit Transfer (DBT) funds successfully disbursed to verified accounts. Check dashboard.</span> |
                    📢 <span class="mx-3">Verification guidelines for Nodal Officers have been updated. View reports menu.</span>
                <?php endif; ?>
            </marquee>
        </div>
    </div>
</section>

<!-- Scholarship Categories -->
<section class="container mt-5 pt-3">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Scholarship Classification Categories</h2>
        <p class="text-muted">Identify your criteria and browse categorized government financial support</p>
        <hr class="mx-auto bg-primary border-0" style="width: 60px; height: 3px;">
    </div>
    
    <div class="row g-4 text-center">
        <!-- Cat 1 -->
        <div class="col-md-3 col-sm-6">
            <div class="gov-card p-4">
                <i class="fas fa-book-reader fs-1 text-primary mb-3"></i>
                <h5 class="fw-bold">Pre-Matric</h5>
                <p class="small text-muted mb-0">For students studying in primary classes up to Class 10th.</p>
            </div>
        </div>
        <!-- Cat 2 -->
        <div class="col-md-3 col-sm-6">
            <div class="gov-card p-4">
                <i class="fas fa-user-graduate fs-1 text-success mb-3"></i>
                <h5 class="fw-bold">Post-Matric</h5>
                <p class="small text-muted mb-0">For Class 11th, 12th, diplomas, and undergraduate programs.</p>
            </div>
        </div>
        <!-- Cat 3 -->
        <div class="col-md-3 col-sm-6">
            <div class="gov-card p-4">
                <i class="fas fa-microscope fs-1 text-danger mb-3"></i>
                <h5 class="fw-bold">Higher Research</h5>
                <p class="small text-muted mb-0">For students enrolled in post-graduation, M.Phil, or PhD programs.</p>
            </div>
        </div>
        <!-- Cat 4 -->
        <div class="col-md-3 col-sm-6">
            <div class="gov-card p-4">
                <i class="fas fa-hand-holding-usd fs-1 text-warning mb-3"></i>
                <h5 class="fw-bold">Professional</h5>
                <p class="small text-muted mb-0">For professional technical fields like engineering, medicine, and MBA.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Scholarships -->
<section class="container mt-5 pt-3">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="fw-bold mb-0">Featured Schemes</h2>
            <p class="text-muted mb-0">Currently open scholarship applications for 2026-27 sessions</p>
        </div>
        <a href="scholarships.php" class="btn btn-outline-primary d-none d-sm-inline-block">View All Scholarships <i class="fas fa-arrow-right ms-1"></i></a>
    </div>

    <div class="row g-4">
        <?php if (!empty($featuredScholarships)): ?>
            <?php foreach ($featuredScholarships as $item): 
                $badgeClass = 'bg-primary';
                if ($item['category'] === 'Minority') $badgeClass = 'bg-warning text-dark';
                else if ($item['category'] === 'OBC' || $item['category'] === 'ST') $badgeClass = 'bg-info text-dark';
                else if ($item['category'] === 'SC') $badgeClass = 'bg-success';
                
                $deadlineDate = !empty($item['deadline']) ? date('M d', strtotime($item['deadline'])) : 'Open';
            ?>
                <div class="col-lg-4 col-md-6">
                    <div class="gov-card d-flex flex-column justify-content-between p-4 h-100">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($item['category']); ?></span>
                                <span class="text-danger small fw-bold"><i class="far fa-clock me-1"></i> <?php echo $deadlineDate; ?></span>
                            </div>
                            <h5 class="card-title fw-bold text-dark mb-2"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p class="small text-muted mb-3"><?php echo htmlspecialchars(mb_strimwidth($item['description'] ?? '', 0, 90, '...')); ?></p>
                            <ul class="list-unstyled small mb-4">
                                <li class="mb-2"><i class="fas fa-user-check text-success me-2"></i><strong>Eligibility:</strong> <?php echo htmlspecialchars(mb_strimwidth($item['eligibility'] ?? 'Refer to guidelines', 0, 45, '...')); ?></li>
                                <li class="mb-2"><i class="fas fa-wallet text-success me-2"></i><strong>Benefit:</strong> ₹ <?php echo number_format($item['benefit_amount']); ?> / Year</li>
                            </ul>
                        </div>
                        <a href="details.php?id=<?php echo $item['scholarship_id']; ?>" class="btn btn-gov-primary w-100">View Scheme Guidelines</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Fallback Static Cards if DB is empty -->
            <div class="col-lg-4 col-md-6">
                <div class="gov-card d-flex flex-column justify-content-between p-4">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary">Central Sector</span>
                            <span class="text-danger small fw-bold"><i class="far fa-clock me-1"></i> Sep 15</span>
                        </div>
                        <h5 class="card-title fw-bold text-dark mb-2">Central Sector Scheme of Scholarship</h5>
                        <p class="small text-muted mb-3">Awarded to top board students pursuing college and university professional courses.</p>
                        <ul class="list-unstyled small mb-4">
                            <li class="mb-2"><i class="fas fa-user-check text-success me-2"></i><strong>Eligibility:</strong> Top 20th percentile class 12th</li>
                            <li class="mb-2"><i class="fas fa-wallet text-success me-2"></i><strong>Benefit:</strong> ₹ 12,000 / Year</li>
                        </ul>
                    </div>
                    <a href="scholarships.php" class="btn btn-gov-primary w-100">View Scheme Guidelines</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="gov-card d-flex flex-column justify-content-between p-4">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-success">UGC Scheme</span>
                            <span class="text-danger small fw-bold"><i class="far fa-clock me-1"></i> Sep 30</span>
                        </div>
                        <h5 class="card-title fw-bold text-dark mb-2">PG Scholarship for Single Girl Child</h5>
                        <p class="small text-muted mb-3">Assisting single female child in families who are enrolled in PG degree programs.</p>
                        <ul class="list-unstyled small mb-4">
                            <li class="mb-2"><i class="fas fa-user-check text-success me-2"></i><strong>Eligibility:</strong> Postgrad girls under 30</li>
                            <li class="mb-2"><i class="fas fa-wallet text-success me-2"></i><strong>Benefit:</strong> ₹ 36,000 / Year</li>
                        </ul>
                    </div>
                    <a href="scholarships.php" class="btn btn-gov-primary w-100">View Scheme Guidelines</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="gov-card d-flex flex-column justify-content-between p-4">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-warning text-dark">Minority</span>
                            <span class="text-danger small fw-bold"><i class="far fa-clock me-1"></i> Oct 10</span>
                        </div>
                        <h5 class="card-title fw-bold text-dark mb-2">Merit-cum-Means Scholarship</h5>
                        <p class="small text-muted mb-3">Assisting students of notified minority communities to pursue vocational degrees.</p>
                        <ul class="list-unstyled small mb-4">
                            <li class="mb-2"><i class="fas fa-user-check text-success me-2"></i><strong>Eligibility:</strong> Minorities, >50% grades</li>
                            <li class="mb-2"><i class="fas fa-wallet text-success me-2"></i><strong>Benefit:</strong> ₹ 25,000 / Year</li>
                        </ul>
                    </div>
                    <a href="scholarships.php" class="btn btn-gov-primary w-100">View Scheme Guidelines</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Statistics Cards Section -->
<section class="bg-light py-5 mt-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-lg-3 col-sm-6">
                <div class="stat-card shadow-sm">
                    <i class="fas fa-users text-primary fs-2 mb-3"></i>
                    <h3 class="fw-bold mb-1">1.8 Million</h3>
                    <p class="text-muted small mb-0">Total Registered Students</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="stat-card success shadow-sm">
                    <i class="fas fa-file-invoice-dollar text-success fs-2 mb-3"></i>
                    <h3 class="fw-bold mb-1">$55.4 Million</h3>
                    <p class="text-muted small mb-0">Disbursed via DBT (FY25)</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="stat-card shadow-sm">
                    <i class="fas fa-university text-danger fs-2 mb-3"></i>
                    <h3 class="fw-bold mb-1">42,500+</h3>
                    <p class="text-muted small mb-0">Verified Educational Institutes</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="stat-card success shadow-sm">
                    <i class="fas fa-clipboard-check text-warning fs-2 mb-3"></i>
                    <h3 class="fw-bold mb-1">105 Schemes</h3>
                    <p class="text-muted small mb-0">Active Government Scholarships</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="container mt-5 pt-3">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Process Pipeline - How It Works</h2>
        <p class="text-muted">Follow these simple steps to claim educational scholarship disbursements</p>
        <hr class="mx-auto bg-primary border-0" style="width: 60px; height: 3px;">
    </div>
    
    <div class="row g-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 text-center shadow-sm p-4 h-100">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                    <span class="fs-4 fw-bold">1</span>
                </div>
                <h5 class="fw-bold">Register Profile</h5>
                <p class="small text-muted mb-0">Generate a unique profile token using your email, telephone, and college details.</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 text-center shadow-sm p-4 h-100">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                    <span class="fs-4 fw-bold">2</span>
                </div>
                <h5 class="fw-bold">Upload Documents</h5>
                <p class="small text-muted mb-0">Provide softcopies of Aadhaar, caste certificates, transcripts and bank details.</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 text-center shadow-sm p-4 h-100">
                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                    <span class="fs-4 fw-bold">3</span>
                </div>
                <h5 class="fw-bold">College Verification</h5>
                <p class="small text-muted mb-0">The portal routes your application to your institution's nodal officer for verification.</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 text-center shadow-sm p-4 h-100">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                    <span class="fs-4 fw-bold">4</span>
                </div>
                <h5 class="fw-bold">DBT Disbursement</h5>
                <p class="small text-muted mb-0">Funds are directly credited to the beneficiary's Aadhaar-seeded bank account.</p>
            </div>
        </div>
    </div>
</section>

<!-- Success Stories -->
<section class="bg-light py-5 mt-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Student Success Stories</h2>
            <p class="text-muted">Read feedback from our scholarship beneficiaries who built bright careers</p>
            <hr class="mx-auto bg-primary border-0" style="width: 60px; height: 3px;">
        </div>
        <div class="row g-4">
            <!-- Testimonial 1 -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fs-4 fw-bold" style="width: 50px; height: 50px;">
                            AR
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Amit Roy</h6>
                            <small class="text-muted">M.Tech Scholar, IIT Delhi</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-0">"Thanks to the PG Technical scholarship, I was able to pay my tuition bills on time and purchase essential research equipment. The direct DBT disbursement process is flawless."</p>
                </div>
            </div>
            <!-- Testimonial 2 -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fs-4 fw-bold" style="width: 50px; height: 50px;">
                            SP
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Sneha Patel</h6>
                            <small class="text-muted">MBBS, KEM Hospital</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-0">"Being eligible for the Pragati Scheme allowed me to pursue my clinical medical degree without causing financial stress to my retired parents. The application status checker kept me updated throughout."</p>
                </div>
            </div>
            <!-- Testimonial 3 -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fs-4 fw-bold" style="width: 50px; height: 50px;">
                            JK
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">John K.</h6>
                            <small class="text-muted">PhD Fellow, IISc Bangalore</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-0">"The Research fellowship program has streamlined the application for Ph.D. students. The UI/UX is extremely clean and transparent, showing verification statuses instantly."</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Frequently Asked Questions Section -->
<section class="container mt-5 pt-3">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Frequently Asked Questions</h2>
        <p class="text-muted">Find quick answers to common issues regarding application submissions</p>
        <hr class="mx-auto bg-primary border-0" style="width: 60px; height: 3px;">
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Who is eligible to apply for these scholarships?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-white small text-muted">
                            Eligibility varies per scheme. Most schemes require students to meet specific academic scores (e.g. above 60% in high school), reside in specified states, and fall below family income thresholds. Check the eligibility page for details.
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            What is Aadhaar Seeding and is it mandatory?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-white small text-muted">
                            Yes. Aadhaar seeding refers to linking your Aadhaar card to your bank account. As per government norms, all student scholarships are disbursed using Direct Benefit Transfer (DBT) which relies entirely on seeded bank accounts for transaction security.
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Can I edit my application after submitting it?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-white small text-muted">
                            Once finalized and submitted, applications are locked for verification. However, if the Institute's Nodal Officer marks the application as "Defective," the portal automatically opens it for editing, allowing you to resubmit correcting details.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Contact & Help -->
<section class="container mt-5 mb-5 pt-3">
    <div class="bg-primary bg-opacity-10 rounded-4 p-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <h3 class="fw-bold mb-2">Need Immediate Assistance?</h3>
                <p class="text-muted mb-0">Our dedicated help desk works 24/7 to resolve technical difficulties, verification errors, and payment issues. Call our toll-free number or visit our contact page.</p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <a href="contact.php" class="btn btn-gov-primary btn-lg px-4 me-2"><i class="fas fa-envelope-open-text me-1"></i> Contact Help Desk</a>
                <a href="faq.php" class="btn btn-outline-primary btn-lg px-4">View Help Guide</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
