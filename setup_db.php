<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP has no password

try {
    // Connect without database to create it
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS garu_portfolio");
    $pdo->exec("USE garu_portfolio");

    // 1. Admin Users Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        )
    ");
    // Insert default admin (username: admin, password: admin123)
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO admin_users (username, password) VALUES ('admin', '$hash')");

    // 2. Settings Table (For dynamic text like Hero, About, Contact info)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            setting_key VARCHAR(50) PRIMARY KEY,
            setting_value TEXT
        )
    ");
    $settings = [
        'hero_title' => 'I build digital solutions that <span>make an impact.</span>',
        'hero_desc' => 'Dedicated and hardworking second-year Information Systems student passionate about developing personal skills and gaining hands-on experience through building efficient, secure applications.',
        'about_text' => 'I am an Information Systems student who wants to develop personal skills and gain more experience through hands-on learning. I have a strong desire to learn new things and I work toward personal and professional development.',
        'email' => 'tangarochristianrey@gmail.com',
        'phone' => '0956-610-4307',
        'location' => 'Panabo City, Davao del Norte<br>Philippines',
        'education_short' => 'BS in Information Systems',
        'education_school' => 'Davao del Norte State College<br>2026',
        'linkedin' => 'https://www.linkedin.com/in/christianrey-tangaro-30b902408/'
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($settings as $k => $v) {
        $stmt->execute([$k, $v]);
    }

    // 3. Skills Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS skills (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50),
            percentage INT,
            icon_class VARCHAR(50),
            color_class VARCHAR(50)
        )
    ");
    $pdo->exec("INSERT IGNORE INTO skills (id, name, percentage, icon_class, color_class) VALUES 
        (1, 'HTML', 90, 'fa-brands fa-html5', 'text-danger'),
        (2, 'CSS', 85, 'fa-brands fa-css3-alt', 'text-primary'),
        (3, 'JavaScript', 70, 'fa-brands fa-js', 'text-warning'),
        (4, 'PHP', 75, 'fa-brands fa-php', 'text-info'),
        (5, 'Bootstrap 5', 85, 'fa-brands fa-bootstrap', 'text-purple'),
        (6, 'Figma (UI/UX)', 80, 'fa-brands fa-figma', 'text-pink')
    ");

    // 4. Experience & Education Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS experience (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type ENUM('work', 'education'),
            title VARCHAR(100),
            subtitle VARCHAR(100),
            description TEXT
        )
    ");
    $pdo->exec("INSERT IGNORE INTO experience (id, type, title, subtitle, description) VALUES 
        (1, 'work', 'Service Crew', 'Sirok Dine & Chill | Dec 2024 - 2026', '<li>Assisted customers with orders and provided quality customer service.</li><li>Maintained cleanliness and organization of the dining area.</li><li>Worked efficiently with team members in a fast-paced environment.</li><li>Handled customer concerns professionally and respectfully.</li>'),
        (2, 'education', 'Tech-Voc Livelihood (Cookery)', 'Panabo City National High School | 2022 - 2024', '<i class=\"fa-solid fa-certificate me-1 text-white\"></i> Cookery Assessment for NCII'),
        (3, 'education', 'Junior High School', 'Maryknoll College of Panabo Inc. | 2018 - 2022', '')
    ");

    // 5. Projects Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100),
            description TEXT,
            icon_class VARCHAR(50),
            tags VARCHAR(255),
            client VARCHAR(255) DEFAULT NULL,
            project_date VARCHAR(255) DEFAULT NULL,
            main_image VARCHAR(255) DEFAULT NULL,
            additional_images TEXT DEFAULT NULL,
            link VARCHAR(255) DEFAULT NULL
        )
    ");
    $pdo->exec("INSERT IGNORE INTO projects (id, title, description, icon_class, tags, client, project_date, main_image, link) VALUES 
        (1, 'Information System', 'A web-based information management system built for streamlining academic data processes.', 'fa-solid fa-laptop-code', 'PHP,MySQL,Bootstrap 5', 'Davao del Norte State College', '2024', NULL, '#'),
        (2, 'Personal Portfolio', 'A dark-themed, glassmorphic personal portfolio website to showcase skills and past experience.', 'fa-solid fa-mobile-screen', 'HTML,CSS,JavaScript', 'Personal', '2026', NULL, '#'),
        (3, 'Dine & Chill POS', 'A conceptual point-of-sale interface designed in Figma and prototyped using web technologies.', 'fa-solid fa-utensils', 'UI/UX,Figma,Bootstrap', 'Sirok Dine & Chill', '2025', NULL, '#')
    ");

    // 6. Certificates Table
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
    $pdo->exec("INSERT IGNORE INTO certificates (id, title, issued_by, month, year, image, keywords, description) VALUES 
        (1, 'Certificate of Recognition', 'Davao del Norte State College', 'May', '2026', 'cert_recognition.png', 'Dean''s Lister, Academic Excellence', 'Awarded for exemplary academic performance.'),
        (2, 'Civic Welfare Training Services (CWTS)', 'Davao del Norte State College', 'June', '2025', 'cert_cwts.png', 'CWTS, NSTP, Community Service', 'Certificate of Completion for the Civic Welfare Training Service component of NSTP.')
    ");

    echo "SUCCESS: Database 'garu_portfolio' and all tables created with initial data!\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
