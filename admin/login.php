<?php
session_start();
if(isset($_SESSION['admin_logged_in'])) {
    header("Location: index");
    exit;
}
include '../includes/db.php';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['login_success_alert'] = true;
        header("Location: index");
        exit;
    } else {
        $error = "Access Denied! The username or password you entered is incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In | CRT Workspace</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #000; color: #fff; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-card { background: #0a0a0a; border: 1px solid #1a1a1a; padding: 48px; border-radius: 12px; width: 100%; max-width: 420px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
        .brand-title { font-size: 1.5rem; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 32px; text-align: center;}
        .form-control { background: #000; border: 1px solid #262626; color: #fff; padding: 12px 16px; font-size: 0.9rem; border-radius: 6px;}
        .form-control:focus { background: #000; color: #fff; border-color: #555; box-shadow: none; }
        .form-label { color: #888; font-size: 0.8rem; font-weight: 500; margin-bottom: 6px; }
        .btn-submit { background: #fff; color: #000; border: none; padding: 12px; border-radius: 6px; font-weight: 600; width: 100%; font-size: 0.9rem; transition: 0.2s;}
        .btn-submit:hover { background: #e5e5e5; }
        .alert-error { background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 10px; border-radius: 6px; font-size: 0.85rem; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-title">CRT <span style="color:#666; font-weight:400;">Workspace</span></div>
        
        <form method="POST">
            <div class="mb-4">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus>
            </div>
            <div class="mb-5">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn-submit">Sign In</button>
        </form>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if($error): ?>
    <script>
        Swal.fire({
            title: 'Sign In Failed',
            text: '<?= htmlspecialchars($error) ?>',
            icon: 'error',
            background: '#0a0a0a',
            color: '#fff',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Try Again'
        });
    </script>
    <?php endif; ?>

    <?php if(isset($_GET['logout'])): ?>
    <script>
        Swal.fire({
            title: 'Signed Out',
            text: 'You have been successfully logged out of CRT Workspace.',
            icon: 'success',
            background: '#0a0a0a',
            color: '#fff',
            confirmButtonColor: '#fff',
            confirmButtonText: '<span style="color:#000;font-weight:bold;">Done</span>'
        });
    </script>
    <?php endif; ?>
</body>
</html>
