<?php
// admin/news.php - จัดการข่าวสารและประชาสัมพันธ์
$pageTitle = "จัดการข่าวสารประชาสัมพันธ์";
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();

// Handle Delete News
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = :id");
    $stmt->execute([':id' => $id]);
    setFlash('success', 'ลบข่าวประชาสัมพันธ์เรียบร้อยแล้ว');
    header('Location: news.php');
    exit;
}

// Search and filter parameters
$search = trim($_GET['search'] ?? '');
$catId = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(n.title LIKE :s OR n.excerpt LIKE :s)";
    $params[':s'] = "%{$search}%";
}

if ($catId > 0) {
    $where[] = "n.category_id = :cat";
    $params[':cat'] = $catId;
}

$whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Fetch news
$sql = "SELECT n.*, c.name as category_name 
        FROM news n 
        LEFT JOIN news_categories c ON n.category_id = c.id 
        $whereSql 
        ORDER BY n.published_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$newsList = $stmt->fetchAll();

// Fetch categories for filter dropdown
$categories = $pdo->query("SELECT * FROM news_categories ORDER BY id ASC")->fetchAll();
?>

<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h3 class="admin-card-title"><i class="fa-regular fa-newspaper text-primary"></i> ข่าวสารและประชาสัมพันธ์ทั้งหมด (<?= count($newsList) ?> รายการ)</h3>
            <p style="font-size: 13px; color: #64748b; margin: 0;">เผยแพร่ข่าวสาร นโยบายสุขภาพดิจิทัล และกิจกรรมสาธารณสุข</p>
        </div>
        <a href="news-form.php" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> เขียนข่าวประชาสัมพันธ์ใหม่
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div style="padding: 16px 24px; background: #f8fafc; border-bottom: 1px solid var(--admin-border);">
        <form action="news.php" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex-grow: 1; min-width: 200px;">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       class="form-control" placeholder="ค้นหาจากหัวข้อข่าว หรือเนื้อหา...">
            </div>
            <div style="width: 220px;">
                <select name="cat_id" class="form-control" onchange="this.form.submit()">
                    <option value="0">-- ทุกหมวดหมู่ --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $catId == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-light btn-sm">
                <i class="fa-solid fa-magnifying-glass"></i> ค้นหา
            </button>
            <?php if (!empty($search) || $catId > 0): ?>
                <a href="news.php" class="btn btn-light btn-sm">ล้างตัวกรอง</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- News Table -->
    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 80px;">รูปปก</th>
                    <th>หัวข้อข่าว / คำโปรย</th>
                    <th>หมวดหมู่</th>
                    <th>ผู้เขียน</th>
                    <th>เข้าชม</th>
                    <th>วันที่เผยแพร่</th>
                    <th style="text-align: right;">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($newsList)): ?>
                    <?php foreach ($newsList as $item): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars((strpos($item['image_url'] ?? '', 'http') === 0 ? '' : '../') . ($item['image_url'] ?: 'assets/images/default-news.jpg')) ?>" 
                                     alt="" class="img-table-thumb" style="width: 70px; height: 48px;">
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 14px; color: #0f172a; margin-bottom: 3px;">
                                    <?php if ($item['is_featured']): ?>
                                        <span class="badge badge-warning" style="margin-right: 4px;"><i class="fa-solid fa-star"></i> เด่น</span>
                                    <?php endif; ?>
                                    <a href="news-form.php?id=<?= $item['id'] ?>" style="color: inherit;">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </a>
                                </div>
                                <div style="font-size: 12px; color: #64748b; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= htmlspecialchars($item['excerpt']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-primary"><?= htmlspecialchars($item['category_name'] ?: 'ทั่วไป') ?></span>
                            </td>
                            <td style="font-size: 12.5px; color: #475569;">
                                <?= htmlspecialchars($item['author']) ?>
                            </td>
                            <td style="font-weight: 600; font-size: 13px;">
                                <?= number_format($item['views']) ?>
                            </td>
                            <td style="font-size: 12px; color: #64748b;">
                                <?= thaiDate($item['published_at']) ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btns" style="justify-content: flex-end;">
                                    <a href="../news-detail.php?id=<?= $item['id'] ?>" target="_blank" class="btn-action" title="เปิดดูหน้าเว็บ">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="news-form.php?id=<?= $item['id'] ?>" class="btn-action btn-edit" title="แก้ไข">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <a href="news.php?action=delete&id=<?= $item['id'] ?>" 
                                       class="btn-action btn-delete confirm-delete" 
                                       data-title="<?= htmlspecialchars($item['title']) ?>" 
                                       title="ลบ">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                            <i class="fa-regular fa-folder-open" style="font-size: 36px; display: block; margin-bottom: 10px;"></i>
                            ยังไม่มีข้อมูลข่าวสาร หรือไม่พบข้อมูลตามคำค้นหา
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
