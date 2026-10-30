<?php
// includes/footer.php - Global Footer
require_once __DIR__ . '/../config/functions.php';

$siteTitle = getSetting('site_name', 'ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล');
$contactEmail = getSetting('contact_email', 'Khthawuth.leo@gmail.com');
$contactPhone = getSetting('contact_phone', '02-590-3000');
$contactAddress = getSetting('contact_address', 'กระทรวงสาธารณสุข ถนนติวานนท์ อำเภอเมือง จังหวัดนนทบุรี 11000');
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Col 1: About & Trust -->
            <div class="footer-brand">
                <h2><i class="fa-solid fa-code-medical text-primary"></i> <?= htmlspecialchars($siteTitle) ?></h2>
                <p>
                    ทีมโปรแกรมเมอร์และพัฒนาระบบสารสนเทศสุขภาพดิจิทัลโรงพยาบาล มุ่งมั่นพัฒนาระบบโรงพยาบาล (HIS), เวชระเบียนอิเล็กทรอนิกส์, APIs เชื่อมต่อข้อมูล และนวัตกรรมดิจิทัลเพื่อการบริการทางการแพทย์ที่มีประสิทธิภาพและปลอดภัยสูงสุด
                </p>
                <div style="display:flex;gap:12px;align-items:center;">
                    <span class="badge" style="background:#1e293b;color:#38bdf8;border:1px solid rgba(56,189,248,0.3);">
                        <i class="fa-solid fa-shield-halved"></i> มาตรฐาน PDPA
                    </span>
                    <span class="badge" style="background:#1e293b;color:#4ade80;border:1px solid rgba(74,222,128,0.3);">
                        <i class="fa-solid fa-lock"></i> ISO/IEC 27001
                    </span>
                </div>
            </div>

            <!-- Col 2: Quick Links -->
            <div>
                <h3 class="footer-heading">เมนูลัด</h3>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="fa-solid fa-chevron-right" style="font-size:11px;"></i> หน้าแรก</a></li>
                    <li><a href="news.php"><i class="fa-solid fa-chevron-right" style="font-size:11px;"></i> ข่าวประชาสัมพันธ์</a></li>
                    <li><a href="organization.php"><i class="fa-solid fa-chevron-right" style="font-size:11px;"></i> ผังบริหารองค์กร</a></li>
                    <li><a href="duty-schedule.php"><i class="fa-solid fa-chevron-right" style="font-size:11px;"></i> ปฏิทินตารางเวรเจ้าหน้าที่</a></li>
                    <li><a href="services.php"><i class="fa-solid fa-chevron-right" style="font-size:11px;"></i> บริการสุขภาพดิจิทัล</a></li>
                    <li><a href="about.php"><i class="fa-solid fa-chevron-right" style="font-size:11px;"></i> เกี่ยวกับเรา</a></li>
                    <li><a href="contact.php"><i class="fa-solid fa-chevron-right" style="font-size:11px;"></i> ติดต่อสอบถาม</a></li>
                </ul>
            </div>

            <!-- Col 3: Services -->
            <div>
                <h3 class="footer-heading">บริการดิจิทัล</h3>
                <ul class="footer-links">
                    <li><a href="services.php#health-id"><i class="fa-solid fa-chevron-right" style="font-size:11px;"></i> บัตรสุขภาพดิจิทัล ID</a></li>
                    <li><a href="services.php#telemed"><i class="fa-solid fa-chevron-right" style="font-size:11px;"></i> แพทย์ทางไกล Telemedicine</a></li>
                    <li><a href="services.php#queue"><i class="fa-solid fa-chevron-right" style="font-size:11px;"></i> ระบบคิวและนัดหมายออนไลน์</a></li>
                    <li><a href="services.php#ai"><i class="fa-solid fa-chevron-right" style="font-size:11px;"></i> AI ประเมินอาการเบื้องต้น</a></li>
                    <li><a href="login.php"><i class="fa-solid fa-chevron-right" style="font-size:11px;"></i> ระบบตรวจสอบสิทธิ์</a></li>
                </ul>
            </div>

            <!-- Col 4: Contact info -->
            <div>
                <h3 class="footer-heading">ข้อมูลติดต่อ</h3>
                <ul class="footer-contact-list">
                    <li>
                        <i class="fa-solid fa-location-dot"></i>
                        <span><?= htmlspecialchars($contactAddress) ?></span>
                    </li>
                    <li>
                        <i class="fa-solid fa-phone"></i>
                        <span>โทร: <?= htmlspecialchars($contactPhone) ?></span>
                    </li>
                    <li>
                        <i class="fa-solid fa-envelope"></i>
                        <span>อีเมล: <?= htmlspecialchars($contactEmail) ?></span>
                    </li>
                    <li>
                        <i class="fa-solid fa-clock"></i>
                        <span>เวลาทำการ: จันทร์ - ศุกร์ 08:30 - 16:30 น.</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                <div>
                    &copy; <?= (date('Y') + 543) ?> <?= htmlspecialchars($siteTitle) ?>. สงวนลิขสิทธิ์ตามกฎหมาย.
                </div>
                <div style="display:flex;gap:18px;">
                    <a href="about.php" style="color:#64748b;">นโยบายความเป็นส่วนตัว</a>
                    <a href="about.php" style="color:#64748b;">ข้อกำหนดการใช้งาน</a>
                    <a href="admin/index.php" style="color:#64748b;"><i class="fa-solid fa-lock"></i> เจ้าหน้าที่ดูแลระบบ</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="assets/js/main.js"></script>
</body>
</html>
