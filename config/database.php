<?php
// config/database.php - Database Configuration & Auto-Migration

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'digital_health_db');

function getDBConnection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        // Connect first without DB to ensure DB exists
        $dsnWithoutDb = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
        $initPdo = new PDO($dsnWithoutDb, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        $initPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // Connect to the specific database
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);

        // Auto initialize tables and initial data
        initializeDatabaseSchema($pdo);

        return $pdo;
    } catch (PDOException $e) {
        die("<div style='font-family:sans-serif;padding:20px;background:#ffebee;color:#c62828;border-radius:8px;'>
            <h3>ข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล (Database Connection Error)</h3>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
            <p>โปรดตรวจสอบว่าเปิดบริการ MySQL ในโปรแกรม XAMPP เรียบร้อยแล้ว</p>
        </div>");
    }
}

function initializeDatabaseSchema(PDO $pdo) {
    // 1. Users Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `fullname` VARCHAR(150) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `phone` VARCHAR(50) NULL,
        `role` ENUM('admin', 'staff', 'user') DEFAULT 'user',
        `two_factor_enabled` TINYINT(1) DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 2. Two-Factor Authentication Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `two_factor_auth` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `otp_code` VARCHAR(10) NOT NULL,
        `expires_at` DATETIME NOT NULL,
        `is_used` TINYINT(1) DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX(`user_id`),
        INDEX(`otp_code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 3. Hero Banners Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `hero_banners` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `subtitle` TEXT NULL,
        `badge_text` VARCHAR(100) DEFAULT 'ข่าวประชาสัมพันธ์',
        `button_text` VARCHAR(100) DEFAULT 'อ่านเพิ่มเติม',
        `button_link` VARCHAR(255) DEFAULT '#',
        `image_url` VARCHAR(255) NOT NULL,
        `sort_order` INT DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 4. News Categories
    $pdo->exec("CREATE TABLE IF NOT EXISTS `news_categories` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `slug` VARCHAR(100) NOT NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 5. News Articles
    $pdo->exec("CREATE TABLE IF NOT EXISTS `news` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `category_id` INT DEFAULT 1,
        `title` VARCHAR(255) NOT NULL,
        `excerpt` TEXT NULL,
        `content` LONGTEXT NOT NULL,
        `image_url` VARCHAR(255) NULL,
        `author` VARCHAR(100) DEFAULT 'ฝ่ายประชาสัมพันธ์สุขภาพดิจิทัล',
        `views` INT DEFAULT 0,
        `is_featured` TINYINT(1) DEFAULT 0,
        `published_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX(`category_id`),
        INDEX(`is_featured`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 6. Executives (ผังบริหารโครงสร้างองค์กร)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `executives` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(150) NOT NULL,
        `position` VARCHAR(150) NOT NULL,
        `department` VARCHAR(150) NULL,
        `level` INT DEFAULT 1 COMMENT '1=ผู้บริหารสูงสุด, 2=รองผู้อำนวยการ, 3=หัวหน้าฝ่าย/กลุ่มงาน',
        `image_url` VARCHAR(255) NULL,
        `email` VARCHAR(100) NULL,
        `phone` VARCHAR(50) NULL,
        `bio` TEXT NULL,
        `sort_order` INT DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 7. Digital Health Services (บริการดิจิทัล)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `services` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(150) NOT NULL,
        `description` TEXT NULL,
        `icon` VARCHAR(50) DEFAULT 'fa-solid fa-laptop-medical',
        `badge` VARCHAR(50) DEFAULT 'บริการออนไลน์',
        `link_url` VARCHAR(255) DEFAULT '#',
        `sort_order` INT DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 8. Contact Inquiries
    $pdo->exec("CREATE TABLE IF NOT EXISTS `contact_messages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `phone` VARCHAR(50) NULL,
        `subject` VARCHAR(200) NOT NULL,
        `message` TEXT NOT NULL,
        `status` ENUM('unread', 'read', 'replied') DEFAULT 'unread',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 9. System Settings
    $pdo->exec("CREATE TABLE IF NOT EXISTS `system_settings` (
        `setting_key` VARCHAR(50) PRIMARY KEY,
        `setting_value` TEXT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 10. Duty Schedules (ปฏิทินตารางเวรเจ้าหน้าที่)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `duty_schedules` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `staff_name` VARCHAR(150) NOT NULL,
        `staff_position` VARCHAR(150) NULL,
        `department` VARCHAR(150) NULL,
        `duty_date` DATE NOT NULL,
        `shift_type` ENUM('morning', 'afternoon', 'night', 'full_day', 'on_call') DEFAULT 'morning',
        `shift_name` VARCHAR(100) DEFAULT 'เวรเช้า (08:30 - 16:30 น.)',
        `phone` VARCHAR(50) NULL,
        `note` TEXT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX(`duty_date`),
        INDEX(`shift_type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Seed default data if empty
    seedInitialData($pdo);
}

function seedInitialData(PDO $pdo) {
    // 1. Seed Admin user if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `username` = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hashed = password_hash('admin1234', PASSWORD_DEFAULT);
        $insertAdmin = $pdo->prepare("INSERT INTO `users` (`username`, `password`, `fullname`, `email`, `phone`, `role`, `two_factor_enabled`) 
            VALUES ('admin', :pwd, 'ผู้ดูแลระบบสุขภาพดิจิทัล', 'Khthawuth.leo@gmail.com', '02-590-3000', 'admin', 1)");
        $insertAdmin->execute([':pwd' => $hashed]);
    }

    // 2. Seed System Settings
    $settings = [
        'site_name' => 'ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล (Digital Health Innovation Center)',
        'site_short_name' => 'สุขภาพดิจิทัล',
        'site_tagline' => 'ยกระดับบริการสุขภาพด้วยเทคโนโลยีและนวัตกรรมดิจิทัลเพื่อประชาชนไทย',
        'admin_2fa_email' => 'Khthawuth.leo@gmail.com',
        'contact_email' => 'Khthawuth.leo@gmail.com',
        'contact_phone' => '02-590-3000 หรือสายด่วน 1422',
        'contact_address' => 'อาคารศูนย์สุขภาพดิจิทัลและเทคโนโลยีการแพทย์ กระทรวงสาธารณสุข ถนนติวานนท์ อำเภอเมือง จังหวัดนนทบุรี 11000',
        'working_hours' => 'จันทร์ - ศุกร์: 08:30 - 16:30 น. (ยกเว้นวันหยุดราชการ)'
    ];
    $stmtCheckSetting = $pdo->prepare("SELECT COUNT(*) FROM `system_settings` WHERE `setting_key` = :k");
    $stmtInsertSetting = $pdo->prepare("INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES (:k, :v)");
    foreach ($settings as $k => $v) {
        $stmtCheckSetting->execute([':k' => $k]);
        if ($stmtCheckSetting->fetchColumn() == 0) {
            $stmtInsertSetting->execute([':k' => $k, ':v' => $v]);
        }
    }

    // 3. Seed Hero Banners
    $stmt = $pdo->query("SELECT COUNT(*) FROM `hero_banners`");
    if ($stmt->fetchColumn() == 0) {
        $banners = [
            [
                'title' => 'ก้าวสู่มิติใหม่ "สุขภาพดิจิทัล" เชื่อมโยงข้อมูลเพื่อสุขภาวะคนไทย',
                'subtitle' => 'ศูนย์กลางบริการสาธารณสุขดิจิทัล เชื่อมโยงเวชระเบียนอิเล็กทรอนิกส์ การแพทย์ทางไกล (Telemedicine) และระบบ AI วินิจฉัยเบื้องต้นที่ปลอดภัยระดับสากล',
                'badge_text' => 'นวัตกรรมเด่น 2026',
                'button_text' => 'สำรวจบริการดิจิทัล',
                'button_link' => 'services.php',
                'image_url' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1600&q=80',
                'sort_order' => 1
            ],
            [
                'title' => 'ระบบสมุดสุขภาพประชาชนดิจิทัล (Digital Health ID)',
                'subtitle' => 'บัตรประชาชนใบเดียวรักษาได้ทุกที่ เข้าถึงประวัติการรักษา ผลตรวจสุขภาพ และนัดหมายแพทย์ล่วงหน้าแบบเรียลไทม์ผ่านสมาร์ทโฟน',
                'badge_text' => 'เปิดตัวระบบใหม่',
                'button_text' => 'ลงทะเบียนรับสิทธิ์',
                'button_link' => 'register.php',
                'image_url' => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&w=1600&q=80',
                'sort_order' => 2
            ],
            [
                'title' => 'บริการแพทย์ทางไกล Telemedicine ปรึกษาหมอผู้เชี่ยวชาญจากบ้าน',
                'subtitle' => 'ลดความแออัดในโรงพยาบาล เพิ่มการเข้าถึงแพทย์เฉพาะทางอย่างเท่าเทียม พร้อมบริการส่งยาถึงบ้านอย่างรวดเร็วและปลอดภัย',
                'badge_text' => 'บริการประชาชน',
                'button_text' => 'เริ่มต้นใช้งาน',
                'button_link' => 'services.php#telemed',
                'image_url' => 'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?auto=format&fit=crop&w=1600&q=80',
                'sort_order' => 3
            ]
        ];
        $insertBanner = $pdo->prepare("INSERT INTO `hero_banners` (`title`, `subtitle`, `badge_text`, `button_text`, `button_link`, `image_url`, `sort_order`, `is_active`) 
            VALUES (:title, :subtitle, :badge_text, :button_text, :button_link, :image_url, :sort_order, 1)");
        foreach ($banners as $b) {
            $insertBanner->execute($b);
        }
    }

    // 4. Seed Categories
    $stmt = $pdo->query("SELECT COUNT(*) FROM `news_categories`");
    if ($stmt->fetchColumn() == 0) {
        $cats = [
            ['name' => 'ข่าวประชาสัมพันธ์ทั่วไป', 'slug' => 'general-pr'],
            ['name' => 'นวัตกรรมและเทคโนโลยีการแพทย์', 'slug' => 'health-tech'],
            ['name' => 'นโยบายและความปลอดภัยไซเบอร์', 'slug' => 'policy-security'],
            ['name' => 'กิจกรรมและการฝึกอบรมบุคลากร', 'slug' => 'events-training'],
            ['name' => 'ประกาศและจัดซื้อจัดจ้าง', 'slug' => 'procurement']
        ];
        $insertCat = $pdo->prepare("INSERT INTO `news_categories` (`name`, `slug`) VALUES (:name, :slug)");
        foreach ($cats as $c) {
            $insertCat->execute($c);
        }
    }

    // 5. Seed News
    $stmt = $pdo->query("SELECT COUNT(*) FROM `news`");
    if ($stmt->fetchColumn() == 0) {
        $newsList = [
            [
                'category_id' => 1,
                'title' => 'ศูนย์สุขภาพดิจิทัลจับมือโรงพยาบาลทั่วประเทศ เปิดใช้ระบบเชื่อมโยงประวัติสุขภาพแบบไร้รอยต่อ',
                'excerpt' => 'กระทรวงสาธารณสุขร่วมกับศูนย์สุขภาพดิจิทัล เปิดตัวโครงการเชื่อมโยงข้อมูลสุขภาพดิจิทัลทั่วประเทศ ยกระดับการส่งต่อผู้ป่วยและลดความซ้ำซ้อนในการตรวจทางห้องปฏิบัติการ',
                'content' => '<p>กระทรวงสาธารณสุขร่วมกับศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล จัดพิธีลงนามบันทึกข้อตกลงความร่วมมือ (MOU) ยกระดับระบบสุขภาพสู่ระบบ "สุขภาพดิจิทัลเต็มรูปแบบ" มุ่งเน้นการเชื่อมโยงระบบเวชระเบียนอิเล็กทรอนิกส์ (Electronic Health Record - EHR) ข้ามสถานพยาบาลแบบเรียลไทม์</p>
                <p>การพัฒนาระบบในครั้งนี้ช่วยให้แพทย์สามารถเรียกดูประวัติการแพ้ยา ประวัติการผ่าตัด และผลแล็บย้อนหลังได้ทันทีเมื่อผู้ป่วยเข้ารับการรักษาฉุกเฉินต่างพื้นที่ ภายใต้การควบคุมสิทธิความเป็นส่วนตัวตามมาตรฐาน PDPA</p>
                <h4>เป้าหมายสำคัญของโครงการ:</h4>
                <ul>
                    <li>ครอบคลุมโรงพยาบาลศูนย์และโรงพยาบาลทั่วไป 100% ภายในปี 2569</li>
                    <li>รองรับการยืนยันตัวตนด้วย Digital Health ID สะดวกรวดเร็วและปลอดภัย</li>
                    <li>ลดระยะเวลารอคอยในโรงพยาบาลเฉลี่ยมากกว่า 45%</li>
                </ul>',
                'image_url' => 'https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&w=1200&q=80',
                'is_featured' => 1,
                'views' => 1420
            ],
            [
                'category_id' => 2,
                'title' => 'ยกระดับบริการ Telemedicine ชุมชนห่างไกล เข้าถึงแพทย์เฉพาะทางไม่ต้องเดินทางข้ามจังหวัด',
                'excerpt' => 'เปิดศูนย์แพทย์ทางไกลชุมชน นำเทคโนโลยีจอสื่อสารความละเอียดสูงและอุปกรณ์ตรวจร่างกายระบบคลาวด์ เสริมศักยภาพ รพ.สต. ทั่วประเทศ',
                'content' => '<p>โครงการขยายจุดบริการ Telemedicine สู่โรงพยาบาลส่งเสริมสุขภาพตำบล (รพ.สต.) ช่วยให้ผู้ป่วยโรคไม่ติดต่อเรื้อรัง (NCDs) เช่น เบาหวาน ความดันโลหิตสูง และโรคหัวใจ สามารถพบแพทย์ผู้เชี่ยวชาญจากโรงพยาบาลศูนย์ได้โดยตรงผ่านระบบวิดีโอคอลความเร็วสูง</p>
                <p>โดยมีเจ้าหน้าที่สาธารณสุขประจำพื้นที่คอยดูแลวัดสัญญาณชีพ และส่งข้อมูลขึ้นระบบคลาวด์เพื่อการวินิจฉัยที่แม่นยำ พร้อมทั้งมีระบบบริการจัดส่งยาถึงบ้านภายใน 24-48 ชั่วโมง</p>',
                'image_url' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80',
                'is_featured' => 1,
                'views' => 980
            ],
            [
                'category_id' => 3,
                'title' => 'ประกาศแนวปฏิบัติความมั่นคงปลอดภัยไซเบอร์ด้านสาธารณสุข (Health Cybersecurity Framework 2026)',
                'excerpt' => 'กำหนดกรอบมาตรฐานความปลอดภัยข้อมูลสุขภาพ การป้องกันการโจมตีแบบ Ransomware และการเข้ารหัสข้อมูลเวชระเบียนขั้นสูง',
                'content' => '<p>เพื่อสร้างความมั่นใจสูงสุดให้แก่ประชาชนและบุคลากรทางการแพทย์ ศูนย์สุขภาพดิจิทัลได้ออกประกาศข้อกำหนดและแนวทางปฏิบัติทางด้านความมั่นคงปลอดภัยทางไซเบอร์สำหรับสถานพยาบาลทุกระดับ</p>
                <p>มาตรการสำคัญประกอบด้วยการตรวจสอบสิทธิ์การเข้าถึงแบบ Multi-Factor Authentication (MFA), การสำรองข้อมูลแบบแยกระบบ (Immutable Backup) และการจัดตั้งศูนย์เฝ้าระวังภัยคุกคามไซเบอร์ด้านสาธารณสุขตลอด 24 ชั่วโมง</p>',
                'image_url' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=1200&q=80',
                'is_featured' => 1,
                'views' => 745
            ],
            [
                'category_id' => 4,
                'title' => 'เปิดรับสมัครอบรมหลักสูตร "ปัญญาประดิษฐ์ทางการแพทย์และเทคโนโลยีสุขภาพดิจิทัลสำหรับบุคลากร"',
                'excerpt' => 'ขอเชิญแพทย์ พยาบาล นักวิชาการคอมพิวเตอร์ และบุคลากรสาธารณสุข เข้าร่วมอบรมหลักสูตรเสริมทักษะ AI and Data Analytics in Healthcare ฟรีไม่มีค่าใช้จ่าย',
                'content' => '<p>ศูนย์สุขภาพดิจิทัลขอเชิญบุคลากรทางการแพทย์และสาธารณสุขเข้าร่วมการอบรมเชิงปฏิบัติการ เพื่อเสริมสร้างความรู้ความเข้าใจเกี่ยวกับการนำ AI มาช่วยคัดกรองโรคเบื้องต้น การบริหารจัดการคลังข้อมูลสุขภาพ (Health Big Data) และการใช้งานระบบ AI Assistant ในคลินิก</p>',
                'image_url' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1200&q=80',
                'is_featured' => 0,
                'views' => 610
            ],
            [
                'category_id' => 1,
                'title' => 'แอปพลิเคชัน "Smart Health Pass" อัปเดตฟังก์ชันใหม่ แจ้งเตือนฉีดวัคซีนและผลตรวจแล็บอัตโนมัติ',
                'excerpt' => 'เพิ่มความสะดวกแก่ประชาชนในการติดตามสุขภาพของตนเองและคนในครอบครัว มีระบบสรุปผลตรวจสุขภาพรายปีในรูปแบบ Infographic เข้าใจง่าย',
                'content' => '<p>เวอร์ชันล่าสุดของแอปพลิเคชัน Smart Health Pass ได้เพิ่มฟีเจอร์การแจ้งเตือนรอบนัดหมายแพทย์ การคำนวณดัชนีสุขภาพส่วนบุคคล และการเชื่อมต่อกับสมาร์ทวอทช์เพื่อบันทึกก้าวเดินและอัตราการเต้นของหัวใจแบบอัตโนมัติ</p>',
                'image_url' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=1200&q=80',
                'is_featured' => 0,
                'views' => 520
            ],
            [
                'category_id' => 5,
                'title' => 'ประกาศประกวดราคาซื้อระบบจัดเก็บและวิเคราะห์ภาพถ่ายทางการแพทย์ผ่านคลาวด์ (Cloud PACS) ด้วยวิธี e-Bidding',
                'excerpt' => 'ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล มีความประสงค์จะประกวดราคาซื้อระบบจัดเก็บและรับส่งภาพทางการแพทย์ดิจิทัล เพื่อรองรับโรงพยาบาลเครือข่าย',
                'content' => '<p>เอกสารประกวดราคาและข้อกำหนดขอบเขตงาน (TOR) สามารถดาวน์โหลดได้ทางเว็บไซต์ระบบการจัดซื้อจัดจ้างภาครัฐ หรือติดต่อสอบถามรายละเอียดได้ที่กลุ่มงานพัสดุและจัดซื้อจัดจ้าง</p>',
                'image_url' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1200&q=80',
                'is_featured' => 0,
                'views' => 312
            ]
        ];

        $insertNews = $pdo->prepare("INSERT INTO `news` (`category_id`, `title`, `excerpt`, `content`, `image_url`, `is_featured`, `views`) 
            VALUES (:category_id, :title, :excerpt, :content, :image_url, :is_featured, :views)");
        foreach ($newsList as $n) {
            $insertNews->execute($n);
        }
    }

    // 6. Seed Executives (ผังบริหารโครงสร้างองค์กร 3 ระดับ)
    $stmt = $pdo->query("SELECT COUNT(*) FROM `executives`");
    if ($stmt->fetchColumn() == 0) {
        $executives = [
            // Level 1: ผู้อำนวยการสูงสุด
            [
                'name' => 'นพ. เกรียงศักดิ์ วิจิตรสวัสดิ์',
                'position' => 'ผู้อำนวยการศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล',
                'department' => 'สำนักบริหารยุทธศาสตร์สุขภาพดิจิทัล',
                'level' => 1,
                'image_url' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=600&q=80',
                'email' => 'kriangsak.w@digitalhealth.moph.go.th',
                'phone' => '02-590-3101',
                'bio' => 'ผู้เชี่ยวชาญด้านเวชศาสตร์สารสนเทศ (Medical Informatics) ประสบการณ์พัฒนาระบบสาธารณสุขดิจิทัลกว่า 20 ปี',
                'sort_order' => 1
            ],
            // Level 2: รองผู้อำนวยการ
            [
                'name' => 'พญ. นภัสสร วงศ์ประเสริฐ',
                'position' => 'รองผู้อำนวยการฝ่ายพัฒนานวัตกรรมและเทคโนโลยีการแพทย์',
                'department' => 'กลุ่มงานนวัตกรรมการแพทย์และ Telemedicine',
                'level' => 2,
                'image_url' => 'https://images.unsplash.com/photo-1594824813583-b9b25a9526f8?auto=format&fit=crop&w=600&q=80',
                'email' => 'napassorn.w@digitalhealth.moph.go.th',
                'phone' => '02-590-3102',
                'bio' => 'ผู้นำโครงการระบบบริการการแพทย์ทางไกลและระบบปัญญาประดิษฐ์เพื่อช่วยคัดกรองผู้ป่วยระดับปฐมภูมิ',
                'sort_order' => 2
            ],
            [
                'name' => 'ดร. ธนวัฒน์ ศิริพงษ์พาณิชย์',
                'position' => 'รองผู้อำนวยการฝ่ายโครงสร้างพื้นฐานและสารสนเทศสุขภาพ',
                'department' => 'กลุ่มงานโครงสร้างพื้นฐานคลาวด์และไซเบอร์ซีเคียวริตี้',
                'level' => 2,
                'image_url' => 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&w=600&q=80',
                'email' => 'thanawat.s@digitalhealth.moph.go.th',
                'phone' => '02-590-3103',
                'bio' => 'ผู้เชี่ยวชาญด้าน Big Data Architecture, Cloud Computing และ Cybersecurity มาตรฐานสากล ISO/IEC 27001',
                'sort_order' => 3
            ],
            // Level 3: หัวหน้ากลุ่มงาน / หัวหน้าฝ่าย
            [
                'name' => 'นายปริญญา ชัยมงคล',
                'position' => 'หัวหน้ากลุ่มงานเทคโนโลยีสารสนเทศและระบบเวชระเบียน',
                'department' => 'กลุ่มงานระบบเวชระเบียนอิเล็กทรอนิกส์ (EHR)',
                'level' => 3,
                'image_url' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=600&q=80',
                'email' => 'parinya.c@digitalhealth.moph.go.th',
                'phone' => '02-590-3201',
                'bio' => 'รับผิดชอบระบบเชื่อมต่อฐานข้อมูลเวชระเบียนและการแลกเปลี่ยนข้อมูลตามมาตรฐาน HL7 FHIR',
                'sort_order' => 4
            ],
            [
                'name' => 'นางสาวรุจิรา พิริยะกุล',
                'position' => 'หัวหน้ากลุ่มงานความมั่นคงปลอดภัยสารสนเทศและคุ้มครองข้อมูล',
                'department' => 'กลุ่มงานไซเบอร์ซีเคียวริตี้และ PDPA สาธารณสุข',
                'level' => 3,
                'image_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
                'email' => 'rujira.p@digitalhealth.moph.go.th',
                'phone' => '02-590-3202',
                'bio' => 'ผู้รับผิดชอบการปฏิบัติตาม พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล (PDPA) และการตรวจประเมินความปลอดภัยระบบสุขภาพ',
                'sort_order' => 5
            ],
            [
                'name' => 'นายแพทย์อัศวิน จิตต์อำไพ',
                'position' => 'หัวหน้ากลุ่มงานส่งเสริมและวิจัยปัญญาประดิษฐ์ทางการแพทย์',
                'department' => 'กลุ่มงาน AI Healthcare Research & Analytics',
                'level' => 3,
                'image_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80',
                'email' => 'asawin.j@digitalhealth.moph.go.th',
                'phone' => '02-590-3203',
                'bio' => 'วิจัยและพัฒนาโมเดล AI ในการอ่านภาพรังสีทรวงอกและการประเมินความเสี่ยงโรคหัวใจและหลอดเลือด',
                'sort_order' => 6
            ],
            [
                'name' => 'นางสาวชนัญชิดา สิทธิโชค',
                'position' => 'หัวหน้ากลุ่มงานสื่อสารองค์กรและประชาสัมพันธ์สุขภาพดิจิทัล',
                'department' => 'กลุ่มงานสื่อสารและภาพลักษณ์องค์กร',
                'level' => 3,
                'image_url' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=600&q=80',
                'email' => 'chananchida.s@digitalhealth.moph.go.th',
                'phone' => '02-590-3204',
                'bio' => 'วางแผนกลยุทธ์การสื่อสารสาธารณะและเผยแพร่สาระความรู้ด้านสุขภาพดิจิทัลสู่ประชาชน',
                'sort_order' => 7
            ]
        ];

        $insertExec = $pdo->prepare("INSERT INTO `executives` (`name`, `position`, `department`, `level`, `image_url`, `email`, `phone`, `bio`, `sort_order`) 
            VALUES (:name, :position, :department, :level, :image_url, :email, :phone, :bio, :sort_order)");
        foreach ($executives as $ex) {
            $insertExec->execute($ex);
        }
    }

    // 7. Seed Services
    $stmt = $pdo->query("SELECT COUNT(*) FROM `services`");
    if ($stmt->fetchColumn() == 0) {
        $services = [
            [
                'title' => 'ระบบบัตรสุขภาพดิจิทัล (Digital Health ID)',
                'description' => 'ยืนยันตัวตนดิจิทัลเพื่อเข้าถึงประวัติการรักษา นัดหมายแพทย์ และขอรับยาได้ทั่วประเทศอย่างปลอดภัย',
                'icon' => 'fa-solid fa-id-card-clip',
                'badge' => 'บริการยอดนิยม',
                'link_url' => '#',
                'sort_order' => 1
            ],
            [
                'title' => 'บริการแพทย์ทางไกล (Telemedicine Portal)',
                'description' => 'พบแพทย์เฉพาะทางผ่านวิดีโอคอลความคมชัดสูง สะดวกรวดเร็ว ไม่ต้องเดินทางไกล พร้อมส่งยาถึงบ้าน',
                'icon' => 'fa-solid fa-video',
                'badge' => 'บริการออนไลน์',
                'link_url' => '#',
                'sort_order' => 2
            ],
            [
                'title' => 'ระบบสมุดสุขภาพครอบครัว (Personal Health Record)',
                'description' => 'เก็บบันทึกผลตรวจสุขภาพ ประวัติการรับวัคซีน และกราฟพัฒนาการสุขภาพส่วนบุคคลและคนในครอบครัว',
                'icon' => 'fa-solid fa-heart-pulse',
                'badge' => 'สุขภาพแม่นยำ',
                'link_url' => '#',
                'sort_order' => 3
            ],
            [
                'title' => 'ระบบ AI ประเมินอาการเบื้องต้น (AI Health Symptom Checker)',
                'description' => 'ระบบปัญญาประดิษฐ์อัจฉริยะช่วยประเมินอาการเบื้องต้น แนะนำแนวทางการปฏิบัติตน และส่งต่อคลินิกที่เหมาะสม',
                'icon' => 'fa-solid fa-robot',
                'badge' => 'เทคโนโลยี AI',
                'link_url' => '#',
                'sort_order' => 4
            ],
            [
                'title' => 'ระบบนัดหมายออนไลน์และคิวอัจฉริยะ (Smart Queue & Appointment)',
                'description' => 'จองคิวตรวจล่วงหน้า แจ้งเตือนเวลาเรียกลำดับคิวแบบเรียลไทม์ ลดระยะเวลารอคอยในโรงพยาบาล',
                'icon' => 'fa-solid fa-calendar-check',
                'badge' => 'สะดวก รวดเร็ว',
                'link_url' => '#',
                'sort_order' => 5
            ],
            [
                'title' => 'ระบบส่งต่อผู้ป่วยฉุกเฉินอัจฉริยะ (Smart Referral & Emergency)',
                'description' => 'ส่งต่อข้อมูลเวชระเบียนแบบเร่งด่วน ประสานงานศูนย์กู้ชีพและห้องฉุกเฉิน เพิ่มโอกาสรอดชีวิต',
                'icon' => 'fa-solid fa-truck-medical',
                'badge' => '24 ชั่วโมง',
                'link_url' => '#',
                'sort_order' => 6
            ]
        ];

        $insertService = $pdo->prepare("INSERT INTO `services` (`title`, `description`, `icon`, `badge`, `link_url`, `sort_order`, `is_active`) 
            VALUES (:title, :description, :icon, :badge, :link_url, :sort_order, 1)");
        foreach ($services as $srv) {
            $insertService->execute($srv);
        }
    }
}
