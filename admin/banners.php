<?php
// admin/banners.php - จัดการฮีโร่แบนเนอร์ (Hero Banners)
$pageTitle = "จัดการฮีโร่แบนเนอร์ (Hero Banners)";
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();

// Handle Delete Banner
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM hero_banners WHERE id = :id");
    $stmt->execute([':id' => $id]);
    setFlash('success', 'ลบฮีโร่แบนเนอร์เรียบร้อยแล้ว');
    header('Location: banners.php');
    exit;
}

// Handle Toggle Active Status
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE hero_banners SET is_active = IF(is_active = 1, 0, 1) WHERE id = :id");
    $stmt->execute([':id' => $id]);
    setFlash('info', 'เปลี่ยนสถานะการแสดงผลแบนเนอร์เรียบร้อยแล้ว');
    header('Location: banners.php');
    exit;
}

// Handle Create / Update Banner Form
$editBanner = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM hero_banners WHERE id = :id");
    $stmt->execute([':id' => (int)$_GET['id']]);
    $editBanner = $stmt->fetch();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bannerId = !empty($_POST['banner_id']) ? (int)$_POST['banner_id'] : null;
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $badgeText = trim($_POST['badge_text'] ?? 'ข่าวประชาสัมพันธ์');
    $buttonText = trim($_POST['button_text'] ?? 'อ่านเพิ่มเติม');
    $buttonLink = trim($_POST['button_link'] ?? '#');
    $imageUrl = trim($_POST['image_url'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    // Handle File Upload if provided
    $uploadResult = handleImageUpload('banner_file', 'banners');
    if ($uploadResult) {
        if (isset($uploadResult['error'])) {
            $error = $uploadResult['error'];
        } elseif (isset($uploadResult['path'])) {
            $imageUrl = $uploadResult['path'];
        }
    }

    if (empty($imageUrl) && !$bannerId) {
        $error = 'กรุณาอัปโหลดรูปภาพหรือระบุ URL รูปภาพสำหรับแบนเนอร์';
    }

    if (empty($error)) {
        if ($bannerId) {
            // Update
            if (!empty($imageUrl)) {
                $sql = "UPDATE hero_banners SET title = :title, subtitle = :subtitle, badge_text = :badge, 
                        button_text = :btn_txt, button_link = :btn_lnk, image_url = :img, sort_order = :sort, is_active = :active 
                        WHERE id = :id";
                $params = [
                    ':title' => $title, ':subtitle' => $subtitle, ':badge' => $badgeText,
                    ':btn_txt' => $buttonText, ':btn_lnk' => $buttonLink, ':img' => $imageUrl,
                    ':sort' => $sortOrder, ':active' => $isActive, ':id' => $bannerId
                ];
            } else {
                $sql = "UPDATE hero_banners SET title = :title, subtitle = :subtitle, badge_text = :badge, 
                        button_text = :btn_txt, button_link = :btn_lnk, sort_order = :sort, is_active = :active 
                        WHERE id = :id";
                $params = [
                    ':title' => $title, ':subtitle' => $subtitle, ':badge' => $badgeText,
                    ':btn_txt' => $buttonText, ':btn_lnk' => $buttonLink,
                    ':sort' => $sortOrder, ':active' => $isActive, ':id' => $bannerId
                ];
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            setFlash('success', 'บันทึกการแก้ไขฮีโร่แบนเนอร์เรียบร้อยแล้ว');
        } else {
            // Insert
            $sql = "INSERT INTO hero_banners (title, subtitle, badge_text, button_text, button_link, image_url, sort_order, is_active) 
                    VALUES (:title, :subtitle, :badge, :btn_txt, :btn_lnk, :img, :sort, :active)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title' => $title, ':subtitle' => $subtitle, ':badge' => $badgeText,
                ':btn_txt' => $buttonText, ':btn_lnk' => $buttonLink, ':img' => $imageUrl,
                ':sort' => $sortOrder, ':active' => $isActive
            ]);
            setFlash('success', 'เพิ่มฮีโร่แบนเนอร์ใหม่เรียบร้อยแล้ว');
        }
        header('Location: banners.php');
        exit;
    }
}

