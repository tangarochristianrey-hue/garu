<?php
ob_start();
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login");
    exit;
}
include '../includes/db.php';
// Fallback guard for $base_url — always set by includes/db.php before this file
if (!isset($base_url)) {
    $is_localhost = isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1' || in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
    $base_url = $is_localhost ? '/garu' : '';
}
$current_page = basename($_SERVER['PHP_SELF'], ".php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Workspace | CRT</title>
    <link rel="icon" type="image/png" href="<?= $base_url ?>/assets/images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #000; color: #ededed; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        
        /* Sidebar Styling */
        .sidebar { background-color: #0a0a0a; border-right: 1px solid #1a1a1a; height: 100vh; position: fixed; width: 260px; left: 0; top: 0; z-index: 1000; }
        .sidebar-brand { font-size: 1.25rem; font-weight: 700; color: #fff; padding: 32px 24px; letter-spacing: -0.5px; display: block; text-decoration: none; }
        .nav-label { font-size: 0.7rem; color: #666; text-transform: uppercase; letter-spacing: 1px; margin: 20px 24px 10px; font-weight: 600; }
        .nav-link { color: #888; padding: 12px 24px; font-size: 0.85rem; font-weight: 500; transition: all 0.2s; display: flex; align-items: center; border-right: 2px solid transparent;}
        .nav-link i { width: 24px; color: #555; font-size: 1rem; transition: 0.2s; }
        .nav-link:hover, .nav-link.active { color: #fff; background-color: #141414; border-color: #fff; }
        .nav-link:hover i, .nav-link.active i { color: #fff; }
        .logout-link { color: #ef4444 !important; border-color: transparent !important; }
        .logout-link i { color: #ef4444 !important; }
        .logout-link:hover { background-color: rgba(239, 68, 68, 0.1) !important; border-color: #ef4444 !important;}
        
        /* Content Area */
        .content-area { margin-left: 260px; padding: 60px; min-height: 100vh; background-color: #000; }
        .page-title { font-size: 1.8rem; font-weight: 600; color: #fff; letter-spacing: -0.5px; margin-bottom: 8px;}
        .page-subtitle { color: #888; font-size: 0.9rem; margin-bottom: 40px;}
        
        /* Components */
        .card-custom { background: #0a0a0a; border: 1px solid #1f1f1f; border-radius: 8px; padding: 24px; }
        .btn-primary-custom { display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #fff; color: #000; border: none; padding: 10px 20px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; transition: 0.2s; box-shadow: 0 4px 12px rgba(255,255,255,0.1); }
        .btn-primary-custom:hover { background: #e5e5e5; transform: translateY(-1px); }
        .btn-outline-custom { display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: transparent; color: #fff; border: 1px solid #333; padding: 8px 16px; border-radius: 6px; font-size: 0.85rem; font-weight: 500; transition: 0.2s; }
        .btn-outline-custom:hover { background: #1a1a1a; border-color: #555; color: #fff; }
        
        /* Forms */
        .form-label { color: #888; font-size: 0.8rem; font-weight: 500; }
        .form-control, .form-select { background: #0a0a0a; border: 1px solid #262626; color: #fff; padding: 12px; border-radius: 6px; font-size: 0.9rem; }
        .form-control:focus, .form-select:focus { background: #0a0a0a; color: #fff; border-color: #555; box-shadow: none; }
        
        /* Tables */
        .table { margin-bottom: 0; color: #fff !important; }
        .table th { background: transparent; border-bottom: 1px solid #262626; color: #888; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 500; padding: 16px 20px; }
        .table td { background: transparent; border-bottom: 1px solid #1a1a1a; padding: 16px 20px; vertical-align: middle; border-top: none; color: #fff !important; }
        .table tbody tr:hover td { background-color: #111; }
        
        /* Utilities */
        .text-muted-custom { color: #888; }
        
        /* Brand Icon Color Utilities */
        .fa-html5, .fa-html5-alt { color: #e34c26 !important; }
        .fa-css3, .fa-css3-alt { color: #264de4 !important; }
        .fa-js, .fa-js-square, .fa-square-js { color: #f7df1e !important; }
        .fa-php { color: #777bb4 !important; }
        .fa-bootstrap { color: #7952b3 !important; }
        .fa-figma { color: #f24e1e !important; }
        .fa-file-signature, .fa-signature, .fa-pencil-alt { color: #38bdf8 !important; }

        /* Mobile Top Nav Styling */
        .mobile-top-bar { display: none; background: #0a0a0a; border-bottom: 1px solid #1a1a1a; padding: 16px 24px; position: fixed; width: 100%; top: 0; left: 0; z-index: 1001; align-items: center; justify-content: space-between; }
        .mobile-brand { font-size: 1.15rem; font-weight: 700; color: #fff; text-decoration: none; }
        .menu-toggle-btn { background: transparent; border: 1px solid #333; color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 1.1rem; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; }
        .menu-toggle-btn:hover { background: #111; border-color: #555; }
        
        /* Sidebar Overlay */
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 999; backdrop-filter: blur(4px); }

        /* Media Queries for Mobile responsiveness */
        @media (max-width: 991.98px) {
            .sidebar { left: -260px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 15px 0 30px rgba(0,0,0,0.5); z-index: 1002; }
            .sidebar.show { left: 0; }
            .sidebar.show ~ .sidebar-overlay { display: block; }
            .content-area { margin-left: 0; padding: 100px 20px 40px !important; }
            .mobile-top-bar { display: flex; }
        }
    </style>
</head>
<body>

    <!-- Mobile Top Navigation Bar -->
    <div class="mobile-top-bar">
        <a href="index" class="mobile-brand">CRT <span style="color:#666; font-weight:400; font-size:0.85rem;">Workspace</span></a>
        <button class="menu-toggle-btn" id="menuToggle"><i class="fa-solid fa-bars"></i></button>
    </div>
    
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Professional SaaS Sidebar -->
    <div class="sidebar">
        <a href="index" class="sidebar-brand">
            CRT <span style="color: #666; font-weight: 400; font-size: 0.9rem;">Workspace</span>
        </a>
        
        <div class="nav-label">Overview</div>
        <a href="index" class="nav-link <?= $current_page=='index'?'active':'' ?>"><i class="fa-solid fa-layer-group"></i> Dashboard</a>
        <a href="<?= $base_url ?>/" target="_blank" class="nav-link"><i class="fa-solid fa-arrow-up-right-from-square"></i> Live Website</a>
        
        <div class="nav-label mt-4">Content Manager</div>
        <a href="settings" class="nav-link <?= $current_page=='settings'?'active':'' ?>"><i class="fa-solid fa-sliders"></i> General Settings</a>
        <a href="skills" class="nav-link <?= $current_page=='skills'?'active':'' ?>"><i class="fa-solid fa-code"></i> Skills</a>
        <a href="experience" class="nav-link <?= $current_page=='experience'?'active':'' ?>"><i class="fa-solid fa-briefcase"></i> Experience</a>
        <a href="projects" class="nav-link <?= $current_page=='projects'?'active':'' ?>"><i class="fa-solid fa-folder"></i> Projects</a>
        <a href="reviews" class="nav-link <?= $current_page=='reviews'?'active':'' ?>"><i class="fa-solid fa-star"></i> Reviews</a>
        <?php
        $unread_stmt = $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0");
        $unread_count = $unread_stmt->fetchColumn();
        ?>
        <a href="messages" class="nav-link <?= $current_page=='messages'?'active':'' ?>"><i class="fa-solid fa-envelope"></i> Messages <?php if($unread_count > 0): ?><span class="badge bg-danger ms-auto" style="font-size: 0.7rem; padding: 4px 8px; border-radius: 50px;"><?= $unread_count ?></span><?php endif; ?></a>
        
        <div class="nav-label mt-4">System</div>
        <a href="logout" class="nav-link logout-link"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a>
    </div>
    
    <!-- Main Content Area -->
    <div class="content-area">
