<?php
// news-detail.php - หน้ารายละเอียดข่าวสาร
require_once __DIR__ . '/config/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pdo = getDBConnection();

// Fetch article
$stmt = $pdo->prepare("SELECT n.*, c.name as category_name, c.slug as category_slug 
    FROM news n 
    LEFT JOIN news_categories c ON n.category_id = c.id 
    WHERE n.id = :id");
$stmt->execute([':id' => $id]);
$article = $stmt->fetch();

if (!$article) {
    setFlash('danger', 'ไม่พบข่าวสารที่คุณต้องการ');
    header('Location: news.php');
    exit;
}

// Increment view count
$pdo->prepare("UPDATE news SET views = views + 1 WHERE id = :id")->execute([':id' => $id]);
$article['views']++;

// Fetch related news in same category or latest
$stmtRelated = $pdo->prepare("SELECT id, title, image_url, published_at, views 
    FROM news WHERE id != :id AND category_id = :cat_id 
    ORDER BY published_at DESC LIMIT 4");
$stmtRelated->execute([':id' => $id, ':cat_id' => $article['category_id']]);
$relatedNews = $stmtRelated->fetchAll();

if (empty($relatedNews)) {
    $stmtRelated = $pdo->prepare("SELECT id, title, image_url, published_at, views 
        FROM news WHERE id != :id 
        ORDER BY published_at DESC LIMIT 4");
    $stmtRelated->execute([':id' => $id]);
    $relatedNews = $stmtRelated->fetchAll();
}

$pageTitle = $article['title'] . " - ข่าวสุขภาพดิจิทัล";
$pageDesc = mb_substr(strip_tags($article['excerpt'] ?: $article['content']), 0, 150);

include __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb Header -->
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 40px 0;">
    <div class="container">
        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #94a3b8; margin-bottom: 12px; flex-wrap: wrap;">
            <a href="index.php" style="color: #cbd5e1;">หน้าแรก</a>
            <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i>
            <a href="news.php" style="color: #cbd5e1;">ข่าวประชาสัมพันธ์</a>
            <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i>
            <a href="news.php?category=<?= urlencode($article['category_slug'] ?? '') ?>" style="color: #38bdf8;"><?= htmlspecialchars($article['category_name'] ?? 'ข่าวสาร') ?></a>
        </div>
        <h1 style="font-size: 26px; font-weight: 700; color: white; line-height: 1.4; max-width: 900px;">
            <?= htmlspecialchars($article['title']) ?>
        </h1>
        <div style="display: flex; align-items: center; gap: 20px; font-size: 13.5px; color: #cbd5e1; margin-top: 15px; flex-wrap: wrap;">
            <span><i class="fa-regular fa-calendar text-primary"></i> เผยแพร่เมื่อ: <?= thaiDate($article['published_at'], true) ?></span>
            <span><i class="fa-solid fa-user-pen text-primary"></i> โดย: <?= htmlspecialchars($article['author']) ?></span>
            <span><i class="fa-regular fa-eye text-primary"></i> เข้าชม: <?= number_format($article['views']) ?> ครั้ง</span>
        </div>
    </div>
</div>

<div class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 2.2fr 1fr; gap: 40px;">
            <!-- Main Article Column -->
            <main>
                <div style="background: white; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); overflow: hidden; padding: 30px;">
                    <?php if (!empty($article['image_url'])): ?>
                        <div style="margin: -30px -30px 25px -30px; max-height: 440px; overflow: hidden; background: #e2e8f0;">
                            <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($article['excerpt'])): ?>
                        <div style="font-size: 16px; font-weight: 500; color: #0f766e; background: #f0fdfa; padding: 18px 22px; border-left: 4px solid #0d9488; border-radius: 6px; margin-bottom: 25px; line-height: 1.7;">
                            <?= htmlspecialchars($article['excerpt']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Article Body Content -->
                    <div class="article-content" style="font-size: 15.5px; line-height: 1.8; color: #334155;">
                        <?= $article['content'] ?>
                    </div>

                    <!-- Share and Tags -->
                    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-weight: 600; font-size: 13.5px; color: #1e293b;">แชร์ข่าวนี้:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" target="_blank" class="btn btn-sm btn-light" style="color:#1877f2;"><i class="fa-brands fa-facebook"></i> Facebook</a>
                            <a href="https://line.me/R/msg/text/?<?= urlencode($article['title'] . " " . "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" target="_blank" class="btn btn-sm btn-light" style="color:#06c755;"><i class="fa-brands fa-line"></i> LINE</a>
                            <button onclick="navigator.clipboard.writeText(window.location.href); alert('คัดลอกลิงก์เรียบร้อยแล้ว!');" class="btn btn-sm btn-light">
                                <i class="fa-regular fa-copy"></i> คัดลอกลิงก์
                            </button>
                        </div>
                        <a href="news.php" class="btn btn-outline-primary btn-sm">
                            <i class="fa-solid fa-arrow-left"></i> กลับไปหน้ารวมข่าว
                        </a>
                    </div>
                </div>
            </main>

            <!-- Sidebar -->
            <aside>
                <!-- Related News -->
                <div style="background: white; border-radius: var(--radius-md); border: 1px solid var(--border-color); padding: 24px; box-shadow: var(--shadow-sm); margin-bottom: 30px;">
                    <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin-bottom: 18px; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-newspaper text-primary"></i> ข่าวที่เกี่ยวข้อง
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <?php foreach ($relatedNews as $rn): ?>
                            <a href="news-detail.php?id=<?= $rn['id'] ?>" style="display: flex; gap: 12px; text-decoration: none; group">
                                <img src="<?= htmlspecialchars($rn['image_url'] ?: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=400&q=80') ?>" 
                                     alt="" style="width: 75px; height: 60px; object-fit: cover; border-radius: 6px; flex-shrink: 0;">
                                <div style="flex-grow: 1;">
                                    <h4 style="font-size: 13.5px; font-weight: 600; color: #1e293b; line-height: 1.4; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?= htmlspecialchars($rn['title']) ?>
                                    </h4>
                                    <span style="font-size: 11.5px; color: #94a3b8;"><i class="fa-regular fa-calendar"></i> <?= thaiDate($rn['published_at']) ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Emergency Contact Hotline Widget -->
                <div style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); color: white; padding: 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-md);">
                    <div style="font-size: 26px; margin-bottom: 10px;"><i class="fa-solid fa-headset"></i></div>
                    <h4 style="font-size: 17px; font-weight: 700; color: white; margin-bottom: 6px;">ศูนย์บริการสุขภาพดิจิทัล</h4>
                    <p style="font-size: 13px; color: #ccfbf1; margin-bottom: 16px; line-height: 1.5;">
                        ต้องการคำปรึกษาการใช้งานระบบ หรือรายงานปัญหาข้อมูลสุขภาพ ติดต่อสายด่วนได้ทุกวันตลอด 24 ชั่วโมง
                    </p>
                    <a href="tel:025903000" class="btn btn-light" style="width: 100%; font-weight: 700; color: #0f766e;">
                        <i class="fa-solid fa-phone"></i> โทร 02-590-3000
                    </a>
                </div>
            </aside>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
