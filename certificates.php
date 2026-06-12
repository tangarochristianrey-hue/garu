<?php include 'includes/header.php'; ?>
<?php
// Fallback guard — $base_url is always set by includes/db.php via header.php
if (!isset($base_url)) {
    $is_localhost = isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1' || in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
    $base_url = $is_localhost ? '/garu' : '';
}
$stmt = $pdo->query("SELECT * FROM certificates ORDER BY year DESC, id DESC");
$certificates = $stmt->fetchAll();
?>

    <section id="certificates" style="padding-top: 150px; min-height: 100vh; padding-bottom: 80px;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-5 reveal">
                <div>
                    <div class="section-label">ACHIEVEMENTS</div>
                    <h2 class="section-title mb-0">Certifications</h2>
                    <p class="hero-desc mt-2 mb-0" style="font-size: 1rem;">Continuous learning and professional development achievements.</p>
                </div>
            </div>
            
            <div class="row g-4">
                <?php if (empty($certificates)): ?>
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
                                    <i class="fa-solid fa-certificate" style="font-size: 1.6rem; color: var(--text-secondary);"></i>
                                </div>
                            </div>

                            <div class="section-label mb-3">COMING SOON</div>
                            <h3 class="fw-bold text-white mb-3" style="font-family: 'Space Grotesk', sans-serif; font-size: 1.6rem; letter-spacing: -0.03em;">
                                Certifications Updating Soon
                            </h3>
                            <p style="color: var(--text-secondary); max-width: 440px; margin: 0 auto 2rem; font-size: 0.9rem; line-height: 1.7;">
                                I am currently organizing my certificates and achievements. 
                                Check back soon.
                            </p>
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
                    <?php foreach($certificates as $index => $cert): ?>
                    <div class="col-lg-4 col-md-6 reveal" style="transition-delay: <?= ($index % 3) * 0.1 ?>s;">
                        <div class="project-card h-100">
                            <div class="project-img p-4 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.02); min-height: 240px;">
                                <?php if(!empty($cert['image'])): ?>
                                    <img src="<?= $base_url ?>/assets/images/certificates/<?= htmlspecialchars($cert['image']) ?>" alt="Certificate Image" class="w-100 rounded" style="object-fit: contain; max-height: 200px; transition: transform 0.5s; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
                                <?php else: ?>
                                    <div class="project-img-fallback">
                                        <i class="fa-solid fa-certificate fa-3x"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="project-body d-flex flex-column flex-grow-1">
                                <h4 class="project-title mb-2 text-white-target"><?= htmlspecialchars($cert['title']) ?></h4>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="text-secondary-custom small"><i class="fa-regular fa-calendar me-1"></i> <?= htmlspecialchars($cert['month'] . ' ' . $cert['year']) ?></span>
                                    <?php if(!empty($cert['issued_by'])): ?>
                                        <span class="text-secondary-custom small"><i class="fa-solid fa-building-columns ms-2 me-1"></i> <?= htmlspecialchars($cert['issued_by']) ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if(!empty($cert['description'])): ?>
                                <p class="project-desc mb-4"><?= htmlspecialchars($cert['description']) ?></p>
                                <?php endif; ?>
                                
                                <?php if(!empty($cert['keywords'])): ?>
                                <div class="d-flex flex-wrap mt-auto pt-3 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
                                    <?php $tags = explode(',', $cert['keywords']); foreach($tags as $tag): if(trim($tag) == '') continue; ?>
                                    <span class="project-tag"><?= htmlspecialchars(trim($tag)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </section>
    
    <style>
        /* Modern Premium Project Cards styling reused and adapted for certificates */
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
        
        .project-img {
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
            transform: scale(1.05);
        }
        
        .project-body {
            padding: 30px;
        }
        .project-title {
            font-size: 1.2rem;
            font-weight: 700;
            line-height: 1.4;
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
    </style>

<?php include 'includes/footer.php'; ?>
