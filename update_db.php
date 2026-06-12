<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=garu_portfolio", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS certificates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            issued_by VARCHAR(255),
            month VARCHAR(50),
            year VARCHAR(50),
            image VARCHAR(255) NOT NULL,
            keywords VARCHAR(255),
            description TEXT
        )
    ");
    echo "Certificates table created successfully.";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
