<?php
$pdo = new PDO('mysql:host=localhost;dbname=garu_portfolio', 'root', '');
try {
    $pdo->exec("ALTER TABLE projects ADD COLUMN client VARCHAR(255) DEFAULT NULL");
    $pdo->exec("ALTER TABLE projects ADD COLUMN project_date VARCHAR(255) DEFAULT NULL");
    $pdo->exec("ALTER TABLE projects ADD COLUMN main_image VARCHAR(255) DEFAULT NULL");
    $pdo->exec("ALTER TABLE projects ADD COLUMN additional_images TEXT DEFAULT NULL");
    echo "Columns added successfully.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
