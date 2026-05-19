<?php include 'includes/header.php'; ?>
<?php
$base_url = $base_url ?? '';
$about_text = getSetting($pdo, 'about_text');
$education_short = getSetting($pdo, 'education_short');
$education_school = getSetting($pdo, 'education_school');
$location = getSetting($pdo, 'location');
$email = getSetting($pdo, 'email');
$phone = getSetting($pdo, 'phone');
$birthdate = getSetting($pdo, 'birthdate');
$full_name = getSetting($pdo, 'full_name');
?>

    <section id="about" style="padding-top: 150px; min-height: 100vh;">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <div class="section-label">ABOUT ME</div>
                    <h2 class="section-title">Get to know <?= htmlspecialchars($full_name) ?> (aka GARU)</h2>
                    <p class="hero-desc mb-4">
                        <?= htmlspecialchars($about_text) ?>
                    </p>
                    <a href="<?= $base_url ?>/experience" class="btn-outline-custom">View More</a>
                </div>
                
                <div class="col-lg-8">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-card">
                                <i class="fa-solid fa-graduation-cap info-icon"></i>
                                <h3 class="info-title">Education</h3>
                                <p class="info-text mb-1" style="font-weight: 700; color: var(--text-primary);"><?= htmlspecialchars($education_short) ?></p>
                                <p class="info-text"><?= htmlspecialchars_decode($education_school) ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <i class="fa-solid fa-location-dot info-icon"></i>
                                <h3 class="info-title">Location</h3>
                                <p class="info-text"><?= htmlspecialchars_decode($location) ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <i class="fa-solid fa-envelope info-icon"></i>
                                <h3 class="info-title">Email</h3>
                                <p class="info-text"><?= htmlspecialchars($email) ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <i class="fa-regular fa-calendar info-icon"></i>
                                <h3 class="info-title">Date of Birth</h3>
                                <p class="info-text"><?= htmlspecialchars($birthdate) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
