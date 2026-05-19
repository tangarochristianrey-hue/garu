<?php
require_once __DIR__ . '/includes/db.php';

// Find the latest resume file dynamically, same as the original logic
$resume_dir = __DIR__ . '/assets/resume/';
$resume_file_url = $base_url . '/assets/resume/Tangaro_CV.pdf'; // fallback
if (is_dir($resume_dir)) {
    $files = glob($resume_dir . '*.pdf');
    if (!empty($files)) {
        $resume_file_url = $base_url . '/assets/resume/' . basename($files[0]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Christian Rey M. Tangaro | Resume / CV</title>
    <!-- Add the Favicon! -->
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($base_url) ?>/assets/images/logo.png">
    
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            background-color: #323639; /* Seamless dark background */
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>
    <iframe src="<?= htmlspecialchars($resume_file_url) ?>" title="Christian Rey M. Tangaro - Resume">
        This browser does not support inline PDFs. Please download the PDF to view it: <a href="<?= htmlspecialchars($resume_file_url) ?>">Download PDF</a>
    </iframe>
</body>
</html>
