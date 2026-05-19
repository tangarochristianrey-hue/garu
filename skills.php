<?php include 'includes/header.php'; ?>
<?php
// Fallback guard — $base_url is always set by includes/db.php via header.php
if (!isset($base_url)) {
    $is_localhost = isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1' || in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
    $base_url = $is_localhost ? '/garu' : '';
}
$stmt = $pdo->query("SELECT * FROM skills ORDER BY id ASC");
$skills = $stmt->fetchAll();

function get_skill_color(string $name): string {
    $name = strtolower($name);
    if (strpos($name, 'html') !== false) return '#e34c26';
    if (strpos($name, 'css') !== false) return '#2563eb';
    if (strpos($name, 'javascript') !== false || strpos($name, 'js') !== false) return '#eab308';
    if (strpos($name, 'php') !== false) return '#8b5cf6';
    if (strpos($name, 'bootstrap') !== false) return '#7c3aed';
    if (strpos($name, 'figma') !== false) return '#f97316';
    if (strpos($name, 'analyst') !== false || strpos($name, 'system') !== false) return '#06b6d4';
    return '#10b981'; // default emerald
}
?>

    <section id="skills" style="padding-top: 150px; min-height: 100vh; padding-bottom: 80px;">
        <div class="container">
            <div class="section-label reveal">SKILLS</div>
            <h2 class="section-title reveal mb-5">Technologies I Work With</h2>
            <div class="row g-4 mt-2">
            <?php if (empty($skills)): ?>
                <div class="col-12">
                    <div class="text-center py-5 my-3">
                        <div class="info-card d-inline-flex align-items-center justify-content-center mb-4" style="width: 76px; height: 76px; border-radius: 20px !important; padding: 0 !important;">
                            <i class="fa-solid fa-code" style="font-size: 1.8rem; color: var(--text-secondary);"></i>
                        </div>
                        <div class="section-label mb-2">COMING SOON</div>
                        <h3 class="fw-bold text-white mb-2" style="font-family: 'Space Grotesk', sans-serif; font-size: 1.4rem; letter-spacing: -0.03em;">No Skills Listed Yet</h3>
                        <p style="color: var(--text-secondary); max-width: 400px; margin: 0 auto; font-size: 0.9rem; line-height: 1.7;">
                            Tech stack and proficiency levels will appear here once they've been added.
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($skills as $index => $skill): 
                    $color = get_skill_color($skill['name']);
                ?>
                <div class="col-md-6 col-lg-3 reveal" style="transition-delay: <?= $index * 0.08 ?>s;">
                    <div class="premium-skill-card" style="--skill-color: <?= $color ?>; --target-width: <?= htmlspecialchars($skill['percentage']) ?>%;">
                        <!-- Glassmorphic Icon Frame with dynamic back-glow on hover -->
                        <div class="premium-skill-icon-frame">
                            <?php if(!empty($skill['image_path'])): ?>
                                <?php 
                                    $src = (strpos($skill['image_path'], 'http') === 0) ? $skill['image_path'] : $base_url . '/assets/images/skills/' . $skill['image_path'];
                                ?>
                                <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($skill['name']) ?>" class="skill-icon-img">
                            <?php else: ?>
                                <i class="<?= htmlspecialchars($skill['icon_class']) ?> skill-icon-fa"></i>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Details Row -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="premium-skill-name text-white-target"><?= htmlspecialchars($skill['name']) ?></span>
                            <span class="premium-skill-percent" style="color: <?= $color ?>;"><?= htmlspecialchars($skill['percentage']) ?>%</span>
                        </div>
                        
                        <!-- Premium Neon Progress Bar -->
                        <div class="premium-progress-track">
                            <div class="premium-progress-fill"></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>
    </section>

    <style>
        /* Futuristic Glassmorphic Skills Styling */
        .premium-skill-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 20px;
            padding: 28px 24px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        /* Spotlight Glow border and shadow on hover */
        .premium-skill-card:hover {
            transform: translateY(-8px);
            border-color: var(--skill-color) !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 
                        0 0 20px rgba(255, 255, 255, 0.02),
                        inset 0 0 12px rgba(255, 255, 255, 0.01);
        }
        
        /* Glassmorphic Icon Frame */
        .premium-skill-icon-frame {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            transition: all 0.4s ease;
        }
        .premium-skill-card:hover .premium-skill-icon-frame {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--skill-color);
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.05);
        }
        
        .skill-icon-img {
            width: 26px;
            height: 26px;
            object-fit: contain;
            transition: transform 0.4s ease;
        }
        .skill-icon-fa {
            font-size: 1.5rem;
            color: var(--text-secondary);
            transition: all 0.4s ease;
        }
        .premium-skill-card:hover .skill-icon-fa {
            color: var(--skill-color);
            text-shadow: 0 0 10px var(--skill-color);
        }
        
        .premium-skill-name {
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: -0.2px;
        }
        
        .premium-skill-percent {
            font-size: 0.85rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        
        /* Premium Neon Progress Bar and loading micro-animations */
        .premium-progress-track {
            height: 6px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            overflow: hidden;
            margin-top: 14px;
        }
        
        .premium-progress-fill {
            height: 100%;
            background: var(--skill-color);
            border-radius: 20px;
            box-shadow: 0 0 12px var(--skill-color);
            animation: skillLoad 1.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            width: 0%;
        }
        
        @keyframes skillLoad {
            from { width: 0%; }
            to { width: var(--target-width); }
        }
        
        /* Adaptive Light Mode Tokens */
        [data-theme="light"] .premium-skill-card {
            background: rgba(0, 0, 0, 0.02);
            border-color: rgba(0, 0, 0, 0.06);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }
        [data-theme="light"] .premium-skill-card:hover {
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
        }
        [data-theme="light"] .premium-skill-icon-frame {
            background: rgba(0, 0, 0, 0.02);
            border-color: rgba(0, 0, 0, 0.06);
        }
        [data-theme="light"] .premium-progress-track {
            background: rgba(0, 0, 0, 0.05);
        }
        [data-theme="light"] .text-white-target {
            color: #0f172a !important;
        }
    </style>

<?php include 'includes/footer.php'; ?>
