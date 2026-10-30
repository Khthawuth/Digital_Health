<?php
// organization.php - ผังโครงสร้างผู้บริหาร ศูนย์สุขภาพดิจิทัล
require_once __DIR__ . '/config/functions.php';

$pageTitle = "ผังโครงสร้างผู้บริหาร - ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล";
$pageDesc = "ผังโครงสร้างการบริหารงาน คณะผู้บริหารระดับสูง และหัวหน้ากลุ่มงาน ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล";

$pdo = getDBConnection();

// Fetch executives grouped by level
$stmtL1 = $pdo->query("SELECT * FROM executives WHERE level = 1 ORDER BY sort_order ASC, id ASC");
$leaders = $stmtL1->fetchAll();

$stmtL2 = $pdo->query("SELECT * FROM executives WHERE level = 2 ORDER BY sort_order ASC, id ASC");
$deputies = $stmtL2->fetchAll();

$stmtL3 = $pdo->query("SELECT * FROM executives WHERE level = 3 ORDER BY sort_order ASC, id ASC");
$heads = $stmtL3->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb Header -->
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 45px 0;">
    <div class="container">
        <div style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: #94a3b8; margin-bottom: 10px;">
            <a href="index.php" style="color: #cbd5e1;">หน้าแรก</a>
            <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
            <span style="color: #38bdf8;">ผังบริหาร</span>
        </div>
        <h1 style="font-size: 32px; font-weight: 700; color: white; margin-bottom: 8px;">
            <i class="fa-solid fa-sitemap text-primary"></i> ผังโครงสร้างการบริหารงาน
        </h1>
        <p style="color: #cbd5e1; font-size: 15px; max-width: 720px;">
            โครงสร้างคณะผู้บริหารและหัวหน้ากลุ่มงาน ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล กระทรวงสาธารณสุข
        </p>
    </div>
</div>

