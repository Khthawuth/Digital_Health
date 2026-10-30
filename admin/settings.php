<?php
// admin/settings.php - ตั้งค่าระบบและความปลอดภัย 2FA
$pageTitle = "ตั้งค่าระบบและการยืนยันตัวตน 2FA";
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();
$currentUser = getCurrentUser();

$error = '';
$success = '';

// Test 2FA Email trigger
if (isset($_GET['action']) && $_GET['action'] === 'test_email') {
    $admin2faEmail = getSetting('admin_2fa_email', 'Khthawuth.leo@gmail.com');
    $res = generateAndSend2FA($currentUser['id'], $admin2faEmail, $currentUser['fullname']);
    setFlash('info', "ได้ทดสอบสร้างและส่งรหัส OTP ({$res['otp']}) ไปยังอีเมล {$admin2faEmail} เรียบร้อยแล้ว (ตรวจสอบในกล่องจดหมาย หรือไฟล์ logs/otp_mail.log)");
    header('Location: settings.php');
    exit;
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. General Site Settings
    $settingsToUpdate = [
        'site_name' => trim($_POST['site_name'] ?? ''),
        'site_short_name' => trim($_POST['site_short_name'] ?? ''),
        'site_tagline' => trim($_POST['site_tagline'] ?? ''),
        'admin_2fa_email' => trim($_POST['admin_2fa_email'] ?? 'Khthawuth.leo@gmail.com'),
        'contact_email' => trim($_POST['contact_email'] ?? ''),
        'contact_phone' => trim($_POST['contact_phone'] ?? ''),
        'contact_address' => trim($_POST['contact_address'] ?? ''),
        'working_hours' => trim($_POST['working_hours'] ?? '')
    ];

    $stmtSet = $pdo->prepare("REPLACE INTO system_settings (setting_key, setting_value) VALUES (:k, :v)");
    foreach ($settingsToUpdate as $k => $v) {
        $stmtSet->execute([':k' => $k, ':v' => $v]);
    }

    // Also update current admin user email if changed
    if (!empty($settingsToUpdate['admin_2fa_email'])) {
        $pdo->prepare("UPDATE users SET email = :email WHERE id = :id")
            ->execute([':email' => $settingsToUpdate['admin_2fa_email'], ':id' => $currentUser['id']]);
    }

    // 2. Change Password if provided
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!empty($newPassword)) {
        if (strlen($newPassword) < 6) {
            $error = 'รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน';
        } else {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = :p WHERE id = :id")->execute([':p' => $hashed, ':id' => $currentUser['id']]);
            $success = 'บันทึกการตั้งค่าระบบและเปลี่ยนรหัสผ่านใหม่เรียบร้อยแล้ว';
        }
    } else {
        $success = 'บันทึกการตั้งค่าระบบเรียบร้อยแล้ว';
    }

    if (empty($error)) {
        setFlash('success', $success);
        header('Location: settings.php');
        exit;
    }
}

// Current values
$siteName = getSetting('site_name', 'ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล');
$siteShortName = getSetting('site_short_name', 'สุขภาพดิจิทัล');
$siteTagline = getSetting('site_tagline', '');
$admin2faEmail = getSetting('admin_2fa_email', 'Khthawuth.leo@gmail.com');
$contactEmail = getSetting('contact_email', 'Khthawuth.leo@gmail.com');
$contactPhone = getSetting('contact_phone', '02-590-3000');
$contactAddress = getSetting('contact_address', '');
$workingHours = getSetting('working_hours', '');
?>

<div style="max-width: 900px; margin: 0 auto;">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="settings.php" method="POST">
        <!-- 2FA Security Settings Card -->
        <div class="form-box" style="border-left: 4px solid #0d9488;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid var(--admin-border);">
                <div>
                    <h4 style="font-size: 16px; font-weight: 700; color: #0f766e; margin: 0;">
                        <i class="fa-solid fa-shield-halved"></i> การรักษาความปลอดภัยและการยืนยันตัวตนสองชั้น (2FA)
                    </h4>
                    <span style="font-size: 12.5px; color: #64748b;">ระบบ 2FA ป้องกันการเข้าถึงส่วนผู้ดูแลระบบโดยไม่ได้รับอนุญาต</span>
                </div>
                <a href="settings.php?action=test_email" class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-paper-plane"></i> ทดสอบส่งรหัส 2FA เข้าอีเมล
                </a>
            </div>

            <div class="form-group">
                <label class="form-label">อีเมลรับรหัสผ่าน OTP ยืนยันตัวตน 2FA ของผู้ดูแลระบบ <span style="color:red;">*</span></label>
                <div class="input-icon-wrap">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="admin_2fa_email" required 
                           value="<?= htmlspecialchars($admin2faEmail) ?>" 
                           class="form-control" style="font-weight: 600; color: #0f766e;">
                </div>
                <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">
                    * ระบบจะสร้างรหัสผ่าน OTP 6 หลัก และส่งไปยังอีเมลนี้ทุกครั้งที่มีการเข้าสู่ระบบด้วยบัญชี admin (ปัจจุบันตั้งค่าเป็น <strong>Khthawuth.leo@gmail.com</strong>)
                </small>
            </div>
        </div>

        <!-- Site Information Card -->
        <div class="form-box">
            <h4 class="form-box-title"><i class="fa-solid fa-globe text-primary"></i> ข้อมูลทั่วไปของเว็บไซต์</h4>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">ชื่อเต็มของหน่วยงาน / เว็บไซต์</label>
                    <input type="text" name="site_name" value="<?= htmlspecialchars($siteName) ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">ชื่อย่อ (Logo Brand)</label>
                    <input type="text" name="site_short_name" value="<?= htmlspecialchars($siteShortName) ?>" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">คำขวัญ / สโลแกน (Tagline)</label>
                <input type="text" name="site_tagline" value="<?= htmlspecialchars($siteTagline) ?>" class="form-control">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">เบอร์โทรศัพท์ / สายด่วน</label>
                    <input type="text" name="contact_phone" value="<?= htmlspecialchars($contactPhone) ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">อีเมลกลางสำหรับติดต่อสอบถาม</label>
                    <input type="email" name="contact_email" value="<?= htmlspecialchars($contactEmail) ?>" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">ที่อยู่สำนักงาน</label>
                <textarea name="contact_address" rows="2" class="form-control"><?= htmlspecialchars($contactAddress) ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">วันและเวลาทำการ</label>
                <input type="text" name="working_hours" value="<?= htmlspecialchars($workingHours) ?>" class="form-control">
            </div>
        </div>

        <!-- Admin Password Change Card -->
        <div class="form-box">
            <h4 class="form-box-title"><i class="fa-solid fa-key text-primary"></i> เปลี่ยนรหัสผ่านผู้ดูแลระบบ (Admin)</h4>
            <p style="font-size: 13px; color: #64748b; margin-bottom: 16px;">หากไม่ต้องการเปลี่ยนรหัสผ่าน ให้เว้นว่างไว้ (รหัสผ่านเริ่มต้นคือ admin1234)</p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">รหัสผ่านใหม่</label>
                    <input type="password" name="new_password" class="form-control" placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="••••••••">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 13px 30px; font-size: 15px; font-weight: 600;">
            <i class="fa-solid fa-floppy-disk"></i> บันทึกการตั้งค่าทั้งหมด
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