// Fetch all banners
$banners = $pdo->query("SELECT * FROM hero_banners ORDER BY sort_order ASC, id DESC")->fetchAll();
?>

<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px;" id="addBannerForm">
    <!-- Banners List -->
    <div>
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title"><i class="fa-solid fa-list-check text-primary"></i> รายการฮีโร่แบนเนอร์ทั้งหมด (<?= count($banners) ?>)</h3>
                <span style="font-size: 12px; color: #64748b;">แบนเนอร์จะแสดงแบบ Carousel สไลด์หน้าแรก</span>
            </div>

            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">รูปภาพ</th>
                            <th>ข้อมูลแบนเนอร์</th>
                            <th>ลำดับ</th>
                            <th>สถานะ</th>
                            <th style="text-align: right;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($banners)): ?>
                            <?php foreach ($banners as $b): ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars((strpos($b['image_url'], 'http') === 0 ? '' : '../') . $b['image_url']) ?>" 
                                             alt="" class="img-table-thumb" style="width: 80px; height: 50px;">
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; font-size: 14px; color: #0f172a; margin-bottom: 3px;">
                                            <?= htmlspecialchars($b['title']) ?>
                                        </div>
                                        <div style="font-size: 12px; color: #64748b;">
                                            <span class="badge badge-secondary"><?= htmlspecialchars($b['badge_text']) ?></span>
                                            ปุ่ม: <em><?= htmlspecialchars($b['button_text']) ?></em> &rarr; <?= htmlspecialchars($b['button_link']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-weight: 600; font-family: monospace;"><?= $b['sort_order'] ?></span>
                                    </td>
                                    <td>
                                        <a href="banners.php?action=toggle&id=<?= $b['id'] ?>" title="คลิกเพื่อเปลี่ยนสถานะ">
                                            <?php if ($b['is_active']): ?>
                                                <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> เปิดใช้งาน</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary"><i class="fa-solid fa-circle-xmark"></i> ปิด</span>
                                            <?php endif; ?>
                                        </a>
                                    </td>
                                    <td style="text-align: right;">
                                        <div class="action-btns" style="justify-content: flex-end;">
                                            <a href="banners.php?action=edit&id=<?= $b['id'] ?>#addBannerForm" class="btn-action btn-edit" title="แก้ไข">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <a href="banners.php?action=delete&id=<?= $b['id'] ?>" 
                                               class="btn-action btn-delete confirm-delete" 
                                               data-title="<?= htmlspecialchars($b['title']) ?>" 
                                               title="ลบ">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align: center; padding: 25px;">ยังไม่มีฮีโร่แบนเนอร์ กรุณาเพิ่มรายการแรกด้านขวามือ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add / Edit Banner Form Card -->
    <div>
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="fa-solid fa-<?= $editBanner ? 'pen-to-square' : 'plus-circle' ?> text-primary"></i> 
                    <?= $editBanner ? 'แก้ไขฮีโร่แบนเนอร์' : 'เพิ่มฮีโร่แบนเนอร์ใหม่' ?>
                </h3>
                <?php if ($editBanner): ?>
                    <a href="banners.php" class="btn btn-light btn-sm"><i class="fa-solid fa-xmark"></i> ยกเลิกการแก้ไข</a>
                <?php endif; ?>
            </div>

            <div style="padding: 24px;">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="banners.php" method="POST" enctype="multipart/form-data">
                    <?php if ($editBanner): ?>
                        <input type="hidden" name="banner_id" value="<?= $editBanner['id'] ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label">หัวข้อหลักบนแบนเนอร์ (Title) <span style="font-weight: normal; color: #64748b;">(เว้นว่างได้ หากเป็นรูปภาพที่มีข้อความอยู่แล้ว)</span></label>
                        <input type="text" name="title" 
                               value="<?= htmlspecialchars($editBanner['title'] ?? '') ?>" 
                               class="form-control" placeholder="ปล่อยว่างได้ หากไม่ต้องการให้มีข้อความและปุ่มบังรูปภาพ">
                    </div>

                    <div class="form-group">
                        <label class="form-label">คำบรรยายสั้น (Subtitle)</label>
                        <textarea name="subtitle" rows="3" class="form-control" 
                                  placeholder="คำบรรยายขยายความบนแบนเนอร์..."><?= htmlspecialchars($editBanner['subtitle'] ?? '') ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group">
                            <label class="form-label">ป้ายกำกับ (Badge)</label>
                            <input type="text" name="badge_text" 
                                   value="<?= htmlspecialchars($editBanner['badge_text'] ?? 'ข่าวประชาสัมพันธ์') ?>" 
                                   class="form-control" placeholder="เช่น ข่าวประชาสัมพันธ์">
                        </div>

                        <div class="form-group">
                            <label class="form-label">ลำดับแสดงผล (Sort Order)</label>
                            <input type="number" name="sort_order" 
                                   value="<?= htmlspecialchars($editBanner['sort_order'] ?? '0') ?>" 
                                   class="form-control" placeholder="0">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group">
                            <label class="form-label">ข้อความบนปุ่มกด</label>
                            <input type="text" name="button_text" 
                                   value="<?= htmlspecialchars($editBanner['button_text'] ?? 'อ่านเพิ่มเติม') ?>" 
                                   class="form-control" placeholder="อ่านเพิ่มเติม">
                        </div>

                        <div class="form-group">
                            <label class="form-label">ลิงก์ปลายทาง (Button Link)</label>
                            <input type="text" name="button_link" 
                                   value="<?= htmlspecialchars($editBanner['button_link'] ?? '#') ?>" 
                                   class="form-control" placeholder="เช่น services.php หรือ https://...">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">อัปโหลดไฟล์ภาพแบนเนอร์ (แนะนำขนาด 1600x600 px)</label>
                        <input type="file" name="banner_file" accept="image/*" class="form-control image-upload-input" data-preview-target="bannerPreviewImg">
                    </div>

                    <div class="form-group">
                        <label class="form-label">หรือ ระบุ URL รูปภาพโดยตรง</label>
                        <input type="text" name="image_url" 
                               value="<?= htmlspecialchars($editBanner['image_url'] ?? '') ?>" 
                               class="form-control" placeholder="https://images.unsplash.com/...">
                    </div>

                    <!-- Image Preview -->
                    <div class="form-group">
                        <label class="form-label">ตัวอย่างรูปภาพ</label>
                        <div class="preview-box">
                            <?php 
                                $currentImg = '';
                                if ($editBanner && !empty($editBanner['image_url'])) {
                                    $currentImg = (strpos($editBanner['image_url'], 'http') === 0 ? '' : '../') . $editBanner['image_url'];
                                }
                            ?>
                            <img id="bannerPreviewImg" src="<?= htmlspecialchars($currentImg) ?>" 
                                 alt="Preview" style="<?= empty($currentImg) ? 'display:none;' : '' ?>">
                            <div class="preview-placeholder" style="<?= !empty($currentImg) ? 'display:none;' : '' ?>color:#94a3b8;font-size:13px;text-align:center;">
                                <i class="fa-regular fa-image" style="font-size:32px;display:block;margin-bottom:6px;"></i>
                                เลือกไฟล์หรือกรอก URL เพื่อดูตัวอย่าง
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px;">
                            <input type="checkbox" name="is_active" <?= (!isset($editBanner) || !empty($editBanner['is_active'])) ? 'checked' : '' ?>>
                            <span><strong>เปิดใช้งานทันที</strong> (แสดงบนสไลด์หน้าแรก)</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-weight: 600;">
                        <i class="fa-solid fa-save"></i> <?= $editBanner ? 'บันทึกการแก้ไข' : 'บันทึกฮีโร่แบนเนอร์' ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
