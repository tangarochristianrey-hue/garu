<?php include 'includes/header.php'; ?>
<?php
$base_url = $base_url ?? '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$proj = $stmt->fetch();

if (!$proj) {
    echo "<script>window.location.href='" . $base_url . "/projects';</script>";
    exit;
}

$tags = explode(',', $proj['tags']);
?>

    <section id="project-details" style="padding-top: 150px; min-height: 100vh; padding-bottom: 80px;">
        <div class="container">
            <!-- Breadcrumbs -->
            <div class="reveal mb-4">
                <a href="<?= $base_url ?>/projects" class="back-link d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-arrow-left-long"></i> Back to Projects
                </a>
            </div>
            
            <div class="row g-5">
                <!-- Left Side: Massive Premium Media Showcase -->
                <div class="col-lg-7 reveal">
                    <div class="details-showcase border border-secondary rounded-4 overflow-hidden shadow-lg">
                        <?php 
                        $image = $proj['main_image'];
                        $images = $proj['additional_images'];
                        
                        $imagesList = [];
                        if (!empty($image) && trim($image) !== '') {
                            $imagesList[] = trim($image);
                        }
                        if (!empty($images) && trim($images) !== '') {
                            $split_images = array_map('trim', explode(',', $images));
                            foreach ($split_images as $s_img) {
                                if ($s_img !== '') {
                                    $imagesList[] = $s_img;
                                }
                            }
                        }
                        $imagesList = array_values(array_unique($imagesList));
                        
                        if (count($imagesList) > 0): 
                            if (count($imagesList) > 1):
                        ?>
                            <!-- Multi-image Carousel Slider -->
                            <div id="projectDetailsCarousel" class="carousel slide h-100 w-100" data-bs-ride="carousel" data-bs-interval="2000">
                                <div class="carousel-indicators">
                                    <?php foreach ($imagesList as $i => $img): ?>
                                        <button type="button" data-bs-target="#projectDetailsCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-current="<?= $i === 0 ? 'true' : 'false' ?>"></button>
                                    <?php endforeach; ?>
                                </div>
                                <div class="carousel-inner h-100 w-100">
                                    <?php foreach ($imagesList as $i => $img): ?>
                                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?> h-100 w-100 position-relative" style="overflow: hidden; background: #000;">
                                            <!-- Dynamic Blurred Glass Backdrop for portrait/mixed shapes -->
                                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('<?= $base_url ?>/assets/images/projects/<?= htmlspecialchars($img) ?>') no-repeat center center / cover; filter: blur(25px); opacity: 0.45; transform: scale(1.15); z-index: 1;"></div>
                                            <!-- Contained sharp image -->
                                            <img src="<?= $base_url ?>/assets/images/projects/<?= htmlspecialchars($img) ?>" class="d-block w-100 h-100 position-relative" style="object-fit: contain; z-index: 2;">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Single Image with premium blurring backdrop -->
                            <div class="h-100 w-100 position-relative" style="overflow: hidden; background: #000;">
                                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('<?= $base_url ?>/assets/images/projects/<?= htmlspecialchars($imagesList[0]) ?>') no-repeat center center / cover; filter: blur(25px); opacity: 0.45; transform: scale(1.15); z-index: 1;"></div>
                                <img src="<?= $base_url ?>/assets/images/projects/<?= htmlspecialchars($imagesList[0]) ?>" class="w-100 h-100 position-relative" style="object-fit: contain; z-index: 2;">
                            </div>
                        <?php endif; ?>
                        <?php else: 
                            // Dynamic Neon Fallback Accent
                            $accentColor = '#6366f1';
                            $accentGlow = 'rgba(99, 102, 241, 0.15)';
                            if ($proj['id'] % 3 === 1) {
                                $accentColor = '#10b981';
                                $accentGlow = 'rgba(16, 185, 129, 0.15)';
                            } elseif ($proj['id'] % 3 === 0) {
                                $accentColor = '#f43f5e';
                                $accentGlow = 'rgba(244, 63, 94, 0.15)';
                            }
                        ?>
                            <!-- Glowing Mesh Gradient fallback with FontAwesome icon -->
                            <div class="h-100 w-100 d-flex align-items-center justify-content-center" style="background: radial-gradient(circle, <?= $accentGlow ?>, #121212);">
                                <i class="<?= htmlspecialchars($proj['icon_class']) ?> fa-5x" style="color: <?= $accentColor ?>; text-shadow: 0 0 35px <?= $accentColor ?>;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Right Side: High-End Details Sidebar -->
                <div class="col-lg-5 reveal" style="transition-delay: 0.15s;">
                    <div class="d-flex flex-column justify-content-between h-100">
                        <div>
                            <!-- Meta Badges -->
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge py-2 px-3 rounded-pill text-uppercase meta-badge">
                                    <i class="fa-solid fa-circle-nodes me-2"></i><?= htmlspecialchars($proj['client'] ?? 'Personal Project') ?>
                                </span>
                                <span class="badge py-2 px-3 rounded-pill meta-badge">
                                    <i class="fa-solid fa-calendar-day me-2"></i><?= htmlspecialchars($proj['project_date'] ?? 'N/A') ?>
                                </span>
                            </div>
                            
                            <!-- Project Title -->
                            <h1 class="fw-bold mb-4 text-white-target" style="font-size: 2.8rem; letter-spacing: -1px; line-height: 1.1;"><?= htmlspecialchars($proj['title']) ?></h1>
                            
                            <!-- Detailed Description -->
                            <p class="text-secondary-custom mb-5" style="line-height: 1.8; font-size: 0.95rem;">
                                <?= nl2br(htmlspecialchars($proj['description'])) ?>
                            </p>
                            
                            <?php if (!empty($proj['link']) && trim($proj['link']) !== '' && trim($proj['link']) !== '#'): ?>
                                <div class="mb-5">
                                    <a href="<?= htmlspecialchars(trim($proj['link'])) ?>" target="_blank" class="btn-primary-custom px-4 py-3 text-decoration-none d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #10b981, #059669); border: none; border-radius: 12px; font-weight: 600; font-size: 0.85rem; letter-spacing: 0.5px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(16, 185, 129, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(16, 185, 129, 0.25)';">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Launch Live Project
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <!-- Technologies Stack -->
                            <h6 class="fw-semibold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px; color: var(--text-secondary);">Technologies Applied</h6>
                            <div class="d-flex flex-wrap gap-2 mb-5">
                                <?php foreach ($tags as $tag): ?>
                                    <span class="project-tag m-0" style="font-size: 0.7rem;"><?= htmlspecialchars(trim($tag)) ?></span>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Premium CTA Panel -->
                            <div class="cta-panel p-4 rounded-4 border d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-1 text-white-target fw-bold" style="font-size: 0.9rem;">Interested in this code?</h6>
                                    <p class="text-secondary-custom mb-0 small">Request full project source details.</p>
                                </div>
                                <a href="<?= $base_url ?>/contact" class="btn-cta-link">
                                    Get In Touch <i class="fa-solid fa-paper-plane ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <style>
        /* Dedicated project details styling */
        .back-link {
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .back-link:hover {
            color: #10b981;
            transform: translateX(-4px);
        }
        
        .details-showcase {
            height: 480px;
            background: #111;
            position: relative;
            border-color: rgba(255, 255, 255, 0.05) !important;
        }
        [data-theme="light"] .details-showcase {
            background: #f1f5f9;
            border-color: rgba(0, 0, 0, 0.05) !important;
        }
        
        .meta-badge {
            font-size: 0.7rem; 
            background: rgba(255, 255, 255, 0.04); 
            color: var(--text-secondary); 
            border: 1px solid rgba(255,255,255,0.05);
        }
        [data-theme="light"] .meta-badge {
            background: rgba(0, 0, 0, 0.02); 
            border-color: rgba(0, 0, 0, 0.05);
        }
        
        /* Premium CTA Panel box */
        .cta-panel {
            background: rgba(255, 255, 255, 0.02);
            border-color: rgba(255, 255, 255, 0.04) !important;
        }
        [data-theme="light"] .cta-panel {
            background: rgba(0, 0, 0, 0.02);
            border-color: rgba(0, 0, 0, 0.05) !important;
        }
        .btn-cta-link {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            font-size: 0.8rem;
            font-weight: 600;
            background: var(--text-primary);
            color: var(--bg-main) !important;
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-cta-link:hover {
            background: #10b981;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }
        
        .carousel-indicators [data-bs-target] {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--text-secondary);
            opacity: 0.4;
            transition: all 0.3s ease;
            border: none;
            margin: 0 4px;
        }
        .carousel-indicators .active {
            opacity: 1;
            transform: scale(1.3);
            background-color: #10b981;
        }
        
        /* Styled tech badges */
        .project-tag {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-right: 8px;
            margin-bottom: 8px;
            display: inline-block;
            transition: all 0.3s ease;
        }
        [data-theme="light"] .project-tag {
            background: rgba(0, 0, 0, 0.02);
            border-color: rgba(0, 0, 0, 0.05);
        }
        
        [data-theme="light"] .text-white-target {
            color: #0f172a !important;
        }
    </style>

<?php include 'includes/footer.php'; ?>
