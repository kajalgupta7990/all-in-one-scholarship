<?php include 'includes/header.php'; ?>

<!-- Header -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-5 fw-bold mb-2">Frequently Asked Questions (FAQ)</h1>
        <p class="lead mb-0 text-white-50">Instant answers to application steps, document upload criteria, and portal issues</p>
    </div>
</section>

<!-- Content -->
<section class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <div class="mb-4 text-center">
                <h3 class="fw-bold text-primary">Browse Common Queries</h3>
                <p class="text-muted small">Select a category below to view answers for specific sections</p>
            </div>

            <div class="accordion" id="portalFaqs">
                <!-- FAQ 1 -->
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                    <h2 class="accordion-header" id="faqOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <i class="fas fa-question-circle text-primary me-2"></i> How do I register on the Student Portal?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="faqOne" data-bs-parent="#portalFaqs">
                        <div class="accordion-body bg-white small text-muted">
                            To register, click on the "Student Login" button in the navigation, then select "Register New Student". You will need to provide your full name, email address, working mobile number, category, current course, and generate a password. After submitting, you can log in to complete your detailed dashboard profile.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                    <h2 class="accordion-header" id="faqTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <i class="fas fa-question-circle text-primary me-2"></i> What is a Bonafide Certificate and where do I get it?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="faqTwo" data-bs-parent="#portalFaqs">
                        <div class="accordion-body bg-white small text-muted">
                            A Bonafide Certificate is proof issued by your college certifying that you are currently enrolled as a regular student in their courses. You can download the standardized template from our <strong>Required Documents</strong> page, print it out, have it signed/stamped by your college registrar or principal, and upload it as a scanned PDF.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                    <h2 class="accordion-header" id="faqThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <i class="fas fa-question-circle text-primary me-2"></i> Can I apply for more than one scholarship scheme?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="faqThree" data-bs-parent="#portalFaqs">
                        <div class="accordion-body bg-white small text-muted">
                            While you can check your eligibility for multiple schemes, you can only apply for and receive benefits from <strong>one scheme</strong> per academic year. If a student is found receiving double benefits from different departments, their application is rejected, and disbursements are reclaimed.
                        </div>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                    <h2 class="accordion-header" id="faqFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            <i class="fas fa-question-circle text-primary me-2"></i> Why is my application status showing "Defective"?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="faqFour" data-bs-parent="#portalFaqs">
                        <div class="accordion-body bg-white small text-muted">
                            An application is marked "Defective" by the institute Nodal Officer if they find discrepancies (e.g. blurred certificate uploads, wrong course duration, or incorrect grade entry). You will receive an SMS/Email notification listing the defects. You must log in, correct the issues, and resubmit within the deadline.
                        </div>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                    <h2 class="accordion-header" id="faqFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            <i class="fas fa-question-circle text-primary me-2"></i> My bank account is active. Why did DBT fail?
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="faqFive" data-bs-parent="#portalFaqs">
                        <div class="accordion-body bg-white small text-muted">
                            A DBT failure occurs if your bank account is not mapped/seeded with your Aadhaar number in the NPCI (National Payments Corporation of India) mapper database, even if the account is active for regular transactions. Visit your bank branch and submit an Aadhaar seeding form to resolve this.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
