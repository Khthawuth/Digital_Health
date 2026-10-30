<?php
// admin/executive-form.php - ฟอร์มเพิ่ม/แก้ไขผู้บริหารในผังองค์กร
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$exec = null;
$pageTitle = $id > 0 ? "แก้ไขข้อมูลผู้บริหาร" : "เพิ่มผู้บริหารใหม่เข้าผังองค์กร";

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM executives WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $exec = $stmt->fetch();
    if (!$exec) {
        setFlash('danger', 'ไม่พบข้อมูลผู้บริหารที่ต้องการแก้ไข');
        header('Location: executives.php');
        exit;
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $level = (int)($_POST['level'] ?? 1);
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $imageUrl = trim($_POST['image_url'] ?? ($exec['image_url'] ?? ''));

    // Handle Image Upload
    $uploadResult = handleImageUpload('exec_image', 'executives');
    if ($uploadResult) {
        if (isset($uploadResult['error'])) {
            $error = $uploadResult['error'];
        } elseif (isset($uploadResult['path'])) {
            $imageUrl = $uploadResult['path'];
        }
    }

    if (empty($name) || empty($position)) {
        $error = 'กรุณาระบุชื่อ-นามสกุล และตำแหน่งบริหาร';
    }

    if (empty($error)) {
        if ($id > 0) {
            $sql = "UPDATE executives SET name = :name, position = :pos, department = :dept, 
                    level = :level, image_url = :img, email = :email, phone = :phone, bio = :bio, sort_order = :sort 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name, ':pos' => $position, ':dept' => $department,
                ':level' => $level, ':img' => $imageUrl, ':email' => $email,
                ':phone' => $phone, ':bio' => $bio, ':sort' => $sortOrder, ':id' => $id
            ]);
            setFlash('success', 'บันทึกการแก้ไขข้อมูลผู้บริหารเรียบร้อยแล้ว');
        } else {
            $sql = "INSERT INTO executives (name, position, department, level, image_url, email, phone, bio, sort_order) 
                    VALUES (:name, :pos, :dept, :level, :img, :email, :phone, :bio, :sort)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name, ':pos' => $position, ':dept' => $department,
                ':level' => $level, ':img' => $imageUrl, ':email' => $email,
                ':phone' => $phone, ':bio' => $bio, ':sort' => $sortOrder
            ]);
            setFlash('success', 'เพิ่มผู้บริหารเข้าสู่ผังองค์กรเรียบร้อยแล้ว');
        }
        header('Location: executives.php');
        exit;
    }
}
?>

