<?php include 'includes/header.php'; ?>
<?php
// Fallback guard — $base_url is always set by includes/db.php via header.php
if (!isset($base_url)) {
    $is_localhost = isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1' || in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
    $base_url = $is_localhost ? '/garu' : '';
}
$stmtWork = $pdo->query("SELECT * FROM experience WHERE type='work' ORDER BY id ASC");
$workExp = $stmtWork->fetchAll();

$stmtEd = $pdo->query("SELECT * FROM experience WHERE type='education' ORDER BY id ASC");
$education = $stmtEd->fetchAll();
?>

    <!-- Experience & Education Section -->
    <section id="experience" class="py-5 mt-5 pt-5" style="min-height: 100vh; display: flex; align-items: center;">
        <div class="container mt-5">
            <div class="row g-5">
                <!-- Work Experience Column -->
                <div class="col-lg-6 reveal">
                    <div class="section-label">EXPERIENCE</div>
                    <h2 class="section-heading mb-5">Work History</h2>
                    
                    <div class="timeline-container">
                        <?php if (empty($workExp)): ?>
                            <div class="text-center py-4">
                                <div class="info-card d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; border-radius: 16px !important; padding: 0 !important;">
                                    <i class="fa-solid fa-briefcase" style="font-size: 1.4rem; color: var(--text-secondary);"></i>
                                </div>
                                <p class="mb-0" style="color: var(--text-secondary); font-size: 0.88rem;">No work experience added yet.</p>
                            </div>
                        <?php else: ?>
                        <?php foreach($workExp as $work): ?>
                        <div class="timeline-item work-item">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="timeline-icon-box work-icon">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 fw-bold text-white-target"><?= $work['title'] ?></h4>
                                    <span class="company-tag mt-1 d-inline-block"><?= htmlspecialchars($work['company']) ?></span>
                                </div>
                            </div>
                            
                            <div class="duration-badge mb-3">
                                <i class="fa-regular fa-calendar-days me-2"></i><?= htmlspecialchars($work['duration']) ?>
                            </div>
                            
                            <ul class="text-secondary-custom timeline-list" style="line-height: 1.8;">
                                <?php 
                                    $sentences = explode(".", $work['description']);
                                    foreach($sentences as $sentence) {
                                        $sentence = trim($sentence);
                                        if(!empty($sentence)) {
                                            echo "<li>" . $sentence . ".</li>";
                                        }
                                    }
                                ?>
                            </ul>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Education Column -->
                <div class="col-lg-6 reveal" style="transition-delay: 0.2s;">
                    <div class="section-label">BACKGROUND</div>
                    <h2 class="section-heading mb-5">Past Education</h2>
                    
                    <div class="timeline-container">
                        <?php if (empty($education)): ?>
                            <div class="text-center py-4">
                                <div class="info-card d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; border-radius: 16px !important; padding: 0 !important;">
                                    <i class="fa-solid fa-graduation-cap" style="font-size: 1.4rem; color: var(--text-secondary);"></i>
                                </div>
                                <p class="mb-0" style="color: var(--text-secondary); font-size: 0.88rem;">No education records added yet.</p>
                            </div>
                        <?php else: ?>
                        <?php foreach($education as $ed): ?>
                        <div class="timeline-item education-item">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="timeline-icon-box education-icon">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 fw-bold text-white-target"><?= $ed['title'] ?></h4>
                                    <span class="company-tag mt-1 d-inline-block"><?= htmlspecialchars($ed['company']) ?></span>
                                </div>
                            </div>
                            
                            <div class="duration-badge mb-3">
                                <i class="fa-regular fa-calendar-days me-2"></i><?= htmlspecialchars($ed['duration']) ?>
                            </div>
                            
                            <!-- Allow rendering html in education descriptions directly -->
                            <div class="text-secondary-custom timeline-desc-text small" style="line-height: 1.8;">
                                <?= nl2br($ed['description']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <style>
        /* Modern Premium Timeline Cards styling */
        .timeline-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .timeline-item {
            position: relative;
            padding: 28px 32px;
            background: rgba(20, 20, 20, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .work-item {
            border-left: 4px solid #10b981; /* Glowing Emerald accent for work */
        }
        .education-item {
            border-left: 4px solid #6366f1; /* Glowing Indigo accent for education */
        }
        .timeline-item:hover {
            transform: translateY(-6px);
            background: rgba(30, 30, 30, 0.8);
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }
        
        /* Floating Icon Boxes */
        .timeline-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            transition: all 0.3s ease;
        }
        .work-icon {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .education-icon {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }
        .timeline-item:hover .work-icon {
            background: #10b981;
            color: #ffffff;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
        }
        .timeline-item:hover .education-icon {
            background: #6366f1;
            color: #ffffff;
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
        }
        
        /* Tags & Badges */
        .company-tag {
            font-size: 0.8rem;
            color: var(--text-secondary);
            font-weight: 500;
        }
        .duration-badge {
            display: inline-flex;
            align-items: center;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-secondary);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .timeline-list {
            padding-left: 20px;
            margin-bottom: 0;
        }
        .timeline-list li {
            margin-bottom: 8px;
        }
        .timeline-list li::marker {
            color: #10b981;
        }
        
        /* Theme adaptive styles */
        [data-theme="light"] .timeline-item {
            background: rgba(255, 255, 255, 0.7);
            border-color: rgba(0, 0, 0, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }
        [data-theme="light"] .timeline-item:hover {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(0, 0, 0, 0.08);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
        }
        [data-theme="light"] .duration-badge {
            background: rgba(0, 0, 0, 0.03);
            border-color: rgba(0, 0, 0, 0.03);
        }
        [data-theme="light"] .text-white-target {
            color: #0f172a !important;
        }
    </style>

<?php include 'includes/footer.php'; ?>
