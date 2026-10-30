<?php
// services.php - บริการดิจิทัลเพื่อประชาชน
require_once __DIR__ . '/config/functions.php';

$pageTitle = "บริการสุขภาพดิจิทัล - ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล";
$pageDesc = "บริการสาธารณสุขดิจิทัล การแพทย์ทางไกล สมุดสุขภาพประชาชน และระบบคิวอัจฉริยะ";

$pdo = getDBConnection();
$services = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- Header -->
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 45px 0;">
    <div class="container">
        <div style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: #94a3b8; margin-bottom: 10px;">
            <a href="index.php" style="color: #cbd5e1;">หน้าแรก</a>
            <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
            <span style="color: #38bdf8;">บริการดิจิทัล</span>
        </div>
        <h1 style="font-size: 32px; font-weight: 700; color: white; margin-bottom: 8px;">
            <i class="fa-solid fa-laptop-medical text-primary"></i> บริการสุขภาพดิจิทัลเพื่อประชาชน
        </h1>
        <p style="color: #cbd5e1; font-size: 15px; max-width: 720px;">
            รวมช่องทางและนวัตกรรมการบริการด้านสุขภาพดิจิทัล เชื่อมโยงข้อมูลอย่างปลอดภัย เพื่อสุขภาวะที่ดีของคนไทยทุกคน
        </p>
    </div>
</div>

<div class="section">
    <div class="container">
        <div class="services-grid" style="grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));">
            <?php foreach ($services as $srv): ?>
                <div class="service-card" style="padding: 30px;">
                    <div class="service-header">
                        <div class="service-icon" style="width: 58px; height: 58px; font-size: 26px;">
                            <i class="<?= htmlspecialchars($srv['icon']) ?>"></i>
                        </div>
                        <span class="badge" style="background:#ccfbf1;color:#0f766e;font-size:12px;font-weight:600;padding:5px 10px;">
                            <?= htmlspecialchars($srv['badge']) ?>
                        </span>
                    </div>
                    <h3 class="service-title" style="font-size: 19px; margin-bottom: 10px;"><?= htmlspecialchars($srv['title']) ?></h3>
                    <p class="service-desc" style="font-size: 14px; line-height: 1.6; margin-bottom: 24px; color: #475569;">
                        <?= htmlspecialchars($srv['description']) ?>
                    </p>
                    <div style="margin-top: auto; display: flex; gap: 10px;">
                        <a href="<?= htmlspecialchars($srv['link_url']) ?>" class="btn btn-primary btn-sm" style="flex-grow: 1;">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> เข้าใช้งานระบบ
                        </a>
                        <a href="contact.php" class="btn btn-light btn-sm" title="สอบถามเพิ่มเติม">
                            <i class="fa-regular fa-circle-question"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Highlight Infographic Card -->
        <div style="margin-top: 50px; background: white; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-md); padding: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;">
            <div>
                <span class="badge-pill"><i class="fa-solid fa-shield-halved"></i> มาตรฐานความปลอดภัยระดับสากล</span>
                <h2 style="font-size: 26px; color: #0f172a; margin-bottom: 16px;">
                    การแพทย์ทางไกล (Telemedicine) สะดวก ปลอดภัย ไม่ต้องรอคิว
                </h2>
                <p style="color: #475569; font-size: 14.5px; line-height: 1.7; margin-bottom: 20px;">
                    ระบบสุขภาพดิจิทัลได้รับการรับรองตามมาตรฐานความมั่นคงปลอดภัยสารสนเทศ ISO/IEC 27001 และพระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล (PDPA) ทุกการสนทนาและการแลกเปลี่ยนประวัติเวชระเบียนได้รับการเข้ารหัสระดับ 256-bit End-to-End Encryption
                </p>
                <div style="display: flex; gap: 15px;">
                    <a href="register.php" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> สมัครสมาชิกเพื่อเริ่มใช้งาน</a>
                    <a href="contact.php" class="btn btn-outline-primary"><i class="fa-solid fa-headset"></i> ขอรับคำแนะนำ</a>
                </div>
            </div>
            <div style="border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-lg);">
                <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=800&q=80" alt="Telemedicine" style="width: 100%; height: 320px; object-fit: cover;">
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
