<?php
// verify-2fa.php - หน้าตรวจสอบรหัสยืนยัน 2FA ทางอีเมล (Khthawuth.leo@gmail.com)
require_once __DIR__ . '/config/functions.php';

// Must have pending 2FA session
if (!isset($_SESSION['pending_2fa_user_id'])) {
    setFlash('warning', 'กรุณาเข้าสู่ระบบก่อนทำการยืนยันรหัสความปลอดภัย');
    header('Location: login.php');
    exit;
}

$pendingUserId = $_SESSION['pending_2fa_user_id'];
$pendingEmail = $_SESSION['pending_2fa_email'] ?? 'Khthawuth.leo@gmail.com';
$pendingUsername = $_SESSION['pending_2fa_username'] ?? 'admin';
$pendingRole = $_SESSION['pending_2fa_role'] ?? 'admin';

$error = '';
$previewOtp = $_SESSION['dev_otp_preview'] ?? '';

// Handle Resend Request
if (isset($_GET['action']) && $_GET['action'] === 'resend') {
    $sendResult = generateAndSend2FA($pendingUserId, $pendingEmail, 'ผู้ดูแลระบบ');
    $previewOtp = $sendResult['otp'];
    setFlash('info', 'สร้างและส่งรหัส OTP ใหม่อีกครั้งไปยัง ' . htmlspecialchars($pendingEmail) . ' เรียบร้อยแล้ว');
    header('Location: verify-2fa.php');
    exit;
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otpCode = trim($_POST['otp_code'] ?? '');

    if (empty($otpCode)) {
        $error = 'กรุณากรอกรหัส OTP 6 หลัก';
    } else {
        $result = verify2FAOTP($pendingUserId, $otpCode);
        if ($result['success']) {
            // Fetch fresh user data
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute([':id' => $pendingUserId]);
            $user = $stmt->fetch();

            // Set logged in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['fullname'] = $user['fullname'];

            // Clear pending 2FA session
            unset($_SESSION['pending_2fa_user_id']);
            unset($_SESSION['pending_2fa_username']);
            unset($_SESSION['pending_2fa_email']);
            unset($_SESSION['pending_2fa_role']);
            unset($_SESSION['dev_otp_preview']);

            setFlash('success', 'ยืนยันตัวตน 2FA สำเร็จ! ยินดีต้อนรับเข้าสู่ระบบ ' . ($user['role'] === 'admin' ? 'ผู้ดูแลระบบ' : ''));

            if ($user['role'] === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = "ยืนยันรหัสความปลอดภัย 2FA - ศูนย์สุขภาพดิจิทัล";
include __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card" style="max-width: 520px;">
        <div class="auth-header">
            <div class="auth-logo" style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);">
                <i class="fa-solid fa-envelope-circle-check"></i>
            </div>
            <h2 class="auth-title">การยืนยันตัวตนสองชั้น (2FA)</h2>
            <p class="auth-subtitle">ระบบรักษาความปลอดภัยข้อมูลสุขภาพภาครัฐ</p>
        </div>

        <!-- Target Email Notice Box -->
        <div class="otp-box-ui">
            <div style="font-size: 13.5px; color: #475569; margin-bottom: 6px;">
                ระบบได้ส่งรหัสผ่านแบบใช้ครั้งเดียว (OTP 6 หลัก) ไปยังอีเมล:
            </div>
            <div class="otp-target-email" style="font-size: 16px;">
                <i class="fa-regular fa-envelope"></i> <?= htmlspecialchars($pendingEmail) ?>
            </div>
            <div style="font-size: 12px; color: #0f766e; margin-top: 8px;">
                (การเข้ารหัสความปลอดภัยสำหรับบัญชี <strong><?= htmlspecialchars($pendingUsername) ?></strong>)
            </div>
        </div>

        <!-- Testing / Localhost Fallback Helper -->
        <?php if (!empty($previewOtp)): ?>
            <div class="dev-otp-helper">
                <i class="fa-solid fa-vial-circle-check" style="font-size: 20px; color: #d97706;"></i>
                <div style="flex-grow: 1;">
                    <div style="font-weight: 700; font-size: 12.5px;">รหัส OTP สำหรับทดสอบ (ระบบส่งเข้าอีเมลจริง + สำรองในเครื่อง):</div>
                    <div style="margin-top: 3px;">
                        รหัสคือ: <span class="dev-otp-badge"><?= htmlspecialchars($previewOtp) ?></span>
                    </div>
                </div>
                <button type="button" id="copyOtpBtn" data-otp="<?= htmlspecialchars($previewOtp) ?>" class="btn btn-sm btn-light" style="font-size: 12px; background: white; border: 1px solid #fcd34d;">
                    <i class="fa-regular fa-copy"></i> นำรหัสไปกรอกอัตโนมัติ
                </button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="verify-2fa.php" method="POST" id="otpForm">
            <!-- Hidden full code field -->
            <input type="hidden" name="otp_code" id="full_otp_code" value="">

            <label class="form-label" style="text-align: center;">กรอกรหัสยืนยัน 6 หลัก</label>
            <div class="otp-inputs">
                <input type="text" maxlength="1" class="otp-input-field" inputmode="numeric" autocomplete="one-time-code">
                <input type="text" maxlength="1" class="otp-input-field" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-input-field" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-input-field" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-input-field" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-input-field" inputmode="numeric">
            </div>

            <!-- Countdown Timer -->
            <div id="otpCountdown" style="text-align: center; font-size: 13px; color: #64748b; margin-bottom: 20px;">
                รหัสจะหมดอายุในอีก 10:00 นาที
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px; font-weight: 600;">
                <i class="fa-solid fa-circle-check"></i> ยืนยันรหัส OTP และเข้าสู่ระบบ
            </button>
        </form>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color); font-size: 13px;">
            <a href="verify-2fa.php?action=resend" style="color: #0d9488; font-weight: 600;">
                <i class="fa-solid fa-rotate-right"></i> ส่งรหัส OTP ใหม่อีกครั้ง
            </a>
            <a href="logout.php" style="color: #64748b;">
                <i class="fa-solid fa-arrow-left"></i> ยกเลิกและกลับไปหน้าล็อคอิน
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
