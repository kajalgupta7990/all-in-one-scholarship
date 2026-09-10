<?php include 'includes/header.php'; ?>

<!-- Header -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-5 fw-bold mb-2">Eligibility Criteria Checker</h1>
        <p class="lead mb-0 text-white-50">Determine eligible scholarship opportunities instantly before registering</p>
    </div>
</section>

<!-- Content Form & Results -->
<section class="container mt-5 mb-5">
    <div class="row g-4">
        <!-- Form Column -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4 bg-white">
                <h4 class="fw-bold mb-3 text-primary"><i class="fas fa-search-plus me-2"></i>Enter Profile Details</h4>
                <p class="small text-muted mb-4">Please input correct financial and educational details. All fields are processed client-side to verify matching guidelines.</p>
                
                <form id="eligibilityForm">
                    <div class="mb-3">
                        <label for="studentName" class="form-label small fw-semibold">Full Name</label>
                        <input type="text" class="form-control" id="studentName" placeholder="Enter your full name" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="studentCategory" class="form-label small fw-semibold">Reservation Category</label>
                            <select class="form-select" id="studentCategory" required>
                                <option value="" disabled selected>Select Category</option>
                                <option value="General">General / Open</option>
                                <option value="OBC">OBC (Other Backward Class)</option>
                                <option value="SC">SC (Scheduled Caste)</option>
                                <option value="ST">ST (Scheduled Tribe)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="studentGender" class="form-label small fw-semibold">Gender</label>
                            <select class="form-select" id="studentGender" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="annualIncome" class="form-label small fw-semibold">Annual Household Family Income ($)</label>
                        <input type="number" class="form-control" id="annualIncome" placeholder="e.g. 3500" min="0" required>
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Total income of parents/guardians from all earning sources combined.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="studentCourse" class="form-label small fw-semibold">Current Course / Degree</label>
                            <select class="form-select" id="studentCourse" required>
                                <option value="" disabled selected>Select Course</option>
                                <option value="School">Secondary School (Class 9-10)</option>
                                <option value="HSC">Higher Secondary (Class 11-12)</option>
                                <option value="UG">Undergrad Degree (BA, B.Sc, B.Tech)</option>
                                <option value="PG">Postgrad Degree (MA, M.Sc, M.Tech)</option>
                                <option value="PhD">Doctoral Programs (Ph.D / Fellowship)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="percentage" class="form-label small fw-semibold">Previous Class Percentage (%)</label>
                            <input type="number" class="form-control" id="percentage" placeholder="e.g. 78" min="0" max="100" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gov-primary w-100 py-2.5 fw-bold mt-2"><i class="fas fa-calculator me-1"></i> Check Scheme Eligibility</button>
                </form>
            </div>
        </div>

        <!-- Result Column -->
        <div class="col-lg-6">
            <div id="eligibilityResult" class="h-100">
                <!-- Fallback card stating instructions -->
                <div class="card border-0 shadow-sm p-5 bg-white text-center h-100 d-flex flex-column justify-content-center align-items-center border-start border-success border-4">
                    <i class="fas fa-calculator text-muted" style="font-size: 4rem;"></i>
                    <h5 class="fw-bold mt-3">Awaiting Assessment</h5>
                    <p class="small text-muted px-lg-4">Fill out the profile criteria form on the left and click submit. The portal system will instantly list matched central, state, and UGC programs.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
