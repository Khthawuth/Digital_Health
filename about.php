<?php
// about.php - เกี่ยวกับเรา วิสัยทัศน์ พันธกิจ
require_once __DIR__ . '/config/functions.php';

$pageTitle = "เกี่ยวกับเรา - ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล";
$pageDesc = "วิสัยทัศน์ พันธกิจ ยุทธศาสตร์ และประวัติความเป็นมาของศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล";

include __DIR__ . '/includes/header.php';
?>

<!-- Header -->
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 45px 0;">
    <div class="container">
        <div style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: #94a3b8; margin-bottom: 10px;">
            <a href="index.php" style="color: #cbd5e1;">หน้าแรก</a>
            <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
            <span style="color: #38bdf8;">เกี่ยวกับเรา</span>
        </div>
        <h1 style="font-size: 32px; font-weight: 700; color: white; margin-bottom: 8px;">
            <i class="fa-regular fa-building text-primary"></i> เกี่ยวกับศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล
        </h1>
        <p style="color: #cbd5e1; font-size: 15px; max-width: 720px;">
            หน่วยงานหลักในการขับเคลื่อนยุทธศาสตร์สุขภาพดิจิทัลเพื่อการสาธารณสุขไทยที่ทั่วถึง เท่าเทียม และทันสมัย
        </p>
    </div>
</div>

<div class="section">
    <div class="container">
        <!-- Vision & Mission Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 50px;">
            <div style="background: white; border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 36px; box-shadow: var(--shadow-sm); border-top: 5px solid var(--primary);">
                <div style="width: 52px; height: 52px; border-radius: 12px; background: #ccfbf1; color: #0d9488; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 20px;">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <h3 style="font-size: 22px; color: #0f172a; margin-bottom: 12px;">วิสัยทัศน์ (Vision)</h3>
                <p style="font-size: 15px; color: #334155; line-height: 1.8;">
                    "เป็นศูนย์กลางนวัตกรรมสุขภาพดิจิทัลชั้นนำระดับภูมิภาค ขับเคลื่อนระบบสุขภาพไทยสู่ความยั่งยืนด้วยข้อมูลและเทคโนโลยีอัจฉริยะ เพื่อสุขภาวะที่ดีของคนไทยทุกคน"
                </p>
            </div>

            <div style="background: white; border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 36px; box-shadow: var(--shadow-sm); border-top: 5px solid var(--secondary);">
                <div style="width: 52px; height: 52px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 20px;">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <h3 style="font-size: 22px; color: #0f172a; margin-bottom: 12px;">พันธกิจ (Mission)</h3>
                <ul style="font-size: 14.5px; color: #334155; line-height: 1.8; padding-left: 20px; display: flex; flex-direction: column; gap: 8px;">
                    <li>พัฒนาระบบแลกเปลี่ยนข้อมูลสุขภาพแห่งชาติ (National Health Information Exchange) ให้มีความปลอดภัยสูง</li>
                    <li>ส่งเสริมและขยายผลระบบการแพทย์ทางไกล (Telemedicine) สู่พื้นที่ห่างไกลอย่างเท่าเทียม</li>
                    <li>วิจัย พัฒนา และประยุกต์ใช้เทคโนโลยีปัญญาประดิษฐ์ (AI) ในการวินิจฉัยและบริการสุขภาพ</li>
                    <li>ยกระดับความมั่นคงปลอดภัยทางไซเบอร์และการคุ้มครองข้อมูลส่วนบุคคล (PDPA) ด้านสาธารณสุข</li>
                </ul>
            </div>
        </div>

        <!-- Strategy 4 Pillars -->
        <div class="section-title-wrap">
            <span class="badge-pill"><i class="fa-solid fa-chess"></i> เสาหลักยุทธศาสตร์</span>
            <h2 class="section-title">4 ยุทธศาสตร์การขับเคลื่อนสุขภาพดิจิทัล (2026-2030)</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px;">
            <div style="background: white; padding: 26px; border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                <div style="font-size: 28px; color: #0d9488; font-weight: 800; font-family: 'Inter', sans-serif; margin-bottom: 10px;">01</div>
                <h4 style="font-size: 17px; margin-bottom: 8px; color: #0f172a;">Smart Infrastructure</h4>
                <p style="font-size: 13.5px; color: #64748b; line-height: 1.6;">
                    วางโครงสร้างพื้นฐานคลาวด์และเครือข่ายความเร็วสูงเชื่อมต่อสถานพยาบาลทุกสังกัดทั่วประเทศ
                </p>
            </div>
            <div style="background: white; padding: 26px; border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                <div style="font-size: 28px; color: #0284c7; font-weight: 800; font-family: 'Inter', sans-serif; margin-bottom: 10px;">02</div>
                <h4 style="font-size: 17px; margin-bottom: 8px; color: #0f172a;">Data Interoperability</h4>
                <p style="font-size: 13.5px; color: #64748b; line-height: 1.6;">
                    มาตรฐานการแลกเปลี่ยนข้อมูลระดับสากล HL7 FHIR เชื่อมโยงเวชระเบียนแบบไร้รอยต่อ
                </p>
            </div>
            <div style="background: white; padding: 26px; border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                <div style="font-size: 28px; color: #8b5cf6; font-weight: 800; font-family: 'Inter', sans-serif; margin-bottom: 10px;">03</div>
                <h4 style="font-size: 17px; margin-bottom: 8px; color: #0f172a;">AI & Innovation</h4>
                <p style="font-size: 13.5px; color: #64748b; line-height: 1.6;">
                    นำปัญญาประดิษฐ์มาช่วยวิเคราะห์และคัดกรองโรคเบื้องต้น เพิ่มประสิทธิภาพการวินิจฉัยของแพทย์
                </p>
            </div>
            <div style="background: white; padding: 26px; border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                <div style="font-size: 28px; color: #f59e0b; font-weight: 800; font-family: 'Inter', sans-serif; margin-bottom: 10px;">04</div>
                <h4 style="font-size: 17px; margin-bottom: 8px; color: #0f172a;">Cybersecurity & PDPA</h4>
                <p style="font-size: 13.5px; color: #64748b; line-height: 1.6;">
                    การปกป้องข้อมูลส่วนบุคคลและการรักษาความมั่นคงปลอดภัยสารสนเทศตามมาตรฐานสากล
                </p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 50px;">
            <a href="organization.php" class="btn btn-primary" style="padding: 12px 28px;">
                <i class="fa-solid fa-sitemap"></i> ดูผังบริหารและคณะทำงาน
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
