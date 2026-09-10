<?php
require_once __DIR__ . '/includes/auth.php';

$successMessage = '';
$errorMessage = '';

// Pre-fill if logged in
$student = getCurrentStudent();
$name = $student ? $student['full_name'] : '';
$email = $student ? $student['email'] : '';
$appId = '';
$category = '';
$rating = 5;
$comments = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $appId = trim($_POST['application_id'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $rating = intval($_POST['experienceRating'] ?? 5);
    $comments = trim($_POST['comments'] ?? '');
    $studentId = isLoggedIn() ? (int)$_SESSION['student_id'] : null;

    if (empty($name) || empty($email) || empty($category) || empty($comments)) {
        $errorMessage = "Please complete all required feedback fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address.";
    } else {
        try {
            dbQuery(
                "INSERT INTO feedback (student_id, name, email, application_id, category, rating, comments, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
                [$studentId, $name, $email, !empty($appId) ? $appId : null, $category, $rating, $comments]
            );
            $successMessage = "Thank you! Your feedback report has been registered into our database and forwarded to the portal development and nodal review team.";
            $comments = '';
            $appId = '';
        } catch (Exception $e) {
            error_log("Feedback DB Error: " . $e->getMessage());
            $errorMessage = "Failed to record feedback. Please try again.";
        }
    }
}

include 'includes/header.php';
?>

<!-- Header -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-5 fw-bold mb-2">Portal Feedback</h1>
        <p class="lead mb-0 text-white-50">Help us improve the digital scholarship experience for students and nodal officers</p>
    </div>
</section>

<!-- Content -->
<section class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 bg-white">
                <h4 class="fw-bold mb-3 text-primary"><i class="fas fa-comment-alt-edit me-2"></i>Share Your Feedback</h4>
                <p class="small text-muted mb-4">We value your input. Please provide suggestions on your portal registration and application experiences below.</p>
                
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success alert-dismissible fade show p-3 shadow-sm mb-4" role="alert">
                        <i class="fas fa-check-circle fs-5 me-2"></i><?php echo htmlspecialchars($successMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger alert-dismissible fade show p-3 shadow-sm mb-4" role="alert">
                        <i class="fas fa-exclamation-circle fs-5 me-2"></i><?php echo htmlspecialchars($errorMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="feedback.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="feedbackName" class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="feedbackName" name="name" placeholder="Your name" value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="feedbackEmail" class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="feedbackEmail" name="email" placeholder="Your email address" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="feedbackAppId" class="form-label small fw-semibold">Application Reference ID (Optional)</label>
                            <input type="text" class="form-control" id="feedbackAppId" name="application_id" placeholder="e.g. NSP-APP-101" value="<?php echo htmlspecialchars($appId); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="feedbackCat" class="form-label small fw-semibold">Feedback Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="feedbackCat" name="category" required>
                                <option value="" disabled <?php echo empty($category) ? 'selected' : ''; ?>>Select Category</option>
                                <option value="Website UI & Navigation" <?php echo ($category === 'Website UI & Navigation' || $category === 'design') ? 'selected' : ''; ?>>Website UI & Navigation</option>
                                <option value="Eligibility Checker tool" <?php echo ($category === 'Eligibility Checker tool' || $category === 'checker') ? 'selected' : ''; ?>>Eligibility Checker tool</option>
                                <option value="Document checklist rules" <?php echo ($category === 'Document checklist rules' || $category === 'docs') ? 'selected' : ''; ?>>Document checklist rules</option>
                                <option value="Help Desk ticket support" <?php echo ($category === 'Help Desk ticket support' || $category === 'support') ? 'selected' : ''; ?>>Help Desk ticket support</option>
                                <option value="General Improvement" <?php echo ($category === 'General Improvement' || $category === 'general') ? 'selected' : ''; ?>>General Improvement suggestions</option>
                            </select>
                        </div>
                    </div>

                    <!-- Rating Stars -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold d-block">Rate Your Overall Portal Experience</label>
                        <div class="d-flex gap-3 fs-3 text-warning">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <div class="form-check form-check-inline p-0 m-0">
                                    <input class="btn-check" type="radio" name="experienceRating" id="rate<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $rating == $i ? 'checked' : ''; ?>>
                                    <label class="btn btn-outline-warning rounded-circle px-3 py-2 fw-bold" for="rate<?php echo $i; ?>"><?php echo $i; ?> <i class="fas fa-star"></i></label>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="feedbackComments" class="form-label small fw-semibold">Comments / Suggestions <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="feedbackComments" name="comments" rows="5" placeholder="Write your valuable feedback here..." required><?php echo htmlspecialchars($comments); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-gov-primary w-100 py-2.5 fw-bold"><i class="fas fa-check-circle me-1"></i> Submit Feedback Report</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
