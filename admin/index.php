<?php
// admin/index.php - แดชบอร์ดภาพรวมผู้ดูแลระบบ
$pageTitle = "แดชบอร์ดภาพรวม (Dashboard)";
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();

// Fetch Metrics
$totalNews = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$totalViews = $pdo->query("SELECT COALESCE(SUM(views), 0) FROM news")->fetchColumn();
$activeBanners = $pdo->query("SELECT COUNT(*) FROM hero_banners WHERE is_active = 1")->fetchColumn();
$totalExecs = $pdo->query("SELECT COUNT(*) FROM executives")->fetchColumn();
$todayDutys = $pdo->query("SELECT COUNT(*) FROM duty_schedules WHERE duty_date = CURDATE()")->fetchColumn();
$unreadMessages = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'")->fetchColumn();

// Fetch Recent News
$stmtRecentNews = $pdo->query("SELECT n.*, c.name as category_name 
    FROM news n 
    LEFT JOIN news_categories c ON n.category_id = c.id 
    ORDER BY n.id DESC LIMIT 5");
$recentNews = $stmtRecentNews->fetchAll();

// Fetch Active Banners
$stmtBanners = $pdo->query("SELECT * FROM hero_banners ORDER BY sort_order ASC, id DESC LIMIT 5");
$banners = $stmtBanners->fetchAll();
?>

<!-- Metrics Summary Cards -->
<div class="metrics-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
    <div class="metric-card">
        <div class="metric-icon-wrap metric-icon-teal">
            <i class="fa-regular fa-newspaper"></i>
        </div>
        <div>
            <div class="metric-value"><?= number_format($totalNews) ?></div>
            <div class="metric-label">ข่าวสารประชาสัมพันธ์</div>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon-wrap metric-icon-blue">
            <i class="fa-solid fa-calendar-days"></i>
        </div>
        <div>
            <div class="metric-value"><?= number_format($todayDutys) ?></div>
            <div class="metric-label">เวรปฏิบัติหน้าที่วันนี้</div>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon-wrap metric-icon-purple">
            <i class="fa-solid fa-images"></i>
        </div>
        <div>
            <div class="metric-value"><?= number_format($activeBanners) ?></div>
            <div class="metric-label">ฮีโร่แบนเนอร์ที่เปิดใช้</div>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon-wrap metric-icon-amber">
            <i class="fa-solid fa-sitemap"></i>
        </div>
        <div>
            <div class="metric-value"><?= number_format($totalExecs) ?></div>
            <div class="metric-label">ผู้บริหารในผังองค์กร</div>
        </div>
    </div>
</div>

<!-- Quick Action Shortcuts -->
<div style="background: white; padding: 20px 24px; border-radius: var(--admin-radius); border: 1px solid var(--admin-border); margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
    <div>
        <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 3px;">เมนูด่วนสำหรับผู้ดูแลระบบ</h3>
        <p style="font-size: 13px; color: #64748b; margin: 0;">เพิ่มเนื้อหา ประชาสัมพันธ์ข้อมูล อัปเดตตารางเวร หรือปรับแต่งเว็บไซต์</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="duty-schedules.php" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-calendar-plus"></i> จัดการตารางเวรเจ้าหน้าที่
        </a>
        <a href="news-form.php" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-plus"></i> เพิ่มข่าวประชาสัมพันธ์
        </a>
        <a href="banners.php#addBannerForm" class="btn btn-light btn-sm">
            <i class="fa-solid fa-image"></i> จัดการฮีโร่แบนเนอร์
        </a>
        <a href="executive-form.php" class="btn btn-light btn-sm">
            <i class="fa-solid fa-user-plus"></i> เพิ่มผู้บริหารในผัง
        </a>
    </div>
</div>

<!-- Two-Column Tables Grid -->
<div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 30px;">
    <!-- Recent News Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fa-regular fa-newspaper text-primary"></i> ข่าวประชาสัมพันธ์ล่าสุด</h3>
            <a href="news.php" class="btn btn-light btn-sm">ดูทั้งหมด</a>
        </div>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">รูป</th>
                        <th>หัวข้อข่าว</th>
                        <th>หมวดหมู่</th>
                        <th>เข้าชม</th>
                        <th style="text-align: right;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentNews)): ?>
                        <?php foreach ($recentNews as $n): ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars($n['image_url'] ?: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=200&q=80') ?>" 
                                         alt="" class="img-table-thumb">
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 13.5px; color: #0f172a; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?= htmlspecialchars($n['title']) ?>
                                    </div>
                                    <div style="font-size: 11.5px; color: #94a3b8; margin-top: 2px;">
                                        <?= thaiDate($n['published_at']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-secondary"><?= htmlspecialchars($n['category_name'] ?: 'ทั่วไป') ?></span>
                                </td>
                                <td><?= number_format($n['views']) ?></td>
                                <td style="text-align: right;">
                                    <div class="action-btns" style="justify-content: flex-end;">
                                        <a href="news-form.php?id=<?= $n['id'] ?>" class="btn-action btn-edit" title="แก้ไข">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        <a href="../news-detail.php?id=<?= $n['id'] ?>" target="_blank" class="btn-action" title="ดูหน้าเว็บ">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">ยังไม่มีข่าวสารในระบบ</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Active Hero Banners Preview Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fa-solid fa-images text-primary"></i> ฮีโร่แบนเนอร์หน้าแรก</h3>
            <a href="banners.php" class="btn btn-light btn-sm">จัดการ</a>
        </div>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">รูปภาพ</th>
                        <th>หัวข้อแบนเนอร์</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($banners)): ?>
                        <?php foreach ($banners as $b): ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars($b['image_url']) ?>" alt="" class="img-table-thumb">
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 13px; color: #0f172a; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?= htmlspecialchars($b['title']) ?>
                                    </div>
                                    <div style="font-size: 11.5px; color: #64748b;">ลำดับ: <?= $b['sort_order'] ?></div>
                                </td>
                                <td>
                                    <?php if ($b['is_active']): ?>
                                        <span class="badge badge-success"><i class="fa-solid fa-check"></i> เปิด</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">ปิด</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align: center; padding: 20px;">ยังไม่มีแบนเนอร์ในระบบ</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
