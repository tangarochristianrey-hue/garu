<?php
// Handle CSV export request BEFORE any HTML output
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    include '../includes/db.php';
    
    // Clean output buffer to prevent corrupted downloads
    if (ob_get_length()) ob_end_clean();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=garu_portfolio_analytics_report_' . date('Ymd') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // Header
    fputcsv($output, ['Christian Rey M. Tangaro - Portfolio System Analytics Report']);
    fputcsv($output, ['Generated Date', date('Y-m-d H:i:s')]);
    fputcsv($output, []);
    
    // 1. General Metrics
    fputcsv($output, ['--- SUMMARY METRICS ---']);
    $total_views = $pdo->query("SELECT COUNT(*) FROM analytics")->fetchColumn();
    $messages_count = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
    $skills_count = $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn();
    $projects_count = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    $reviews_count = $pdo->query("SELECT COUNT(*) FROM ratings")->fetchColumn();
    
    fputcsv($output, ['Metric Name', 'Count/Value']);
    fputcsv($output, ['Total Page Views', $total_views]);
    fputcsv($output, ['Total Contact Inquiries Received', $messages_count]);
    fputcsv($output, ['Managed Technical Skills', $skills_count]);
    fputcsv($output, ['Showcased Projects', $projects_count]);
    fputcsv($output, ['Community Reviews & Ratings', $reviews_count]);
    fputcsv($output, []);
    
    // 2. Traffic Trends (Last 7 Days)
    fputcsv($output, ['--- DAILY TRAFFIC TRENDS (LAST 7 DAYS) ---']);
    fputcsv($output, ['Date', 'Day', 'Views']);
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $day_name = date('l', strtotime($date));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM analytics WHERE visit_date = ?");
        $stmt->execute([$date]);
        $views = $stmt->fetchColumn();
        fputcsv($output, [$date, $day_name, $views]);
    }
    fputcsv($output, []);
    
    // 3. Contact Messages log
    fputcsv($output, ['--- RECENT CONTACT INQUIRIES LOG ---']);
    fputcsv($output, ['ID', 'Sender Name', 'Sender Email', 'Subject', 'Message Body', 'Sent Date', 'Read Status']);
    $messages = $pdo->query("SELECT id, name, email, subject, message, created_at, is_read FROM messages ORDER BY id DESC")->fetchAll();
    foreach($messages as $msg) {
        fputcsv($output, [
            $msg['id'],
            $msg['name'],
            $msg['email'],
            $msg['subject'],
            $msg['message'],
            $msg['created_at'],
            $msg['is_read'] ? 'Read' : 'Unread'
        ]);
    }
    
    fclose($output);
    exit;
}

include 'includes/header.php'; 
$base_url = $base_url ?? '';

// Fetch Total Views
$total_views = $pdo->query("SELECT COUNT(*) FROM analytics")->fetchColumn();

// Fetch Messages Counts
$messages_count = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$unread_messages_count = $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();

// Fetch Reviews Counts
$reviews_count = $pdo->query("SELECT COUNT(*) FROM ratings")->fetchColumn();
$avg_rating = $pdo->query("SELECT AVG(rating) FROM ratings")->fetchColumn();
$avg_rating = $avg_rating ? number_format($avg_rating, 1) : '0.0';

