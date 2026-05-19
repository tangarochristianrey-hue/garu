<?php include 'includes/header.php'; ?>
<?php
$base_url = $base_url ?? '';
$hero_title = getSetting($pdo, 'hero_title');
$hero_desc = getSetting($pdo, 'hero_desc');
$email = getSetting($pdo, 'email');
$linkedin = getSetting($pdo, 'linkedin');
$facebook = getSetting($pdo, 'facebook');
$github = getSetting($pdo, 'github');
$full_name = getSetting($pdo, 'full_name');

$clean_title = strip_tags(htmlspecialchars_decode($hero_title));
?>

    <section id="home" style="padding-top: 150px; min-height: 100vh;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-center text-lg-start d-flex flex-column align-items-center align-items-lg-start">
                    <div class="hero-tag">
                        <i class="fa-solid fa-code"></i> Information Systems Student
                    </div>
                    
                    <!-- Sleek Mobile Profile Avatar (only visible on mobile/tablet) -->
                    <div class="d-block d-lg-none my-4">
                        <div class="mobile-avatar-box">
                            <img src="<?= $base_url ?>/assets/images/garu.jpg" alt="Christian Rey M. Tangaro" class="mobile-avatar-img">
                        </div>
                    </div>
                    
                    <h2 class="text-white mb-2 fs-3 fw-normal">Hi, I'm <span class="fw-bold">GARU</span> (<?= htmlspecialchars($full_name) ?>) 👋</h2>
                    <?php
                        $title_lines = explode("\n", str_replace("\r", "", trim($hero_title)));
                        $last_line = array_pop($title_lines);
                        $formatted_title = "";
                        if(count($title_lines) > 0) {
                            $formatted_title = implode("<br>", array_map('htmlspecialchars', $title_lines)) . "<br>";
                        }
                        $formatted_title .= "<span>" . htmlspecialchars($last_line) . "</span>";
                    ?>
                    <h1 class="hero-title"><?= $formatted_title ?></h1>
                    <p class="hero-desc">
                         <?= htmlspecialchars($hero_desc) ?>
                    </p>
                    
                    <div class="d-flex justify-content-center justify-content-lg-start gap-3 mb-5">
                        <a href="<?= $base_url ?>/projects" class="btn-primary-custom">View My Work</a>
                        <a href="<?= $base_url ?>/contact" class="btn-outline-custom">Let's Talk</a>
                    </div>
                    
                    <div class="social-icons d-flex justify-content-center justify-content-lg-start gap-1">
                        <a href="<?= htmlspecialchars($github) ?>" target="_blank"><i class="fa-brands fa-github"></i></a>
                        <a href="<?= htmlspecialchars($linkedin) ?>" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="<?= htmlspecialchars($facebook) ?>" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="mailto:<?= htmlspecialchars($email) ?>"><i class="fa-solid fa-envelope"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="hero-img-box">
                        <img src="<?= $base_url ?>/assets/images/garu.jpg" alt="Christian Rey M. Tangaro" class="w-100 h-100" style="object-fit: cover; border-radius: 24px;">
                        
                        <div class="floating-badge shadow-lg" style="top: 40px; left: -40px;">
                            <i class="fa-solid fa-window-maximize text-white"></i>
                            <h6>Web Developer</h6>
                        </div>
                        
                        <div class="floating-badge shadow-lg" style="bottom: 80px; left: -30px;">
                            <i class="fa-solid fa-pen-nib text-white"></i>
                            <h6>UI/UX Designer</h6>
                        </div>
                        
                        <div class="floating-badge shadow-lg" style="top: 50%; right: -40px; transform: translateY(-50%);">
                            <i class="fa-solid fa-file-contract text-white"></i>
                            <h6>System Analyst</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .mobile-avatar-box {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            padding: 4px;
            background: linear-gradient(135deg, #fff, rgba(255,255,255,0.1));
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.15), 
                        inset 0 0 15px rgba(255, 255, 255, 0.05);
            display: inline-block;
            animation: avatarPulse 4s infinite ease-in-out;
        }
        
        .mobile-avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #000;
        }
        
        @keyframes avatarPulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 25px rgba(255, 255, 255, 0.15); }
            50% { transform: scale(1.03); box-shadow: 0 0 35px rgba(255, 255, 255, 0.25); }
        }
    </style>

<?php include 'includes/footer.php'; ?>
