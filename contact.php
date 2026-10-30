<?php
// contact.php - หน้าติดต่อเราและส่งข้อความสอบถาม
require_once __DIR__ . '/config/functions.php';

$pageTitle = "ติดต่อเรา - ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล";
$pageDesc = "ติดต่อสอบถามข้อมูล ร้องเรียนการใช้งานระบบ หรือประสานงานศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล";

$pdo = getDBConnection();

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'กรุณากรอกข้อมูลในช่องที่มีเครื่องหมายดอกจัน (*) ให้ครบถ้วน';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'รูปแบบอีเมลไม่ถูกต้อง';
    } else {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) 
            VALUES (:name, :email, :phone, :subject, :message)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':subject' => $subject,
            ':message' => $message
        ]);
        $success = true;
        setFlash('success', 'เจ้าหน้าที่ได้รับข้อความของคุณเรียบร้อยแล้ว และจะติดต่อกลับโดยเร็วที่สุด');
    }
}

$siteTitle = getSetting('site_name', 'ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล');
$contactEmail = getSetting('contact_email', 'Khthawuth.leo@gmail.com');
$contactPhone = getSetting('contact_phone', '02-590-3000');
$contactAddress = getSetting('contact_address', 'อาคารศูนย์สุขภาพดิจิทัล กระทรวงสาธารณสุข ถนนติวานนท์ อำเภอเมือง จังหวัดนนทบุรี 11000');

include __DIR__ . '/includes/header.php';
?>

<!-- Header -->
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 45px 0;">
    <div class="container">
        <div style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: #94a3b8; margin-bottom: 10px;">
            <a href="index.php" style="color: #cbd5e1;">หน้าแรก</a>
            <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
            <span style="color: #38bdf8;">ติดต่อเรา</span>
        </div>
        <h1 style="font-size: 32px; font-weight: 700; color: white; margin-bottom: 8px;">
            <i class="fa-regular fa-envelope text-primary"></i> ติดต่อศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล
        </h1>
        <p style="color: #cbd5e1; font-size: 15px; max-width: 720px;">
            มีข้อสงสัยเกี่ยวกับบริการด้านสุขภาพดิจิทัล หรือต้องการประสานงานเชื่อมต่อระบบ ติดต่อเราได้ทุกช่องทาง
        </p>
    </div>
</div>

