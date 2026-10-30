<?php
// config/functions.php - Helper Functions & 2FA Engine

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';

// Flash message helpers
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Authentication status
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id, username, fullname, email, phone, role FROM users WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    return $stmt->fetch();
}

function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('warning', 'กรุณาเข้าสู่ระบบก่อนทำรายการ');
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        setFlash('danger', 'คุณไม่มีสิทธิ์เข้าถึงหน้าผู้ดูแลระบบ');
        header('Location: ../login.php');
        exit;
    }
}

// System settings getter
function getSetting($key, $default = '') {
    static $settingsCache = null;
    $pdo = getDBConnection();
    if ($settingsCache === null) {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings");
        $settingsCache = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    return $settingsCache[$key] ?? $default;
}

// Thai Date Formatter
function thaiDate($datetime, $showTime = false) {
    if (!$datetime || $datetime === '0000-00-00 00:00:00') return '-';
    $timestamp = strtotime($datetime);
    $thai_months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
        5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
        9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];
    $day = date('j', $timestamp);
    $month = $thai_months[(int)date('n', $timestamp)];
    $year = date('Y', $timestamp) + 543; // Buddhist Era
    
    if ($showTime) {
        $time = date('H:i', $timestamp) . ' น.';
        return "$day $month $year เวลา $time";
    }
    return "$day $month $year";
}