<!-- Organization Chart Content -->
<div class="section">
    <div class="container">
        <div class="org-chart-wrapper">
            <!-- Level 1: ผู้บริหารสูงสุด -->
            <div class="org-level-title">
                <span><i class="fa-solid fa-crown text-warning"></i> ผู้บริหารสูงสุด (Executive Director)</span>
            </div>
            
            <div class="org-row-level1">
                <?php foreach ($leaders as $ex): ?>
                    <div class="exec-card leader">
                        <div class="exec-avatar-wrap">
                            <img src="<?= htmlspecialchars($ex['image_url'] ?: 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=600&q=80') ?>" 
                                 alt="<?= htmlspecialchars($ex['name']) ?>" 
                                 class="exec-avatar">
                        </div>
                        <h3 class="exec-name"><?= htmlspecialchars($ex['name']) ?></h3>
                        <div class="exec-position"><?= htmlspecialchars($ex['position']) ?></div>
                        <div class="exec-dept"><?= htmlspecialchars($ex['department']) ?></div>
                        <?php if (!empty($ex['bio'])): ?>
                            <p style="font-size: 13px; color: #64748b; margin-bottom: 12px; line-height: 1.5;">
                                <?= htmlspecialchars($ex['bio']) ?>
                            </p>
                        <?php endif; ?>
                        <div class="exec-contact-info">
                            <?php if (!empty($ex['email'])): ?>
                                <a href="mailto:<?= htmlspecialchars($ex['email']) ?>"><i class="fa-regular fa-envelope"></i> <?= htmlspecialchars($ex['email']) ?></a>
                            <?php endif; ?>
                            <?php if (!empty($ex['phone'])): ?>
                                <span><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($ex['phone']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Connecting Tree Vertical Line -->
            <div style="width: 2px; height: 35px; background: #0d9488; margin: -20px auto 25px auto;"></div>

            <!-- Level 2: รองผู้อำนวยการ -->
            <div class="org-level-title">
                <span><i class="fa-solid fa-users-gear text-primary"></i> คณะรองผู้อำนวยการ (Deputy Directors)</span>
            </div>

            <div class="org-row-level2">
                <?php foreach ($deputies as $ex): ?>
                    <div class="exec-card deputy">
                        <div class="exec-avatar-wrap">
                            <img src="<?= htmlspecialchars($ex['image_url'] ?: 'https://images.unsplash.com/photo-1594824813583-b9b25a9526f8?auto=format&fit=crop&w=600&q=80') ?>" 
                                 alt="<?= htmlspecialchars($ex['name']) ?>" 
                                 class="exec-avatar">
                        </div>
                        <h3 class="exec-name"><?= htmlspecialchars($ex['name']) ?></h3>
                        <div class="exec-position"><?= htmlspecialchars($ex['position']) ?></div>
                        <div class="exec-dept"><?= htmlspecialchars($ex['department']) ?></div>
                        <?php if (!empty($ex['bio'])): ?>
                            <p style="font-size: 12.5px; color: #64748b; margin-bottom: 10px; line-height: 1.5;">
                                <?= htmlspecialchars($ex['bio']) ?>
                            </p>
                        <?php endif; ?>
                        <div class="exec-contact-info">
                            <?php if (!empty($ex['email'])): ?>
                                <a href="mailto:<?= htmlspecialchars($ex['email']) ?>"><i class="fa-regular fa-envelope"></i> <?= htmlspecialchars($ex['email']) ?></a>
                            <?php endif; ?>
                            <?php if (!empty($ex['phone'])): ?>
                                <span><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($ex['phone']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Connecting Tree Vertical Line -->
            <div style="width: 2px; height: 35px; background: #0284c7; margin: -20px auto 25px auto;"></div>

            <!-- Level 3: หัวหน้ากลุ่มงาน / ฝ่าย -->
            <div class="org-level-title">
                <span><i class="fa-solid fa-network-wired text-secondary"></i> หัวหน้ากลุ่มงานและฝ่ายบริหาร (Department Heads)</span>
            </div>

            <div class="org-row-level3">
                <?php foreach ($heads as $ex): ?>
                    <div class="exec-card head">
                        <div class="exec-avatar-wrap">
                            <img src="<?= htmlspecialchars($ex['image_url'] ?: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=600&q=80') ?>" 
                                 alt="<?= htmlspecialchars($ex['name']) ?>" 
                                 class="exec-avatar">
                        </div>
                        <h3 class="exec-name" style="font-size: 15px;"><?= htmlspecialchars($ex['name']) ?></h3>
                        <div class="exec-position" style="font-size: 12.5px; min-height: 34px;"><?= htmlspecialchars($ex['position']) ?></div>
                        <div class="exec-dept" style="font-size: 11.5px;"><?= htmlspecialchars($ex['department']) ?></div>
                        <div class="exec-contact-info">
                            <?php if (!empty($ex['email'])): ?>
                                <a href="mailto:<?= htmlspecialchars($ex['email']) ?>" style="font-size:11.5px;"><i class="fa-regular fa-envelope"></i> <?= htmlspecialchars($ex['email']) ?></a>
                            <?php endif; ?>
                            <?php if (!empty($ex['phone'])): ?>
                                <span style="font-size:11.5px;"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($ex['phone']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Vision and Governance Box -->
        <div style="margin-top: 40px; background: #f0fdfa; border: 1px solid #ccfbf1; padding: 25px; border-radius: var(--radius-md); display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
            <div style="width: 50px; height: 50px; border-radius: 12px; background: #0d9488; color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div style="flex-grow: 1;">
                <h4 style="font-size: 17px; color: #0f766e; margin-bottom: 4px;">หลักธรรมาภิบาลและการบริหารจัดการข้อมูลสุขภาพ</h4>
                <p style="font-size: 13.5px; color: #334155; margin: 0;">
                    คณะผู้บริหารศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล ปฏิบัติหน้าที่ตามหลักธรรมาภิบาลข้อมูลภาครัฐ (Data Governance) ยึดมั่นในความโปร่งใส ความถูกต้องของข้อมูล และความมั่นคงปลอดภัยสารสนเทศสูงสุดเพื่อประโยชน์ของประชาชน
                </p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
