<?php
// admin/news-form.php - ฟอร์มเพิ่ม/แก้ไขข่าวประชาสัมพันธ์
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$news = null;
$pageTitle = $id > 0 ? "แก้ไขข่าวประชาสัมพันธ์" : "เพิ่มข่าวประชาสัมพันธ์ใหม่";

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $news = $stmt->fetch();
    if (!$news) {
        setFlash('danger', 'ไม่พบข่าวสารที่ต้องการแก้ไข');
        header('Location: news.php');
        exit;
    }
}

$categories = $pdo->query("SELECT * FROM news_categories ORDER BY id ASC")->fetchAll();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 1);
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author = trim($_POST['author'] ?? 'ฝ่ายประชาสัมพันธ์สุขภาพดิจิทัล');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $imageUrl = trim($_POST['image_url'] ?? ($news['image_url'] ?? ''));

    // Handle File Upload
    $uploadResult = handleImageUpload('news_image', 'news');
    if ($uploadResult) {
        if (isset($uploadResult['error'])) {
            $error = $uploadResult['error'];
        } elseif (isset($uploadResult['path'])) {
            $imageUrl = $uploadResult['path'];
        }
    }

    if (empty($title)) {
        $error = 'กรุณาระบุหัวข้อข่าว';
    } elseif (empty($content)) {
        $error = 'กรุณากรอกเนื้อหาข่าวสาร';
    }

    if (empty($error)) {
        if ($id > 0) {
            // Update
            $sql = "UPDATE news SET category_id = :cat, title = :title, excerpt = :excerpt, 
                    content = :content, image_url = :img, author = :author, is_featured = :feat 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':cat' => $categoryId,
                ':title' => $title,
                ':excerpt' => $excerpt,
                ':content' => $content,
                ':img' => $imageUrl,
                ':author' => $author,
                ':feat' => $isFeatured,
                ':id' => $id
            ]);
            setFlash('success', 'บันทึกการแก้ไขข่าวประชาสัมพันธ์เรียบร้อยแล้ว');
        } else {
            // Insert
            $sql = "INSERT INTO news (category_id, title, excerpt, content, image_url, author, is_featured, views) 
                    VALUES (:cat, :title, :excerpt, :content, :img, :author, :feat, 0)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':cat' => $categoryId,
                ':title' => $title,
                ':excerpt' => $excerpt,
                ':content' => $content,
                ':img' => $imageUrl,
                ':author' => $author,
                ':feat' => $isFeatured
            ]);
            setFlash('success', 'เผยแพร่ข่าวประชาสัมพันธ์ใหม่เรียบร้อยแล้ว');
        }
        header('Location: news.php');
        exit;
    }
}
?>

