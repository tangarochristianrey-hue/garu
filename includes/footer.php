<?php
// Fallback guard for $base_url — always set by includes/db.php before this file
if (!isset($base_url)) {
    $is_localhost = isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1' || in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
    $base_url = $is_localhost ? '/garu' : '';
}
$f_facebook = getSetting($pdo, 'facebook');
$f_github = getSetting($pdo, 'github');
$f_linkedin = getSetting($pdo, 'linkedin');
$f_email = getSetting($pdo, 'email');
$f_phone = getSetting($pdo, 'phone');
$f_location = getSetting($pdo, 'location');
?>
    <!-- Minimalist Footer -->
    <footer class="pt-5 pb-4 mt-5" style="border-top: 1px solid var(--border-color); background: var(--bg-main);">
        <div class="container mt-4 reveal">
            <div class="row g-5 mb-5">
                <div class="col-lg-4 col-md-6">
                    <div class="mb-4">
                        <img src="<?= $base_url ?>/assets/images/logo.png" alt="CRT Logo" class="footer-logo">
                    </div>
                    <p class="text-secondary-custom mb-4 pe-lg-4 fs-6" style="line-height: 1.6; color: var(--text-secondary);">
                        Building digital solutions that make an impact. Let's work together to bring your ideas to life through elegant code and intuitive design.
                    </p>
                    <div class="d-flex gap-3 social-icons">
                        <a href="<?= htmlspecialchars($f_github) ?>" target="_blank" class="text-decoration-none"><i class="fa-brands fa-github"></i></a>
                        <a href="<?= htmlspecialchars($f_linkedin) ?>" target="_blank" class="text-decoration-none"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="<?= htmlspecialchars($f_facebook) ?>" target="_blank" class="text-decoration-none"><i class="fa-brands fa-facebook-f"></i></a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <h5 class="text-white mb-4 fw-bold fs-6 text-uppercase" style="letter-spacing: 1px;">Explore</h5>
                    <ul class="list-unstyled d-flex flex-column gap-3">
                        <li><a href="<?= $base_url ?>/" class="text-decoration-none footer-link" style="color: var(--text-secondary);">Home</a></li>
                        <li><a href="<?= $base_url ?>/about" class="text-decoration-none footer-link" style="color: var(--text-secondary);">About Me</a></li>
                        <li><a href="<?= $base_url ?>/projects" class="text-decoration-none footer-link" style="color: var(--text-secondary);">Projects</a></li>
                        <li><a href="<?= $base_url ?>/experience" class="text-decoration-none footer-link" style="color: var(--text-secondary);">Experience</a></li>
                        <li><a href="<?= $base_url ?>/reviews" class="text-decoration-none footer-link" style="color: var(--text-secondary);">Reviews</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4 fw-bold fs-6 text-uppercase" style="letter-spacing: 1px;">Contact</h5>
                    <ul class="list-unstyled d-flex flex-column gap-3">
                        <li style="color: var(--text-secondary);" class="d-flex gap-3"><i class="fa-solid fa-envelope mt-1"></i> <?= htmlspecialchars($f_email) ?></li>
                        <li style="color: var(--text-secondary);" class="d-flex gap-3"><i class="fa-solid fa-phone mt-1"></i> <?= htmlspecialchars($f_phone) ?></li>
                        <li style="color: var(--text-secondary);" class="d-flex gap-3"><i class="fa-solid fa-location-dot mt-1"></i> <?= htmlspecialchars($f_location) ?></li>
                    </ul>
                </div>
                
                <!-- CTA -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4 fw-bold fs-6 text-uppercase" style="letter-spacing: 1px;">Ready to work?</h5>
                    <p class="mb-4" style="color: var(--text-secondary); font-size: 0.9rem;">I'm currently available for freelance work and open to new opportunities.</p>
                    <a href="<?= $base_url ?>/contact" class="btn-outline-custom w-100 text-center text-decoration-none d-inline-block">Get in Touch</a>
                </div>
            </div>
            
            <!-- Bottom Bar -->
            <div class="pt-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-center" style="border-color: var(--border-color) !important;">
                <p class="mb-0 small" style="color: var(--text-secondary);">&copy; <?php echo date('Y'); ?> Christian Rey M. Tangaro. All rights reserved.</p>
                <p class="mb-0 small mt-2 mt-md-0 d-flex align-items-center" style="color: var(--text-secondary);">
                    Built with <a href="#" target="_blank" class="fw-semibold ms-1 text-decoration-none" style="color: var(--text-primary); transition: 0.2s;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='var(--text-primary)'">HJexperts</a>
                </p>
            </div>
        </div>
        
        <style>
            .footer-link { transition: 0.2s; }
            .footer-link:hover { color: var(--text-primary) !important; }
            
            /* High-End Responsive Mobile Footer Layout Optimization */
            @media (max-width: 767px) {
                footer {
                    padding-top: 3rem !important;
                    padding-bottom: 2rem !important;
                }
                .footer-logo {
                    max-height: 42px !important;
                    width: auto !important;
                    margin-bottom: 0.5rem !important;
                }
                footer p.text-secondary-custom,
                footer .col-lg-3:last-child { 
                    display: none !important; /* Cleanly hide bloated text block & duplicate CTA on mobile */
                }
                footer .row.g-5 {
                    --bs-gutter-y: 2rem !important;
                    --bs-gutter-x: 1rem !important;
                    margin-bottom: 2rem !important;
                }
                footer .col-lg-4 {
                    text-align: center !important;
                    margin-bottom: 0.5rem !important;
                }
                footer .social-icons {
                    justify-content: center !important;
                    margin-bottom: 0.5rem !important;
                }
                /* Stack elements vertically at full width to prevent squishing long contact lines */
                footer .col-lg-2, 
                footer .col-lg-3:nth-child(3) {
                    width: 100% !important;
                    margin-top: 0 !important;
                    text-align: center !important;
                }
                footer h5 {
                    font-size: 0.85rem !important;
                    margin-bottom: 1rem !important;
                    letter-spacing: 2px !important;
                    color: var(--text-primary) !important;
                }
                /* Display Explore Links horizontally inline */
                footer .col-lg-2 ul {
                    flex-direction: row !important;
                    justify-content: center !important;
                    flex-wrap: wrap !important;
                    gap: 1.5rem !important;
                }
                /* Stack Contact Items with generous line heights and keep icons visible */
                footer .col-lg-3:nth-child(3) ul {
                    flex-direction: column !important;
                    align-items: center !important;
                    gap: 0.8rem !important;
                }
                footer ul li {
                    font-size: 0.85rem !important;
                    line-height: 1.4 !important;
                }
                footer ul li i {
                    font-size: 0.9rem !important;
                    color: var(--text-primary) !important;
                    opacity: 0.85;
                }
                footer .pt-4.border-top {
                    text-align: center !important;
                    margin-top: 1rem !important;
                }
            }
        </style>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS -->
    <?php
    $js_file = dirname(__DIR__) . '/assets/js/main.js';
    $js_version = file_exists($js_file) ? filemtime($js_file) : time();
    ?>
    <script src="<?= $base_url ?>/assets/js/main.js?v=<?= $js_version ?>"></script>
</body>
</html>
