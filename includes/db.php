<?php
// Smart Environment Detection: Auto-switch between local XAMPP and InfinityFree Production Server
$is_localhost = isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1' || in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));

if ($is_localhost) {
    // Local Development Settings
    $host = 'localhost';
    $dbname = 'garu_portfolio';
    $username = 'root';
    $password = '';
} else {
    // Live InfinityFree Production Server Settings
    $host = 'sql101.infinityfree.com';
    $dbname = 'if0_41953950_garu_portfolio';
    $username = 'if0_41953950';
    $password = 'pKMUmOQERdfInZ';
}

// Global base URL path helper to keep assets and routing functional in local vs live server root directories
$base_url = $is_localhost ? '/garu' : '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Automatically perform database schema migration for portfolio ratings
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS ratings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL,
            rating INT NOT NULL,
            comment TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Zero-Effort Auto-Migration: Ensure projects table has all 5 modern custom fields automatically
    try {
        $cols = $pdo->query("SHOW COLUMNS FROM projects")->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($cols)) {
            if (!in_array('link', $cols)) {
                $pdo->exec("ALTER TABLE projects ADD COLUMN link VARCHAR(255) DEFAULT NULL");
            }
            if (!in_array('client', $cols)) {
                $pdo->exec("ALTER TABLE projects ADD COLUMN client VARCHAR(255) DEFAULT NULL");
            }
            if (!in_array('project_date', $cols)) {
                $pdo->exec("ALTER TABLE projects ADD COLUMN project_date VARCHAR(255) DEFAULT NULL");
            }
            if (!in_array('main_image', $cols)) {
                $pdo->exec("ALTER TABLE projects ADD COLUMN main_image VARCHAR(255) DEFAULT NULL");
            }
            if (!in_array('additional_images', $cols)) {
                $pdo->exec("ALTER TABLE projects ADD COLUMN additional_images TEXT DEFAULT NULL");
            }
        }
    } catch (PDOException $ex) {
        // Safe fallback in case table doesn't exist yet
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper function to fetch settings easily
function getSetting(PDO $pdo, string $key): string {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    return $stmt->fetchColumn() ?: '';
}

// Brevo Transactional Email Sender Helper
function sendBrevoEmail(string $toEmail, string $toName, string $subject, string $htmlContent, ?PDO $pdo = null): bool {
    $apiKey = 'YOUR_BREVO_API_KEY_HERE'; // Redacted for security
    
    // Default Fallbacks
    $senderEmail = 'tangarochristianrey@gmail.com';
    $senderName = 'Christian Rey M. Tangaro';
    
    if ($pdo) {
        $dbEmail = getSetting($pdo, 'email');
        $dbName = getSetting($pdo, 'full_name');
        if (!empty($dbEmail)) $senderEmail = $dbEmail;
        if (!empty($dbName)) $senderName = $dbName;
    }

    $url = 'https://api.brevo.com/v3/smtp/email';
    
    $payload = [
        'sender' => [
            'name' => $senderName,
            'email' => $senderEmail
        ],
        'to' => [
            [
                'email' => $toEmail,
                'name' => $toName
            ]
        ],
        'subject' => $subject,
        'htmlContent' => $htmlContent
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'api-key: ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    // Disable SSL verification issues if locally hosted on XAMPP without root certs
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode >= 200 && $httpCode < 300;
}
?>