<div style="max-width: 1040px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 700; color: #0f172a;"><?= $pageTitle ?></h2>
            <p style="font-size: 13.5px; color: #64748b; margin: 0;">กรอกข้อมูลข่าวสารและรายละเอียดให้ครบถ้วนเพื่อเผยแพร่บนเว็บไซต์</p>
        </div>
        <a href="news.php" class="btn btn-light btn-sm">
            <i class="fa-solid fa-arrow-left"></i> ย้อนกลับไปรายการข่าว
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="news-form.php<?= $id > 0 ? '?id=' . $id : '' ?>" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <!-- Left Main Column -->
            <div>
                <div class="form-box">
                    <h4 class="form-box-title"><i class="fa-solid fa-file-lines text-primary"></i> ข้อมูลเนื้อหาข่าว</h4>
                    
                    <div class="form-group">
                        <label class="form-label">หัวข้อข่าวประชาสัมพันธ์ <span style="color:red;">*</span></label>
                        <input type="text" name="title" required 
                               value="<?= htmlspecialchars($news['title'] ?? '') ?>" 
                               class="form-control" placeholder="ระบุหัวข้อข่าวที่ดึงดูดและเข้าใจง่าย...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">คำโปรย / สรุปเนื้อหาเบื้องต้น (Excerpt)</label>
                        <textarea name="excerpt" rows="3" class="form-control" 
                                  placeholder="สรุปประเด็นสำคัญของข่าว 2-3 บรรทัด สำหรับแสดงในการ์ดหน้ารวมข่าว..."><?= htmlspecialchars($news['excerpt'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">เนื้อหาข่าวฉบับเต็ม <span style="color:red;">*</span> (รองรับแท็ก HTML เช่น &lt;p&gt;, &lt;h4&gt;, &lt;ul&gt;, &lt;li&gt;)</label>
                        <textarea name="content" required rows="14" class="form-control" 
                                  style="font-family: monospace; font-size: 13.5px; line-height: 1.6;"
                                  placeholder="พิมพ์เนื้อหาข่าวประชาสัมพันธ์ที่นี่..."><?= htmlspecialchars($news['content'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column -->
            <div>
                <!-- Publishing Options -->
                <div class="form-box">
                    <h4 class="form-box-title"><i class="fa-solid fa-gear text-primary"></i> ตั้งค่าการเผยแพร่</h4>
                    
                    <div class="form-group">
                        <label class="form-label">หมวดหมู่ข่าว <span style="color:red;">*</span></label>
                        <select name="category_id" required class="form-control">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($news['category_id'] ?? 1) == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">ชื่อผู้เขียน / แหล่งที่มา</label>
                        <input type="text" name="author" 
                               value="<?= htmlspecialchars($news['author'] ?? 'ฝ่ายประชาสัมพันธ์สุขภาพดิจิทัล') ?>" 
                               class="form-control" placeholder="ฝ่ายประชาสัมพันธ์สุขภาพดิจิทัล">
                    </div>

                    <div class="form-group" style="padding-top: 10px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="is_featured" <?= !empty($news['is_featured']) ? 'checked' : '' ?>>
                            <span><strong>ปักหมุดเป็นข่าวเด่น (Featured)</strong></span>
                        </label>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="form-box">
                    <h4 class="form-box-title"><i class="fa-regular fa-image text-primary"></i> รูปภาพหน้าปกข่าว</h4>

                    <div class="form-group">
                        <label class="form-label">อัปโหลดไฟล์ภาพจากเครื่อง</label>
                        <input type="file" name="news_image" accept="image/*" class="form-control image-upload-input" data-preview-target="newsPreviewImg">
                    </div>

                    <div class="form-group">
                        <label class="form-label">หรือ ระบุลิงก์ URL ของรูปภาพ</label>
                        <input type="text" name="image_url" 
                               value="<?= htmlspecialchars($news['image_url'] ?? '') ?>" 
                               class="form-control" placeholder="https://images.unsplash.com/...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">ตัวอย่างรูปภาพ</label>
                        <div class="preview-box">
                            <?php 
                                $currentImg = '';
                                if ($news && !empty($news['image_url'])) {
                                    $currentImg = (strpos($news['image_url'], 'http') === 0 ? '' : '../') . $news['image_url'];
                                }
                            ?>
                            <img id="newsPreviewImg" src="<?= htmlspecialchars($currentImg) ?>" 
                                 alt="Preview" style="<?= empty($currentImg) ? 'display:none;' : '' ?>">
                            <div class="preview-placeholder" style="<?= !empty($currentImg) ? 'display:none;' : '' ?>color:#94a3b8;font-size:13px;text-align:center;">
                                <i class="fa-regular fa-image" style="font-size:32px;display:block;margin-bottom:6px;"></i>
                                เลือกไฟล์หรือระบุ URL เพื่อดูภาพ
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 15px; font-weight: 600;">
                    <i class="fa-solid fa-floppy-disk"></i> <?= $id > 0 ? 'บันทึกการแก้ไขข่าว' : 'เผยแพร่ข่าวประชาสัมพันธ์' ?>
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
