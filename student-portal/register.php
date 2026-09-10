<?php
require_once __DIR__ . '/includes/auth.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$errorMessage = '';
$successMessage = '';

// Form values for preserving input
$name = '';
$email = '';
$phone = '';
$course = '';
$category = '';
$address = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($course) || empty($category) || empty($address) || empty($password)) {
        $errorMessage = "All fields marked with an asterisk are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please provide a valid email address.";
    } elseif (!preg_match('/^[0-9]{10}$/', preg_replace('/[^0-9]/', '', $phone))) {
        $errorMessage = "Mobile number must be a valid 10-digit phone number.";
    } elseif (strlen($password) < 6) {
        $errorMessage = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirmPassword) {
        $errorMessage = "Password and confirm password do not match.";
    } else {
        // Check if email already exists
        $existing = dbFetchOne("SELECT student_id FROM students WHERE email = ?", [$email]);
        if ($existing) {
            $errorMessage = "An account with this email address already exists. Please log in.";
        } else {
            // Hash password securely
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            try {
                dbQuery(
                    "INSERT INTO students (full_name, email, phone, password, course, category, address, verification_status, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())",
                    [$name, $email, $phone, $hashedPassword, $course, $category, $address]
                );

                $_SESSION['auth_success'] = "Registration successful! You can now log in to the portal.";
                header("Location: login.php");
                exit();
            } catch (Exception $e) {
                error_log("Registration Error: " . $e->getMessage());
                $errorMessage = "An error occurred while registering your profile. Please try again.";
            }
        }
    }
}

include 'includes/header.php';
?>

<section class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="border-top: 6px solid var(--secondary-color);">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus text-success" style="font-size: 3.5rem;"></i>
                        <h3 class="fw-bold mt-2">New Student Registration</h3>
                        <p class="small text-muted">Register to search and apply for central/state schemes</p>
                    </div>

                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($errorMessage); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label for="regName" class="form-label small fw-semibold">Full Name (As in Marksheet) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                <input type="text" id="regName" name="name" class="form-control border-start-0" placeholder="e.g. Aarav Sharma" value="<?php echo htmlspecialchars($name); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="regEmail" class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" id="regEmail" name="email" class="form-control border-start-0" placeholder="student@email.com" value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="regPhone" class="form-label small fw-semibold">Mobile Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone-alt text-muted"></i></span>
                                    <input type="tel" id="regPhone" name="phone" class="form-control border-start-0" placeholder="10-digit number" value="<?php echo htmlspecialchars($phone); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="regCourse" class="form-label small fw-semibold">Current Enrolled Course <span class="text-danger">*</span></label>
                                <select class="form-select" id="regCourse" name="course" required>
                                    <option value="" disabled <?php echo empty($course) ? 'selected' : ''; ?>>Select Course</option>
                                    <option value="B.Tech Computer Science" <?php echo ($course === 'B.Tech Computer Science' || $course === 'btech') ? 'selected' : ''; ?>>Bachelor of Technology (B.Tech)</option>
                                    <option value="B.Sc Physics" <?php echo ($course === 'B.Sc Physics' || $course === 'bsc') ? 'selected' : ''; ?>>Bachelor of Science (B.Sc)</option>
                                    <option value="M.B.B.S" <?php echo ($course === 'M.B.B.S' || $course === 'mbbs') ? 'selected' : ''; ?>>Bachelor of Medicine (MBBS)</option>
                                    <option value="M.Tech Computer Science" <?php echo ($course === 'M.Tech Computer Science' || $course === 'mtech') ? 'selected' : ''; ?>>Master of Technology (M.Tech)</option>
                                    <option value="B.A. Political Science" <?php echo ($course === 'B.A. Political Science') ? 'selected' : ''; ?>>Bachelor of Arts (B.A)</option>
                                    <option value="Ph.D Research" <?php echo ($course === 'phd') ? 'selected' : ''; ?>>Doctor of Philosophy (Ph.D)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="regCategory" class="form-label small fw-semibold">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="regCategory" name="category" required>
                                    <option value="" disabled <?php echo empty($category) ? 'selected' : ''; ?>>Select Category</option>
                                    <option value="General" <?php echo (strcasecmp($category, 'General') === 0) ? 'selected' : ''; ?>>General</option>
                                    <option value="OBC" <?php echo (strcasecmp($category, 'OBC') === 0) ? 'selected' : ''; ?>>OBC</option>
                                    <option value="SC" <?php echo (strcasecmp($category, 'SC') === 0) ? 'selected' : ''; ?>>SC</option>
                                    <option value="ST" <?php echo (strcasecmp($category, 'ST') === 0) ? 'selected' : ''; ?>>ST</option>
                                    <option value="Minority" <?php echo (strcasecmp($category, 'Minority') === 0) ? 'selected' : ''; ?>>Minority</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="regAddress" class="form-label small fw-semibold">Permanent Home Address <span class="text-danger">*</span></label>
                            <textarea id="regAddress" name="address" class="form-control" rows="2" placeholder="House No, Street, Landmark, District, State, Pincode" required><?php echo htmlspecialchars($address); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="regPass" class="form-label small fw-semibold">Choose Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="password" id="regPass" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="regConfirm" class="form-label small fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock-open text-muted"></i></span>
                                    <input type="password" id="regConfirm" name="confirm_password" class="form-control border-start-0" placeholder="••••••••" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-4 small">
                            <input class="form-check-input" type="checkbox" id="termsCheck" required checked>
                            <label class="form-check-label text-muted" for="termsCheck">I agree to terms outlined in the <a href="terms.php" class="text-decoration-none">Terms of Agreement</a></label>
                        </div>

                        <button type="submit" class="btn btn-gov-secondary w-100 py-2.5 fw-bold"><i class="fas fa-user-plus me-1"></i> Register Student Account</button>
                        
                        <div class="text-center small mt-4 text-muted">
                            Already registered? <a href="login.php" class="fw-bold text-decoration-none text-primary">Login Here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
