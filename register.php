<?php
// register.php - ลงทะเบียนสมาชิกประชาชนและบุคลากรสุขภาพ
require_once __DIR__ . '/config/functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$fullname = '';
$username = '';
$email = '';
$phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $consent = isset($_POST['consent']);

    if (empty($fullname) || empty($username) || empty($email) || empty($password)) {
        $error = 'กรุณากรอกข้อมูลในช่องที่จำเป็นให้ครบถ้วน';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'รูปแบบอีเมลไม่ถูกต้อง';
    } elseif (strlen($password) < 6) {
        $error = 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
    } elseif ($password !== $confirmPassword) {
        $error = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
    } elseif (!$consent) {
        $error = 'กรุณายินยอมตามข้อกำหนดนโยบายคุ้มครองข้อมูลส่วนบุคคล (PDPA)';
    } else {
        $pdo = getDBConnection();

        // Check if username already exists
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :u");
        $stmtCheck->execute([':u' => $username]);
        if ($stmtCheck->fetchColumn() > 0) {
            $error = 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว กรุณาเลือกชื่อผู้ใช้อื่น';
        } else {
            // Hash password and insert
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmtInsert = $pdo->prepare("INSERT INTO users (username, password, fullname, email, phone, role, two_factor_enabled) 
                VALUES (:u, :p, :f, :e, :ph, 'user', 0)");
            $stmtInsert->execute([
                ':u' => $username,
                ':p' => $hashed,
                ':f' => $fullname,
                ':e' => $email,
                ':ph' => $phone
            ]);

            setFlash('success', 'ลงทะเบียนสำเร็จเรียบร้อยแล้ว ท่านสามารถเข้าสู่ระบบด้วยชื่อผู้ใช้และรหัสผ่านได้ทันที');
            header('Location: login.php');
            exit;
        }
    }
}

$pageTitle = "ลงทะเบียนสมัครสมาชิก - ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล";
include __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card" style="max-width: 520px;">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="auth-title">ลงทะเบียนสมาชิก</h2>
            <p class="auth-subtitle">ระบบบัตรสุขภาพดิจิทัลและบริการสาธารณสุขทางไกล</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label class="form-label">ชื่อ - นามสกุลจริง <span style="color:red;">*</span></label>
                <div class="input-icon-wrap">
                    <i class="fa-regular fa-id-card"></i>
                    <input type="text" name="fullname" required value="<?= htmlspecialchars($fullname) ?>" 
                           class="form-control" placeholder="เช่น นายสมชาย สุขภาพดี">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">ชื่อผู้ใช้ (Username) <span style="color:red;">*</span></label>
                    <div class="input-icon-wrap">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" name="username" required value="<?= htmlspecialchars($username) ?>" 
                               class="form-control" placeholder="เช่น somchai2026">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">เบอร์โทรศัพท์</label>
                    <div class="input-icon-wrap">
                        <i class="fa-solid fa-phone"></i>
                        <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" 
                               class="form-control" placeholder="เช่น 081-234-5678">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">อีเมล <span style="color:red;">*</span></label>
                <div class="input-icon-wrap">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="email" required value="<?= htmlspecialchars($email) ?>" 
                           class="form-control" placeholder="เช่น your.email@example.com">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">รหัสผ่าน <span style="color:red;">*</span></label>
                    <div class="input-icon-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" required 
                               class="form-control" placeholder="อย่างน้อย 6 ตัวอักษร">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">ยืนยันรหัสผ่าน <span style="color:red;">*</span></label>
                    <div class="input-icon-wrap">
                        <i class="fa-solid fa-shield-halved"></i>
                        <input type="password" name="confirm_password" required 
                               class="form-control" placeholder="พิมพ์รหัสผ่านซ้ำ">
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 10px;">
                <label style="display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: #475569; cursor: pointer;">
                    <input type="checkbox" name="consent" required style="margin-top: 3px;">
                    <span>ข้าพเจ้ายินยอมให้ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล จัดเก็บและประมวลผลข้อมูลส่วนบุคคลตาม <a href="about.php" target="_blank" style="color:#0d9488;text-decoration:underline;">พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล (PDPA)</a></span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px; font-weight: 600; margin-top: 10px;">
                <i class="fa-solid fa-user-check"></i> ยืนยันการลงทะเบียน
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color); font-size: 13.5px; color: #64748b;">
            มีบัญชีผู้ใช้งานอยู่แล้ว? <a href="login.php" style="color: #0d9488; font-weight: 600;">เข้าสู่ระบบที่นี่</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
