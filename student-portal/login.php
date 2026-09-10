<?php
require_once __DIR__ . '/includes/auth.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$errorMessage = $_SESSION['auth_error'] ?? '';
unset($_SESSION['auth_error']);

$successMessage = $_SESSION['auth_success'] ?? '';
unset($_SESSION['auth_success']);

$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errorMessage = "Please enter both your email address and password.";
    } else {
        $student = dbFetchOne("SELECT * FROM students WHERE email = ?", [$email]);

        // Support password_verify and plaintext demo password compatibility
        if ($student && (password_verify($password, $student['password']) || $password === $student['password'])) {
            // If the password was stored as plain text, rehash it to bcrypt now
            if ($password === $student['password']) {
                $newHash = password_hash($password, PASSWORD_BCRYPT);
                dbQuery("UPDATE students SET password = ? WHERE student_id = ?", [$newHash, $student['student_id']]);
            }

            // Regenerate session ID for security against session fixation
            session_regenerate_id(true);

            $_SESSION['student_logged_in'] = true;
            $_SESSION['student_id'] = (int)$student['student_id'];
            $_SESSION['student_name'] = $student['full_name'];
            $_SESSION['student_email'] = $student['email'];
            $_SESSION['student_course'] = $student['course'];
            $_SESSION['student_category'] = $student['category'];

            header("Location: dashboard.php");
            exit();
        } else {
            $errorMessage = "Invalid email address or password. Please try again.";
        }
    }
}

include 'includes/header.php';
?>

<section class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="border-top: 6px solid var(--primary-color);">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-graduation-cap text-primary" style="font-size: 3.5rem;"></i>
                        <h3 class="fw-bold mt-2">Student Portal</h3>
                        <p class="small text-muted">Log in to apply, track status, and upload certificates</p>
                    </div>

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

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label small fw-semibold">Registered Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" id="loginEmail" name="email" class="form-control border-start-0" placeholder="student@university.com" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <label for="loginPassword" class="form-label small fw-semibold">Account Password</label>
                                <a href="contact.php" class="small text-decoration-none">Forgot Password?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" id="loginPassword" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="form-check mb-4 small">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label text-muted" for="rememberMe">Keep me logged in on this device</label>
                        </div>

                        <button type="submit" class="btn btn-gov-primary w-100 py-2.5 fw-bold mb-3"><i class="fas fa-sign-in-alt me-1"></i> Authenticate Credentials</button>
                        
                        <div class="text-center small mt-4 text-muted">
                            New applicant on this portal? <a href="register.php" class="fw-bold text-decoration-none text-primary">Register New Profile</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