<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 700; color: #0f172a;"><?= $pageTitle ?></h2>
            <p style="font-size: 13.5px; color: #64748b; margin: 0;">กรอกข้อมูลผู้บริหารและกำหนดระดับโครงสร้างในแผนผังองค์กร</p>
        </div>
        <a href="executives.php" class="btn btn-light btn-sm">
            <i class="fa-solid fa-arrow-left"></i> กลับไปหน้ารายการผังบริหาร
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="executive-form.php<?= $id > 0 ? '?id=' . $id : '' ?>" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <!-- Left Info Column -->
            <div class="form-box">
                <h4 class="form-box-title"><i class="fa-solid fa-user-tie text-primary"></i> ข้อมูลประวัติและตำแหน่ง</h4>

                <div class="form-group">
                    <label class="form-label">ชื่อ - นามสกุล (พร้อมคำนำหน้า/ยศ) <span style="color:red;">*</span></label>
                    <input type="text" name="name" required 
                           value="<?= htmlspecialchars($exec['name'] ?? '') ?>" 
                           class="form-control" placeholder="เช่น นพ. เกรียงศักดิ์ วิจิตรสวัสดิ์">
                </div>

                <div class="form-group">
                    <label class="form-label">ตำแหน่งบริหารงาน <span style="color:red;">*</span></label>
                    <input type="text" name="position" required 
                           value="<?= htmlspecialchars($exec['position'] ?? '') ?>" 
                           class="form-control" placeholder="เช่น ผู้อำนวยการศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล">
                </div>

                <div class="form-group">
                    <label class="form-label">กลุ่มงาน / ฝ่ายที่สังกัด</label>
                    <input type="text" name="department" 
                           value="<?= htmlspecialchars($exec['department'] ?? '') ?>" 
                           class="form-control" placeholder="เช่น สำนักบริหารยุทธศาสตร์สุขภาพดิจิทัล">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label class="form-label">อีเมลติดต่อ</label>
                        <input type="email" name="email" 
                               value="<?= htmlspecialchars($exec['email'] ?? '') ?>" 
                               class="form-control" placeholder="kriangsak.w@digitalhealth.moph.go.th">
                    </div>

                    <div class="form-group">
                        <label class="form-label">เบอร์โทรศัพท์ติดต่อ</label>
                        <input type="text" name="phone" 
                               value="<?= htmlspecialchars($exec['phone'] ?? '') ?>" 
                               class="form-control" placeholder="02-590-3101">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">ประวัติโดยย่อ / ความเชี่ยวชาญ</label>
                    <textarea name="bio" rows="4" class="form-control" 
                              placeholder="เช่น ผู้เชี่ยวชาญด้านเวชศาสตร์สารสนเทศ และสถาปัตยกรรมคลาวด์สาธารณสุข..."><?= htmlspecialchars($exec['bio'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Right Hierarchy & Photo Column -->
            <div>
                <!-- Hierarchy Level Box -->
                <div class="form-box">
                    <h4 class="form-box-title"><i class="fa-solid fa-sitemap text-primary"></i> โครงสร้างลำดับขั้นในผัง</h4>

                    <div class="form-group">
                        <label class="form-label">ระดับในผังบริหาร (Hierarchy Level) <span style="color:red;">*</span></label>
                        <select name="level" required class="form-control">
                            <option value="1" <?= ($exec['level'] ?? 1) == 1 ? 'selected' : '' ?>>
                                ระดับ 1: ผู้บริหารสูงสุด (ผู้อำนวยการ)
                            </option>
                            <option value="2" <?= ($exec['level'] ?? 1) == 2 ? 'selected' : '' ?>>
                                ระดับ 2: รองผู้อำนวยการ
                            </option>
                            <option value="3" <?= ($exec['level'] ?? 1) == 3 ? 'selected' : '' ?>>
                                ระดับ 3: หัวหน้ากลุ่มงาน / ฝ่ายบริหาร
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">ลำดับการแสดงผล (Sort Order)</label>
                        <input type="number" name="sort_order" 
                               value="<?= htmlspecialchars($exec['sort_order'] ?? '0') ?>" 
                               class="form-control" placeholder="0">
                        <small style="color: #64748b; font-size: 11.5px;">ค่าน้อยจะแสดงก่อนจากซ้ายไปขวา</small>
                    </div>
                </div>

                <!-- Executive Photo -->
                <div class="form-box">
                    <h4 class="form-box-title"><i class="fa-regular fa-image text-primary"></i> รูปถ่ายทางการ</h4>

                    <div class="form-group">
                        <label class="form-label">อัปโหลดรูปภาพผู้บริหาร</label>
                        <input type="file" name="exec_image" accept="image/*" class="form-control image-upload-input" data-preview-target="execPreviewImg">
                    </div>

                    <div class="form-group">
                        <label class="form-label">หรือ ระบุ URL รูปภาพ</label>
                        <input type="text" name="image_url" 
                               value="<?= htmlspecialchars($exec['image_url'] ?? '') ?>" 
                               class="form-control" placeholder="https://images.unsplash.com/...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">ตัวอย่างรูปถ่าย</label>
                        <div class="preview-box" style="height: 150px;">
                            <?php 
                                $currentImg = '';
                                if ($exec && !empty($exec['image_url'])) {
                                    $currentImg = (strpos($exec['image_url'], 'http') === 0 ? '' : '../') . $exec['image_url'];
                                }
                            ?>
                            <img id="execPreviewImg" src="<?= htmlspecialchars($currentImg) ?>" 
                                 alt="Preview" style="<?= empty($currentImg) ? 'display:none;' : '' ?>">
                            <div class="preview-placeholder" style="<?= !empty($currentImg) ? 'display:none;' : '' ?>color:#94a3b8;font-size:13px;text-align:center;">
                                <i class="fa-regular fa-user" style="font-size:32px;display:block;margin-bottom:6px;"></i>
                                เลือกรูปถ่ายเพื่อแสดงตัวอย่าง
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 13px; font-weight: 600;">
                    <i class="fa-solid fa-floppy-disk"></i> <?= $id > 0 ? 'บันทึกการแก้ไข' : 'บันทึกเข้าสู่ผังบริหาร' ?>
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
