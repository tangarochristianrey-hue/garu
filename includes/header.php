<?php
session_start();
include_once __DIR__ . '/db.php';

// Track Page View
try {
    $page_url = $_SERVER['REQUEST_URI'];
    // Filter out admin routes from tracking
    if (strpos($page_url, '/admin/') === false) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $visit_date = date('Y-m-d');
        $stmt = $pdo->prepare("INSERT INTO analytics (page_url, ip_address, visit_date) VALUES (?, ?, ?)");
        $stmt->execute([$page_url, $ip_address, $visit_date]);
    }
} catch(Exception $e) {}

$current_page = basename($_SERVER['PHP_SELF'], ".php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Christian Rey M. Tangaro | Portfolio</title>
    <link rel="icon" type="image/png" href="<?= $base_url ?>/assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php 
    $css_file = dirname(__DIR__) . '/assets/css/style.css';
    $css_version = file_exists($css_file) ? filemtime($css_file) : time();
    ?>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css?v=<?= $css_version ?>">
</head>
<body>
 
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= $base_url ?>/">
                <img src="<?= $base_url ?>/assets/images/logo.png" alt="CRT Logo" class="navbar-logo">
            </a>
            
            <button class="navbar-toggler shadow-none border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa-solid fa-bars text-white fs-4"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link <?= $current_page == 'index' ? 'active' : '' ?>" href="<?= $base_url ?>/">Home</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page == 'about' ? 'active' : '' ?>" href="<?= $base_url ?>/about">About</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page == 'skills' ? 'active' : '' ?>" href="<?= $base_url ?>/skills">Skills</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page == 'projects' ? 'active' : '' ?>" href="<?= $base_url ?>/projects">Projects</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page == 'experience' ? 'active' : '' ?>" href="<?= $base_url ?>/experience">Experience</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page == 'reviews' ? 'active' : '' ?>" href="<?= $base_url ?>/reviews">Reviews</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page == 'contact' ? 'active' : '' ?>" href="<?= $base_url ?>/contact">Contact</a></li>
                </ul>
                
                <div class="d-flex align-items-center gap-4">
                    <i class="fa-solid fa-moon mode-icon"></i>
                    <?php
                    $resume_dir = dirname(__DIR__) . '/assets/resume/';
                    $resume_file = $base_url . '/assets/resume/Tangaro_CV.pdf'; // default fallback
                    if (is_dir($resume_dir)) {
                        $files = glob($resume_dir . '*.pdf');
                        if (!empty($files)) {
                            $resume_file = $base_url . '/assets/resume/' . basename($files[0]);
                        }
                    }
                    ?>
                    <a href="<?= htmlspecialchars($resume_file) ?>" target="_blank" class="btn-nav">Download CV</a>
                </div>
            </div>
        </div>
    </nav>
