<?php include 'includes/header.php'; ?>
<?php
// Fallback guard — $base_url is always set by includes/db.php via header.php
if (!isset($base_url)) {
    $is_localhost = isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1' || in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
    $base_url = $is_localhost ? '/garu' : '';
}
$stmt = $pdo->query("SELECT * FROM projects ORDER BY id ASC");
$projects = $stmt->fetchAll();
?>

    <section id="projects" style="padding-top: 150px; min-height: 100vh; padding-bottom: 80px;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-5 reveal">
                <div>
                    <div class="section-label">FEATURED PROJECTS</div>
                    <h2 class="section-title mb-0">Some Things I've Built</h2>
                </div>
                <a href="#" class="btn-outline-custom" style="padding: 10px 24px; font-size: 0.85rem;">View All Projects</a>
            </div>
            
            <div class="row g-4">
                <?php if (empty($projects)): ?>
                    <div class="col-12">
                        <div class="text-center py-5 my-4">
                            <!-- Animated pulse ring -->
                            <div class="position-relative d-inline-flex align-items-center justify-content-center mb-5" style="width: 100px; height: 100px;">
                                <div style="
                                    position: absolute; inset: 0;
                                    border-radius: 50%;
                                    border: 1px solid rgba(255,255,255,0.08);
                                    animation: pulseRing 2.5s ease-out infinite;
                                "></div>
                                <div style="
                                    position: absolute; inset: 12px;
                                    border-radius: 50%;
                                    border: 1px solid rgba(255,255,255,0.05);
                                    animation: pulseRing 2.5s ease-out infinite 0.4s;
                                "></div>
                                <div class="info-card d-flex align-items-center justify-content-center" style="width: 72px; height: 72px; border-radius: 50% !important; padding: 0 !important; height: auto !important; flex-shrink: 0;">
                                    <i class="fa-regular fa-clock" style="font-size: 1.6rem; color: var(--text-secondary);"></i>
                                </div>
                            </div>

                            <div class="section-label mb-3">COMING SOON</div>
                            <h3 class="fw-bold text-white mb-3" style="font-family: 'Space Grotesk', sans-serif; font-size: 1.6rem; letter-spacing: -0.03em;">
                                Case Studies Dropping Soon
                            </h3>
                            <p style="color: var(--text-secondary); max-width: 440px; margin: 0 auto 2rem; font-size: 0.9rem; line-height: 1.7;">
                                I'm currently documenting and polishing my projects into proper case studies. 
                                Check back soon — great things are in the works.
                            </p>
                            <a href="<?= $base_url ?>/contact" class="btn-outline-custom" style="padding: 12px 32px; font-size: 0.85rem;">
                                Get Notified &nbsp;<i class="fa-solid fa-bell"></i>
                            </a>
                        </div>
                    </div>
                    <style>
                        @keyframes pulseRing {
                            0%   { transform: scale(0.9); opacity: 0.6; }
                            70%  { transform: scale(1.25); opacity: 0; }
                            100% { transform: scale(1.25); opacity: 0; }
                        }
                    </style>
                <?php else: ?>
                    <?php foreach($projects as $index => $proj): 
                        $tags = explode(',', $proj['tags']);
                    ?>
                    <div class="col-lg-4 col-md-6 reveal" style="transition-delay: <?= $index * 0.1 ?>s;">
                        <div class="project-card h-100" style="cursor: pointer;" onclick="window.location.href='<?= $base_url ?>/project/<?= $proj['id'] ?>';">
                            <div class="project-img">
                                <?php if(!empty($proj['main_image'])): ?>
                                    <img src="<?= $base_url ?>/assets/images/projects/<?= htmlspecialchars($proj['main_image']) ?>" alt="Project Image" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s;">
                                <?php else: ?>
                                    <div class="project-img-fallback">
                                        <i class="<?= htmlspecialchars($proj['icon_class']) ?> fa-3x"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="project-body d-flex flex-column flex-grow-1">
                                <h4 class="project-title mb-2 text-white-target"><?= htmlspecialchars($proj['title']) ?></h4>
                                <p class="project-desc mb-4"><?= htmlspecialchars($proj['description']) ?></p>
                                
                                <div class="d-flex flex-wrap mb-4">
                                    <?php foreach($tags as $tag): ?>
                                    <span class="project-tag"><?= htmlspecialchars(trim($tag)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- Premium Interactive Call-to-action Footer -->
                                <div class="mt-auto pt-4 d-flex align-items-center justify-content-between border-top" style="border-color: rgba(255,255,255,0.05) !important;">
                                    <span class="text-secondary-custom small" style="font-size: 0.75rem;">Interactive Case Study</span>
                                    <span class="project-link-btn">
                                        View Details <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </section>
    
    <style>
        /* Modern Premium Project Cards styling */
        .project-card {
            background: rgba(20, 20, 20, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }
        .project-card:hover {
            transform: translateY(-8px);
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }
        
        /* Fallback icon backgrounds with custom neon glows */
        .project-img {
            height: 220px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
        }
        .project-img-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s ease;
        }
        .project-img img {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .project-card:hover .project-img img {
            transform: scale(1.08);
        }
        
        /* Neon Accents based on layout index */
        .col-lg-4:nth-child(3n+1) .project-img-fallback {
            background: radial-gradient(circle at 50% 50%, rgba(16, 185, 129, 0.18), rgba(15, 15, 15, 1));
        }
        .col-lg-4:nth-child(3n+1) .project-img-fallback i {
            color: #10b981 !important;
            text-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
        }
        
        .col-lg-4:nth-child(3n+2) .project-img-fallback {
            background: radial-gradient(circle at 50% 50%, rgba(99, 102, 241, 0.18), rgba(15, 15, 15, 1));
        }
        .col-lg-4:nth-child(3n+2) .project-img-fallback i {
            color: #6366f1 !important;
            text-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
        }
        
        .col-lg-4:nth-child(3n+3) .project-img-fallback {
            background: radial-gradient(circle at 50% 50%, rgba(244, 63, 94, 0.18), rgba(15, 15, 15, 1));
        }
        .col-lg-4:nth-child(3n+3) .project-img-fallback i {
            color: #f43f5e !important;
            text-shadow: 0 0 20px rgba(244, 63, 94, 0.4);
        }
        
        .project-card:hover .project-img-fallback {
            transform: scale(1.06);
        }
        
        /* Card Body details */
        .project-body {
            padding: 30px;
        }
        .project-title {
            font-size: 1.25rem;
            font-weight: 700;
            transition: color 0.3s ease;
        }
        .project-card:hover .project-title {
            color: var(--text-primary);
        }
        .project-desc {
            font-size: 0.85rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }
        
        /* Styled Badges */
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
        .project-card:hover .project-tag {
            border-color: rgba(255, 255, 255, 0.15);
            color: var(--text-primary);
        }
        
        /* Interactive call to action */
        .project-link-btn {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .project-link-btn i {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .project-card:hover .project-link-btn {
            color: var(--text-primary);
        }
        .project-card:hover .project-link-btn i {
            transform: translateX(5px);
        }
        
        /* Theme adaptive styles */
        [data-theme="light"] .project-card {
            background: rgba(255, 255, 255, 0.7);
            border-color: rgba(0, 0, 0, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }
        [data-theme="light"] .project-card:hover {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(0, 0, 0, 0.08);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
        }
        [data-theme="light"] .text-white-target {
            color: #0f172a !important;
        }
        [data-theme="light"] .project-card:hover .project-title {
            color: #0f172a !important;
        }
        [data-theme="light"] .project-tag {
            background: rgba(0, 0, 0, 0.02);
            border-color: rgba(0, 0, 0, 0.05);
        }
        [data-theme="light"] .project-card:hover .project-tag {
            border-color: rgba(0, 0, 0, 0.15);
            color: #0f172a;
        }
        [data-theme="light"] .col-lg-4:nth-child(3n+1) .project-img-fallback {
            background: radial-gradient(circle at 50% 50%, rgba(16, 185, 129, 0.12), rgba(240, 240, 240, 0.5));
        }
        [data-theme="light"] .col-lg-4:nth-child(3n+2) .project-img-fallback {
            background: radial-gradient(circle at 50% 50%, rgba(99, 102, 241, 0.12), rgba(240, 240, 240, 0.5));
        }
        [data-theme="light"] .col-lg-4:nth-child(3n+3) .project-img-fallback {
            background: radial-gradient(circle at 50% 50%, rgba(244, 63, 94, 0.12), rgba(240, 240, 240, 0.5));
        }
    </style>

<?php include 'includes/footer.php'; ?>
