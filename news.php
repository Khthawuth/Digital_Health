<?php
// news.php - รายการข่าวสารประชาสัมพันธ์ทั้งหมด
require_once __DIR__ . '/config/functions.php';

$pageTitle = "ข่าวสารประชาสัมพันธ์ - ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล";
$pageDesc = "ศูนย์รวมข่าวประชาสัมพันธ์ กิจกรรม ประกาศ และสาระความรู้ด้านนวัตกรรมสุขภาพดิจิทัล";

$pdo = getDBConnection();

// Filters & Pagination
$categorySlug = isset($_GET['category']) ? trim($_GET['category']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Fetch Categories for filter tabs
$categories = $pdo->query("SELECT * FROM news_categories ORDER BY id ASC")->fetchAll();

// Build query
$where = [];
$params = [];

if (!empty($categorySlug)) {
    $where[] = "c.slug = :catslug";
    $params[':catslug'] = $categorySlug;
}

if (!empty($search)) {
    $where[] = "(n.title LIKE :search OR n.excerpt LIKE :search OR n.content LIKE :search)";
    $params[':search'] = "%{$search}%";
}

$whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Count total
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM news n LEFT JOIN news_categories c ON n.category_id = c.id $whereSql");
$countStmt->execute($params);
$totalNews = $countStmt->fetchColumn();
$totalPages = ceil($totalNews / $perPage);

// Fetch data
$sql = "SELECT n.*, c.name as category_name, c.slug as category_slug 
        FROM news n 
        LEFT JOIN news_categories c ON n.category_id = c.id 
        $whereSql 
        ORDER BY n.is_featured DESC, n.published_at DESC 
        LIMIT :offset, :perPage";

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->bindValue(':perPage', (int)$perPage, PDO::PARAM_INT);
$stmt->execute();
$newsList = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb Header -->
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 45px 0;">
    <div class="container">
        <div style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: #94a3b8; margin-bottom: 10px;">
            <a href="index.php" style="color: #cbd5e1;">หน้าแรก</a>
            <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
            <span style="color: #38bdf8;">ข่าวสารประชาสัมพันธ์</span>
        </div>
        <h1 style="font-size: 32px; font-weight: 700; color: white; margin-bottom: 8px;">
            <i class="fa-regular fa-newspaper text-primary"></i> ข่าวสารและกิจกรรมประชาสัมพันธ์
        </h1>
        <p style="color: #cbd5e1; font-size: 15px;">
            ติดตามข่าวสาร ความก้าวหน้านวัตกรรมสุขภาพดิจิทัล และประกาศสำคัญจากกระทรวงสาธารณสุข
        </p>
    </div>
</div>

<div class="section">
    <div class="container">
        <!-- Search and Category Filters -->
        <div style="background: white; padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); margin-bottom: 30px;">
            <form action="news.php" method="GET" style="display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 20px;">
                <?php if (!empty($categorySlug)): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($categorySlug) ?>">
                <?php endif; ?>
                <div style="flex-grow: 1; position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="ค้นหาหัวข้อข่าว, กิจกรรม, นวัตกรรม..." 
                           class="form-control" style="padding-left: 44px; height: 46px; border-radius: 8px;">
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 0 24px; height: 46px;">
                    <i class="fa-solid fa-magnifying-glass"></i> ค้นหาข่าว
                </button>
                <?php if (!empty($search) || !empty($categorySlug)): ?>
                    <a href="news.php" class="btn btn-light" style="padding: 0 18px; height: 46px;">
                        <i class="fa-solid fa-rotate-left"></i> ล้างตัวกรอง
                    </a>
                <?php endif; ?>
            </form>

            <!-- Category Pills -->
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <a href="news.php<?= !empty($search) ? '?search=' . urlencode($search) : '' ?>" 
                   class="btn btn-sm <?= empty($categorySlug) ? 'btn-primary' : 'btn-light' ?>" style="border-radius: 20px;">
                    ทั้งหมด (<?= $totalNews ?>)
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="news.php?category=<?= urlencode($cat['slug']) ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                       class="btn btn-sm <?= $categorySlug === $cat['slug'] ? 'btn-primary' : 'btn-light' ?>" style="border-radius: 20px;">
                        <?= htmlspecialchars($cat['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- News Grid -->
        <?php if (!empty($newsList)): ?>
            <div class="news-grid">
                <?php foreach ($newsList as $item): ?>
                    <article class="news-card">
                        <div class="news-thumb-wrap">
                            <img src="<?= htmlspecialchars($item['image_url'] ?: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=800&q=80') ?>" 
                                 alt="<?= htmlspecialchars($item['title']) ?>" 
                                 class="news-thumb"
                                 loading="lazy">
                            <span class="news-cat-badge"><?= htmlspecialchars($item['category_name'] ?: 'ประชาสัมพันธ์') ?></span>
                            <?php if ($item['is_featured']): ?>
                                <span class="news-featured-tag"><i class="fa-solid fa-thumbtack"></i> ข่าวเด่น</span>
                            <?php endif; ?>
                        </div>
                        <div class="news-body">
                            <div class="news-meta">
                                <span><i class="fa-regular fa-calendar"></i> <?= thaiDate($item['published_at']) ?></span>
                                <span><i class="fa-regular fa-eye"></i> <?= number_format($item['views']) ?> ครั้ง</span>
                            </div>
                            <h3 class="news-title">
                                <a href="news-detail.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['title']) ?></a>
                            </h3>
                            <p class="news-excerpt"><?= htmlspecialchars($item['excerpt']) ?></p>
                            <div class="news-footer">
                                <span style="font-size:12px;color:#64748b;"><i class="fa-solid fa-user-pen"></i> <?= htmlspecialchars($item['author']) ?></span>
                                <a href="news-detail.php?id=<?= $item['id'] ?>" class="read-more-link">
                                    อ่านต่อ <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div style="display: flex; justify-content: center; gap: 8px; margin-top: 40px;">
                    <?php if ($page > 1): ?>
                        <a href="news.php?page=<?= $page - 1 ?><?= !empty($categorySlug) ? '&category=' . urlencode($categorySlug) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="btn btn-light btn-sm">
                            <i class="fa-solid fa-chevron-left"></i> ก่อนหน้า
                        </a>
                    <?php endif; ?>

                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <a href="news.php?page=<?= $p ?><?= !empty($categorySlug) ? '&category=' . urlencode($categorySlug) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                           class="btn btn-sm <?= $p === $page ? 'btn-primary' : 'btn-light' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="news.php?page=<?= $page + 1 ?><?= !empty($categorySlug) ? '&category=' . urlencode($categorySlug) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="btn btn-light btn-sm">
                            ถัดไป <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; background: white; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <i class="fa-regular fa-folder-open" style="font-size: 48px; color: #94a3b8; margin-bottom: 16px;"></i>
                <h3 style="font-size: 18px; color: #1e293b; margin-bottom: 8px;">ไม่พบข่าวสารตามเงื่อนไขที่ค้นหา</h3>
                <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">โปรดลองค้นหาด้วยคำอื่น หรือเลือกหมวดหมู่อื่น</p>
                <a href="news.php" class="btn btn-outline-primary btn-sm">ดูข่าวทั้งหมด</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