// Fetch last 7 days for the chart
$chart_data = [];
$max_views = 0;
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_name = date('D', strtotime($date));
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM analytics WHERE visit_date = ?");
    $stmt->execute([$date]);
    $views = $stmt->fetchColumn();
    $chart_data[] = ['day' => $day_name, 'views' => $views, 'date' => $date];
    if ($views > $max_views) $max_views = $views;
}
if ($max_views == 0) $max_views = 1; // Prevent division by zero
?>

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="page-title">Dashboard & Analytics</h1>
            <p class="page-subtitle mb-0">Real-time metrics and system overview.</p>
        </div>
        <div>
            <span class="btn-outline-custom text-success border-success" style="pointer-events:none;">
                <i class="fa-solid fa-circle text-success me-2" style="font-size:0.5rem;"></i> System Online
            </span>
        </div>
    </div>
    
    <!-- Analytics Metric Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card-custom h-100 position-relative overflow-hidden">
                <div style="position: absolute; top: -20px; right: -20px; color: #111; font-size: 6rem; z-index: 0;"><i class="fa-solid fa-eye"></i></div>
                <div style="position: relative; z-index: 1;">
                    <p class="text-muted-custom text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Total Page Views</p>
                    <h3 class="fw-bold mb-2" style="font-size: 2.2rem; color: #fff;"><?= number_format($total_views) ?></h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-success" style="font-size: 0.8rem;"><i class="fa-solid fa-circle-check"></i> Live Tracking</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card-custom h-100 position-relative overflow-hidden">
                <div style="position: absolute; top: -20px; right: -20px; color: #111; font-size: 6rem; z-index: 0;"><i class="fa-solid fa-code"></i></div>
                <div style="position: relative; z-index: 1;">
                    <p class="text-muted-custom text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Managed Skills</p>
                    <?php 
                    $sc = $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn(); 
                    $avg_percentage = $pdo->query("SELECT AVG(percentage) FROM skills")->fetchColumn();
                    $avg_percentage = $avg_percentage ? round($avg_percentage) : 0;
                    ?>
                    <h3 class="fw-bold mb-2" style="font-size: 2.2rem; color: #fff;"><?= $sc ?></h3>
                    <div class="progress mt-3" style="height: 4px; background: #222; border-radius: 4px; overflow: hidden;" title="Average Skill Proficiency: <?= $avg_percentage ?>%">
                        <div class="progress-bar" style="width: <?= $avg_percentage ?>%; background: linear-gradient(90deg, #10b981, #3b82f6); box-shadow: 0 0 8px #10b981; transition: width 1s ease;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card-custom h-100 position-relative overflow-hidden">
                <div style="position: absolute; top: -20px; right: -20px; color: #111; font-size: 6rem; z-index: 0;"><i class="fa-solid fa-folder"></i></div>
                <div style="position: relative; z-index: 1;">
                    <p class="text-muted-custom text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Active Projects</p>
                    <?php 
                    $pc = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(); 
                    $project_ratio = min(($pc / 5) * 100, 100);
                    ?>
                    <h3 class="fw-bold mb-2" style="font-size: 2.2rem; color: #fff;"><?= $pc ?></h3>
                    <div class="progress mt-3" style="height: 4px; background: #222; border-radius: 4px; overflow: hidden;" title="Completion Target Ratio (Target: 5 projects)">
                        <div class="progress-bar" style="width: <?= $project_ratio ?>%; background: linear-gradient(90deg, #3b82f6, #8b5cf6); box-shadow: 0 0 8px #3b82f6; transition: width 1s ease;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-6 col-sm-6">
            <div class="card-custom h-100 position-relative overflow-hidden">
                <div style="position: absolute; top: -20px; right: -20px; color: #111; font-size: 6rem; z-index: 0;"><i class="fa-solid fa-envelope"></i></div>
                <div style="position: relative; z-index: 1;">
                    <p class="text-muted-custom text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Inbox Messages</p>
                    <h3 class="fw-bold mb-2" style="font-size: 2.2rem; color: #fff;"><?= $messages_count ?></h3>
                    <?php if($unread_messages_count > 0): ?>
                        <span class="text-danger" style="font-size: 0.8rem; font-weight: 600;"><i class="fa-solid fa-circle-exclamation"></i> <?= $unread_messages_count ?> UNREAD</span>
                    <?php else: ?>
                        <span class="text-success" style="font-size: 0.8rem;"><i class="fa-solid fa-circle-check"></i> Inbox all read</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-6 col-sm-6">
            <div class="card-custom h-100 position-relative overflow-hidden">
                <div style="position: absolute; top: -20px; right: -20px; color: #111; font-size: 6rem; z-index: 0;"><i class="fa-solid fa-star"></i></div>
                <div style="position: relative; z-index: 1;">
                    <p class="text-muted-custom text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">User Reviews</p>
                    <h3 class="fw-bold mb-2" style="font-size: 2.2rem; color: #fff;"><?= $reviews_count ?></h3>
                    <span class="text-warning" style="font-size: 0.8rem; font-weight: 600;">
                        <i class="fa-solid fa-star text-warning"></i> <?= $avg_rating ?> Rating Average
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Profile Completion -->
        <div class="col-lg-8">
            <div class="card-custom h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold fs-6 text-white mb-0">System Activity & Engagement</h5>
                    <a href="?export=csv" class="btn btn-sm btn-outline-custom"><i class="fa-solid fa-download me-2"></i> Report</a>
                </div>
                
                <!-- Dynamic Analytics Chart -->
                <div class="d-flex align-items-end justify-content-between mt-5" style="height: 180px; padding-bottom: 20px; border-bottom: 1px solid #1a1a1a;">
                    <?php foreach($chart_data as $data): 
                        $height_percent = ($data['views'] / $max_views) * 100;
                        if($height_percent < 5 && $data['views'] > 0) $height_percent = 5;
                        if($data['views'] == 0) $height_percent = 2; // tiny blip for 0
                        $is_today = ($data['date'] == date('Y-m-d'));
                        
                        if ($is_today) {
                            $bg_style = 'background: linear-gradient(180deg, #10b981, rgba(16, 185, 129, 0.2)); border: 1px solid #10b981; box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);';
                        } else {
                            $bg_style = 'background: linear-gradient(180deg, #3b82f6, rgba(59, 130, 246, 0.15)); border: 1px solid rgba(59, 130, 246, 0.3);';
                        }
                    ?>
                    <div class="chart-col d-flex flex-column align-items-center justify-content-end" style="width: 8%; height: 100%; position: relative;">
                        <!-- Floating Views Tooltip -->
                        <div class="chart-tooltip mb-2 text-center" style="position: absolute; bottom: calc(<?= $height_percent ?>% + 15px); background: #0c0c0c; border: 1px solid #222; border-radius: 4px; padding: 4px 8px; color: #fff; font-size: 0.65rem; font-weight: 700; opacity: 0; pointer-events: none; transition: all 0.2s ease; white-space: nowrap; box-shadow: 0 4px 10px rgba(0,0,0,0.5);">
                            <?= $data['views'] ?> views
                        </div>
                        <!-- Sleek slender bar -->
                        <div style="width: 24px; <?= $bg_style ?> height: <?= $height_percent ?>%; border-radius: 6px 6px 0 0; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer;" 
                             onmouseover="showTooltip(this)" 
                             onmouseout="hideTooltip(this)"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="d-flex justify-content-between mt-2 text-muted-custom" style="font-size: 0.75rem;">
                    <?php foreach($chart_data as $data): ?>
                        <span style="width: 8%; text-align: center; <?= $data['date'] == date('Y-m-d') ? 'color: #fff; font-weight: bold;' : '' ?>"><?= $data['day'] ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <script>
        function showTooltip(el) {
            el.style.transform = 'scaleY(1.05)';
            el.style.filter = 'brightness(1.2)';
            var tooltip = el.parentElement.querySelector('.chart-tooltip');
            if (tooltip) {
                tooltip.style.opacity = '1';
                tooltip.style.transform = 'translateY(-5px)';
            }
        }
        function hideTooltip(el) {
            el.style.transform = 'none';
            el.style.filter = 'none';
            var tooltip = el.parentElement.querySelector('.chart-tooltip');
            if (tooltip) {
                tooltip.style.opacity = '0';
                tooltip.style.transform = 'none';
            }
        }
        </script>
        
        <div class="col-lg-4">
            <div class="card-custom h-100">
                <h5 class="fw-bold fs-6 text-white mb-4">Quick Actions</h5>
                
                <a href="settings" class="d-block text-decoration-none mb-3 p-3" style="background: #111; border: 1px solid #222; border-radius: 8px; transition: 0.2s;" onmouseover="this.style.borderColor='#fff'" onmouseout="this.style.borderColor='#222'">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-dark p-2 rounded"><i class="fa-solid fa-user-pen text-white"></i></div>
                        <div>
                            <h6 class="text-white mb-1 fs-6">Update Profile</h6>
                            <p class="text-muted-custom mb-0" style="font-size: 0.75rem;">Change hero text and info</p>
                        </div>
                    </div>
                </a>
                
                <a href="projects" class="d-block text-decoration-none mb-3 p-3" style="background: #111; border: 1px solid #222; border-radius: 8px; transition: 0.2s;" onmouseover="this.style.borderColor='#fff'" onmouseout="this.style.borderColor='#222'">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-dark p-2 rounded"><i class="fa-solid fa-plus text-white"></i></div>
                        <div>
                            <h6 class="text-white mb-1 fs-6">Add Project</h6>
                            <p class="text-muted-custom mb-0" style="font-size: 0.75rem;">Upload new portfolio item</p>
                        </div>
                    </div>
                </a>
                
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
                <a href="<?= htmlspecialchars($resume_file) ?>" target="_blank" class="d-block text-decoration-none p-3" style="background: #111; border: 1px solid #222; border-radius: 8px; transition: 0.2s;" onmouseover="this.style.borderColor='#fff'" onmouseout="this.style.borderColor='#222'">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-dark p-2 rounded"><i class="fa-solid fa-file-pdf text-white"></i></div>
                        <div>
                            <h6 class="text-white mb-1 fs-6">View Resume</h6>
                            <p class="text-muted-custom mb-0" style="font-size: 0.75rem;">Check active PDF resume</p>
                        </div>
                    </div>
                </a>
            </div>
    </div>

    <!-- Recent Messages -->
    <div class="card-custom mt-5 p-0 overflow-hidden mb-5">
        <div class="d-flex justify-content-between align-items-center p-4 border-bottom border-secondary" style="background: rgba(255,255,255,0.01);">
            <h5 class="fw-bold fs-6 text-white mb-0"><i class="fa-solid fa-envelope me-2"></i> Recent Client Inquiries</h5>
            <a href="messages" class="btn btn-sm btn-outline-custom" style="padding: 6px 12px; font-size: 0.75rem;">View Inbox <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Sender</th>
                    <th>Subject & Message</th>
                    <th>Date</th>
                    <th style="width: 100px;" class="text-end">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $latest_inquiries = $pdo->query("SELECT * FROM messages ORDER BY id DESC LIMIT 3")->fetchAll();
                foreach($latest_inquiries as $msg): 
                ?>
                <tr style="<?= $msg['is_read'] == 0 ? 'background: rgba(255,255,255,0.01);' : '' ?>">
                    <td>
                        <div class="fw-bold text-white small"><?= htmlspecialchars($msg['name']) ?></div>
                        <div class="text-muted-custom" style="font-size: 0.75rem;"><?= htmlspecialchars($msg['email']) ?></div>
                    </td>
                    <td>
                        <div class="text-white small fw-semibold"><?= htmlspecialchars($msg['subject'] ?: '(No Subject)') ?></div>
                        <div class="text-muted-custom text-truncate" style="max-width: 500px; font-size: 0.75rem;"><?= htmlspecialchars($msg['message']) ?></div>
                    </td>
                    <td class="text-muted-custom small"><?= date('M d, Y', strtotime($msg['created_at'])) ?></td>
                    <td class="text-end">
                        <?php if($msg['is_read'] == 0): ?>
                            <span class="badge bg-danger" style="font-size: 0.65rem; border-radius: 4px;">NEW</span>
                        <?php else: ?>
                            <span class="badge bg-dark border border-secondary text-secondary" style="font-size: 0.65rem; border-radius: 4px;">READ</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($latest_inquiries) == 0): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted-custom py-4 small">No messages received yet.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include 'includes/footer.php'; ?>