<div class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 40px;">
            <!-- Contact Form -->
            <div style="background: white; border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 36px; box-shadow: var(--shadow-sm);">
                <h3 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">
                    ส่งข้อความถึงเรา
                </h3>
                <p style="font-size: 14px; color: #64748b; margin-bottom: 24px;">
                    กรอกข้อมูลด้านล่าง เจ้าหน้าที่จะดำเนินการตอบกลับทางอีเมลหรือโทรศัพท์ของท่าน
                </p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i> ส่งข้อความเรียบร้อยแล้ว ขอบคุณสำหรับข้อมูล
                    </div>
                <?php endif; ?>

                <form action="contact.php" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">ชื่อ - นามสกุล <span style="color:red;">*</span></label>
                            <input type="text" name="name" required class="form-control" placeholder="เช่น นายสมชาย ใจดี">
                        </div>
                        <div class="form-group">
                            <label class="form-label">เบอร์โทรศัพท์ติดต่อ</label>
                            <input type="text" name="phone" class="form-control" placeholder="เช่น 081-234-5678">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">อีเมลสำหรับติดต่อกลับ <span style="color:red;">*</span></label>
                        <input type="email" name="email" required class="form-control" placeholder="เช่น your.email@example.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label">หัวข้อเรื่องที่ต้องการติดต่อ <span style="color:red;">*</span></label>
                        <select name="subject" required class="form-control">
                            <option value="">-- กรุณาเลือกหัวข้อ --</option>
                            <option value="สอบถามการใช้งานระบบการแพทย์ทางไกล (Telemedicine)">สอบถามการใช้งานระบบการแพทย์ทางไกล (Telemedicine)</option>
                            <option value="ปัญหาการยืนยันตัวตน Digital Health ID">ปัญหาการยืนยันตัวตน Digital Health ID</option>
                            <option value="ประสานงานเชื่อมต่อระบบโรงพยาบาล">ประสานงานเชื่อมต่อระบบโรงพยาบาล</option>
                            <option value="ร้องเรียนหรือข้อเสนอแนะ">ร้องเรียนหรือข้อเสนอแนะ</option>
                            <option value="เรื่องอื่นๆ">เรื่องอื่นๆ</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">รายละเอียดข้อความ <span style="color:red;">*</span></label>
                        <textarea name="message" required rows="5" class="form-control" placeholder="พิมพ์รายละเอียดที่ต้องการสอบถาม..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px;">
                        <i class="fa-solid fa-paper-plane"></i> ส่งข้อความ
                    </button>
                </form>
            </div>

            <!-- Contact Information Card -->
            <div>
                <div style="background: white; border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 36px; box-shadow: var(--shadow-sm); margin-bottom: 30px;">
                    <h3 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                        ช่องทางการติดต่อ
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div style="display: flex; gap: 16px;">
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #ccfbf1; color: #0d9488; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 15px; margin-bottom: 4px;">สถานที่ตั้งสำนักงาน</h4>
                                <p style="font-size: 13.5px; color: #64748b; line-height: 1.6;">
                                    <?= htmlspecialchars($contactAddress) ?>
                                </p>
                            </div>
                        </div>

                        <div style="display: flex; gap: 16px;">
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 15px; margin-bottom: 4px;">โทรศัพท์ / สายด่วน</h4>
                                <p style="font-size: 13.5px; color: #64748b; line-height: 1.6;">
                                    โทรศัพท์: <?= htmlspecialchars($contactPhone) ?><br>
                                    สายด่วนสาธารณสุข: 1422 (ตลอด 24 ชั่วโมง)
                                </p>
                            </div>
                        </div>

                        <div style="display: flex; gap: 16px;">
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #f3e8ff; color: #9333ea; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 15px; margin-bottom: 4px;">อีเมลกลาง</h4>
                                <p style="font-size: 13.5px; color: #64748b; line-height: 1.6;">
                                    <a href="mailto:<?= htmlspecialchars($contactEmail) ?>" style="color: #0d9488; font-weight: 500;"><?= htmlspecialchars($contactEmail) ?></a>
                                </p>
                            </div>
                        </div>

                        <div style="display: flex; gap: 16px;">
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                                <i class="fa-regular fa-clock"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 15px; margin-bottom: 4px;">วันและเวลาทำการ</h4>
                                <p style="font-size: 13.5px; color: #64748b; line-height: 1.6;">
                                    จันทร์ - ศุกร์: 08:30 - 16:30 น.<br>
                                    (ยกเว้นวันหยุดราชการและวันหยุดนักขัตฤกษ์)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Map Placeholder Card -->
                <div style="background: white; border-radius: var(--radius-lg); border: 1px solid var(--border-color); overflow: hidden; box-shadow: var(--shadow-sm);">
                    <div style="background: #0f172a; color: white; padding: 14px 20px; font-weight: 600; font-size: 14px;">
                        <i class="fa-solid fa-map-location-dot text-primary"></i> แผนที่ศูนย์สุขภาพดิจิทัล
                    </div>
                    <div style="height: 200px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; text-align: center; padding: 20px;">
                        <div>
                            <i class="fa-solid fa-location-crosshairs" style="font-size: 32px; color: #0d9488; margin-bottom: 8px;"></i>
                            <div style="font-weight: 600; color: #334155;">ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล</div>
                            <div style="font-size: 12px; color: #64748b;">กระทรวงสาธารณสุข ถนนติวานนท์ อ.เมือง จ.นนทบุรี</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
