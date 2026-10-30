<?php
// login.php - เข้าสู่ระบบ (รองรับทั้ง Admin และ User)
require_once __DIR__ . '/config/functions.php';

// If already logged in, redirect
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: admin/index.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$error = '';
$usernameInput = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameInput = trim($_POST['username'] ?? '');
    $passwordInput = trim($_POST['password'] ?? '');

    if (empty($usernameInput) || empty($passwordInput)) {
        $error = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่านให้ครบถ้วน';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $usernameInput]);
        $user = $stmt->fetch();

        if ($user && password_verify($passwordInput, $user['password'])) {
            // Password is correct!
            // Check if user requires 2FA (Admin always requires 2FA via Khthawuth.leo@gmail.com)
            if ($user['role'] === 'admin' || !empty($user['two_factor_enabled'])) {
                // Ensure email is Khthawuth.leo@gmail.com for admin
                $twoFaEmail = !empty($user['email']) ? $user['email'] : 'Khthawuth.leo@gmail.com';

                // Generate and send 2FA OTP
                $sendResult = generateAndSend2FA($user['id'], $twoFaEmail, $user['fullname']);

                // Store pending session
                $_SESSION['pending_2fa_user_id'] = $user['id'];
                $_SESSION['pending_2fa_username'] = $user['username'];
                $_SESSION['pending_2fa_email'] = $twoFaEmail;
                $_SESSION['pending_2fa_role'] = $user['role'];

                setFlash('info', 'รหัสความปลอดภัย OTP 6 หลัก ถูกส่งไปยังอีเมล ' . htmlspecialchars($twoFaEmail) . ' เรียบร้อยแล้ว');
                header('Location: verify-2fa.php');
                exit;
            } else {
                // Normal user without 2FA
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['fullname'] = $user['fullname'];

                setFlash('success', 'เข้าสู่ระบบสำเร็จ ยินดีต้อนรับคุณ ' . htmlspecialchars($user['fullname']));
                header('Location: index.php');
                exit;
            }
        } else {
            $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง โปรดตรวจสอบอีกครั้ง';
        }
    }
}

$pageTitle = "เข้าสู่ระบบ - ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล";
include __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <h2 class="auth-title">เข้าสู่ระบบ</h2>
            <p class="auth-subtitle">ระบบบริการสุขภาพดิจิทัลและศูนย์จัดการข้อมูลภาครัฐ</p>
        </div>

        <!-- Quick Admin Credentials Hint Box -->
        <div style="background: #f0fdfa; border: 1px solid #ccfbf1; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; font-size: 13px; color: #0f766e;">
            <div style="font-weight: 700; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-key"></i> บัญชีผู้ดูแลระบบ (Admin Credentials):
            </div>
            <div>ชื่อผู้ใช้: <code style="background: white; padding: 2px 6px; border-radius: 4px; font-weight: bold;">admin</code> | รหัสผ่าน: <code style="background: white; padding: 2px 6px; border-radius: 4px; font-weight: bold;">admin1234</code></div>
            <div style="margin-top: 4px; font-size: 12px; color: #0d9488;">
                <i class="fa-solid fa-shield-halved"></i> มีระบบ 2FA ยืนยันรหัสผ่านอีเมล: <strong>Khthawuth.leo@gmail.com</strong>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label class="form-label">ชื่อผู้ใช้งาน (Username)</label>
                <div class="input-icon-wrap">
                    <i class="fa-regular fa-user"></i>
                    <input type="text" name="username" required value="<?= htmlspecialchars($usernameInput) ?>" 
                           class="form-control" placeholder="admin หรือชื่อผู้ใช้ของคุณ" autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">รหัสผ่าน (Password)</label>
                <div class="input-icon-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" required 
                           class="form-control" placeholder="••••••••">
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; font-size: 13px;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                    <input type="checkbox" name="remember" checked> จำชื่อผู้ใช้
                </label>
                <a href="contact.php" style="color: #0d9488;">ลืมรหัสผ่าน?</a>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px; font-weight: 600;">
                <i class="fa-solid fa-right-to-bracket"></i> เข้าสู่ระบบ
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color); font-size: 13.5px; color: #64748b;">
            ยังไม่มีบัญชีผู้ใช้งาน? <a href="register.php" style="color: #0d9488; font-weight: 600;">สมัครสมาชิกประชาชน</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