// 2FA Generation & Sending to Khthawuth.leo@gmail.com
function generateAndSend2FA($userId, $email, $fullname = 'ผู้ใช้งาน') {
    $pdo = getDBConnection();
    
    // Generate secure 6-digit OTP code
    $otpCode = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', time() + (10 * 60)); // Valid for 10 minutes

    // Invalidate previous unused codes for this user
    $stmtExpire = $pdo->prepare("UPDATE two_factor_auth SET is_used = 1 WHERE user_id = :uid AND is_used = 0");
    $stmtExpire->execute([':uid' => $userId]);

    // Insert new 2FA record
    $stmt = $pdo->prepare("INSERT INTO two_factor_auth (user_id, email, otp_code, expires_at, is_used) 
        VALUES (:uid, :email, :code, :exp, 0)");
    $stmt->execute([
        ':uid' => $userId,
        ':email' => $email,
        ':code' => $otpCode,
        ':exp' => $expiresAt
    ]);

    // Store in session for dev preview / test fallback
    $_SESSION['dev_otp_preview'] = $otpCode;
    $_SESSION['otp_created_time'] = time();

    // Prepare email sending
    $subject = "รหัสยืนยันตัวตน 2FA (OTP) - ศูนย์สุขภาพดิจิทัล";
    $message = "
    <!DOCTYPE html>
    <html lang='th'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0fdf4; margin: 0; padding: 20px; }
            .card { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border-top: 6px solid #0d9488; }
            .header { text-align: center; margin-bottom: 25px; }
            .logo { font-size: 24px; font-weight: bold; color: #0d9488; }
            .otp-box { background: #f0fdfa; border: 2px dashed #14b8a6; padding: 18px; border-radius: 10px; text-align: center; margin: 25px 0; }
            .otp-code { font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #0f766e; }
            .warning { font-size: 13px; color: #dc2626; margin-top: 15px; }
            .footer { font-size: 12px; color: #64748b; text-align: center; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 15px; }
        </style>
    </head>
    <body>
        <div class='card'>
            <div class='header'>
                <div class='logo'>🏥 ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล</div>
                <h2 style='color:#1e293b;margin:10px 0 0;'>รหัสยืนยันตัวตนสองชั้น (2FA OTP)</h2>
            </div>
            <p>เรียนคุณ <strong>" . htmlspecialchars($fullname) . "</strong>,</p>
            <p>ระบบตรวจพบการร้องขอเข้าสู่ระบบผู้ดูแลระบบ (Admin) ศูนย์สุขภาพดิจิทัล โปรดนำรหัส OTP 6 หลักด้านล่างไปกรอกในหน้ายืนยันความปลอดภัย:</p>
            <div class='otp-box'>
                <div style='font-size:14px;color:#0d9488;margin-bottom:8px;'>รหัส OTP ของคุณคือ:</div>
                <div class='otp-code'>" . $otpCode . "</div>
                <div style='font-size:12px;color:#64748b;margin-top:6px;'>(รหัสมีอายุการใช้งาน 10 นาที)</div>
            </div>
            <p class='warning'>⚠️ <strong>คำเตือนความปลอดภัย:</strong> ห้ามเปิดเผยรหัส OTP นี้แก่บุคคลอื่นเด็ดขาด เจ้าหน้าที่ศูนย์สุขภาพดิจิทัลจะไม่ติดต่อเพื่อขอรหัสนี้จากท่าน</p>
            <div class='footer'>
                อีเมลนี้เป็นข้อความอัตโนมัติจากระบบรักษาความปลอดภัย ศูนย์สุขภาพดิจิทัล กระทรวงสาธารณสุข<br>
                ส่งไปยัง: " . htmlspecialchars($email) . " | วันที่ส่ง: " . date('d/m/Y H:i:s') . "
            </div>
        </div>
    </body>
    </html>";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: ศูนย์สุขภาพดิจิทัล <noreply@digitalhealth.moph.go.th>\r\n";
    $headers .= "Reply-To: noreply@digitalhealth.moph.go.th\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Attempt to send email via mail()
    $mailSent = @mail($email, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);

    // Also write to an email log file in runtime for testing verification
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }
    $logContent = "[" . date('Y-m-d H:i:s') . "] 2FA OTP for User ID: {$userId} | Email: {$email} | Code: {$otpCode} | Sent: " . ($mailSent ? 'YES' : 'NO (fallback preview active)') . "\n";
    @file_put_contents($logDir . '/otp_mail.log', $logContent, FILE_APPEND);

    return [
        'success' => true,
        'otp' => $otpCode,
        'email' => $email,
        'mailSent' => $mailSent
    ];
}

// Verify 2FA OTP Code
function verify2FAOTP($userId, $inputOtp) {
    $pdo = getDBConnection();
    $inputOtp = trim($inputOtp);

    $stmt = $pdo->prepare("SELECT id, expires_at FROM two_factor_auth 
        WHERE user_id = :uid AND otp_code = :code AND is_used = 0 
        ORDER BY id DESC LIMIT 1");
    $stmt->execute([
        ':uid' => $userId,
        ':code' => $inputOtp
    ]);
    $record = $stmt->fetch();

    if (!$record) {
        return ['success' => false, 'message' => 'รหัส OTP ไม่ถูกต้อง กรุณาตรวจสอบใหม่อีกครั้ง'];
    }

    if (strtotime($record['expires_at']) < time()) {
        return ['success' => false, 'message' => 'รหัส OTP หมดอายุแล้ว (เกิน 10 นาที) กรุณากดขอรหัสใหม่'];
    }

    // Mark as used
    $updateStmt = $pdo->prepare("UPDATE two_factor_auth SET is_used = 1 WHERE id = :id");
    $updateStmt->execute([':id' => $record['id']]);

    // Clean session preview
    unset($_SESSION['dev_otp_preview']);

    return ['success' => true, 'message' => 'ยืนยันรหัสความปลอดภัยสำเร็จ'];
}

// Upload file helper
function handleImageUpload($fileInputName, $subfolder = 'news') {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$fileInputName];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($fileInfo, $file['tmp_name']);
    finfo_close($fileInfo);

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($mimeType, $allowedTypes) || !in_array($ext, $allowedExts)) {
        return ['error' => 'ประเภทไฟล์ไม่ถูกต้อง อนุญาตเฉพาะไฟล์ JPG, PNG, WEBP, GIF เท่านั้น'];
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        return ['error' => 'ขนาดไฟล์เกิน 5MB กรุณาเลือกไฟล์ที่มีขนาดเล็กลง'];
    }

    $uploadDir = __DIR__ . '/../uploads/' . $subfolder . '/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0777, true);
    }

    $filename = $subfolder . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['path' => 'uploads/' . $subfolder . '/' . $filename];
    }

    return ['error' => 'ไม่สามารถบันทึกไฟล์ได้'];
}
