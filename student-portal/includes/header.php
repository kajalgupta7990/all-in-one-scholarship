<?php 
require_once __DIR__ . '/auth.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Government Scholarship Portal - Single stop solution for student scholarship applications and disbursements.">
    <title>Government Scholarship Portal</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"> 
    <!-- Custom Style CSS --> 
    <link href="assets/css/style.css" rel="stylesheet"> 
</head> 
<body> 
 
    <!-- Government Top Bar --> 
    <div class="gov-top-bar text-white d-none d-md-block"> 
        <div class="container d-flex justify-content-between align-items-center"> 
            <div> 
                <span class="me-3"><i class="fas fa-landmark me-1 text-warning"></i> Ministry of Electronics & IT, Govt. of India</span> 
                <span><i class="fas fa-phone-alt me-1"></i> Helpline: 1800-111-222</span> 
            </div> 
            <div class="d-flex align-items-center gap-3"> 
                <a href="#" class="text-white text-decoration-none">Skip to main content</a> | 
                <a href="#" class="text-white text-decoration-none">A+</a>  
                <a href="#" class="text-white text-decoration-none">A</a>  
                <a href="#" class="text-white text-decoration-none">A-</a> | 
                <span class="badge bg-secondary">English</span> 
            </div> 
        </div> 
    </div> 
 
    <!-- Main Navigation Bar --> 
    <nav class="navbar navbar-expand-lg navbar-dark gov-navbar sticky-top"> 
        <div class="container"> 
            <a class="navbar-brand" href="index.php"> 
                <i class="fas fa-graduation-cap text-warning fs-3"></i> 
                <div> 
                    <span class="d-block lh-1 text-white fs-5 fw-bold">SCHOLARSHIPS</span> 
                    <small class="d-block text-uppercase text-light font-weight-light" style="font-size: 0.65rem; letter-spacing: 1px;">National Portal</small> 
                </div> 
            </a> 
             
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation"> 
                <span class="navbar-toggler-icon"></span> 
            </button> 
             
            <div class="collapse navbar-collapse" id="mainNavbar"> 
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center"> 
                    <li class="nav-item"> 
                        <a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Home</a> 
                    </li> 
                    <li class="nav-item"> 
                        <a class="nav-link" href="about.php">About</a> 
                    </li> 
                    <li class="nav-item"> 
                        <a class="nav-link" href="scholarships.php">Scholarships</a> 
                    </li> 
                    <li class="nav-item"> 
                        <a class="nav-link" href="checker.php"><i class="fas fa-calculator me-1"></i> Checker</a> 
                    </li> 
                    <li class="nav-item"> 
                        <a class="nav-link" href="documents.php">Documents</a> 
                    </li> 
                    <li class="nav-item"> 
                        <a class="nav-link" href="announcements.php">Announcements</a> 
                    </li> 
                    <li class="nav-item"> 
                        <a class="nav-link" href="faq.php">FAQ</a> 
                    </li> 
                    <li class="nav-item"> 
                        <a class="nav-link" href="contact.php">Contact</a> 
                    </li> 
                     
                    <!-- Auth & Theme Items --> 
                    <?php if (isLoggedIn()): ?> 
                    <li class="nav-item ms-lg-3 my-2 my-lg-0"> 
                        <a href="dashboard.php" class="btn btn-outline-light btn-sm w-100 me-2"><i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['student_name'] ?? 'Dashboard'); ?></a> 
                    </li> 
                    <li class="nav-item my-2 my-lg-0"> 
                        <a href="logout.php" class="btn btn-danger btn-sm w-100 text-white font-weight-bold"><i class="fas fa-sign-out-alt me-1"></i> Logout</a> 
                    </li> 
                    <?php else: ?> 
                    <li class="nav-item ms-lg-3 my-2 my-lg-0"> 
                        <a href="dashboard.php" class="btn btn-outline-light btn-sm w-100 me-2"><i class="fas fa-user-circle me-1"></i> Portal Dashboard</a> 
                    </li> 
                    <li class="nav-item my-2 my-lg-0"> 
                        <a href="login.php" class="btn btn-warning btn-sm w-100 text-dark font-weight-bold"><i class="fas fa-sign-in-alt me-1"></i> Student Login</a> 
                    </li> 
                    <!-- Admin Panel -->
<li class="nav-item my-2 my-lg-0 ms-lg-2"> 
   <a href="http://localhost:51234/Admin/Dashboard"
       class="btn btn-primary btn-sm w-100 text-white font-weight-bold"> 
        <i class="fas fa-user-shield me-1"></i> Admin Panel 
    </a> 
</li>
                    <?php endif; ?> 
                     
                    <!-- Theme Toggle --> 
                    <li class="nav-item ms-lg-3 text-center"> 
                        <button class="theme-toggle-btn w-100 justify-content-center" id="themeToggle" title="Toggle Light/Dark Theme"> 
                            <i class="fas fa-moon text-white"></i> 
                        </button> 
                    </li> 
                </ul> 
            </div> 
        </div> 
    </nav> 
