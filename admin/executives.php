<?php
// admin/executives.php - จัดการผังบริหารและคณะผู้บริหาร
$pageTitle = "จัดการผังบริหาร (Organization Chart)";
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();

// Handle Delete Executive
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM executives WHERE id = :id");
    $stmt->execute([':id' => $id]);
    setFlash('success', 'ลบข้อมูลผู้บริหารออกจากผังเรียบร้อยแล้ว');
    header('Location: executives.php');
    exit;
}

// Fetch all executives sorted by level and sort_order
$stmt = $pdo->query("SELECT * FROM executives ORDER BY level ASC, sort_order ASC, id ASC");
$executives = $stmt->fetchAll();

$levelLabels = [
    1 => 'ระดับ 1: ผู้บริหารสูงสุด (Executive Director)',
    2 => 'ระดับ 2: คณะรองผู้อำนวยการ (Deputy Directors)',
    3 => 'ระดับ 3: หัวหน้ากลุ่มงานและฝ่ายบริหาร (Department Heads)'
];
?>

<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h3 class="admin-card-title"><i class="fa-solid fa-sitemap text-primary"></i> โครงสร้างผังบริหารศูนย์สุขภาพดิจิทัล (<?= count($executives) ?> ท่าน)</h3>
            <p style="font-size: 13px; color: #64748b; margin: 0;">ข้อมูลจะถูกจัดเรียงเป็นแผนผังองค์กรบนหน้าเว็บไซต์หลัก (organization.php)</p>
        </div>
        <a href="executive-form.php" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-user-plus"></i> เพิ่มผู้บริหารใหม่เข้าสู่ผัง
        </a>
    </div>

    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 70px;">รูปถ่าย</th>
                    <th>ชื่อ - นามสกุล</th>
                    <th>ตำแหน่งบริหาร</th>
                    <th>กลุ่มงาน / ฝ่าย</th>
                    <th>ระดับในผัง</th>
                    <th>ลำดับ</th>
                    <th>ข้อมูลติดต่อ</th>
                    <th style="text-align: right;">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($executives)): ?>
                    <?php foreach ($executives as $ex): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars((strpos($ex['image_url'] ?? '', 'http') === 0 ? '' : '../') . ($ex['image_url'] ?: 'assets/images/default-avatar.png')) ?>" 
                                     alt="" class="avatar-table-thumb">
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 14px; color: #0f172a;">
                                    <?= htmlspecialchars($ex['name']) ?>
                                </div>
                            </td>
                            <td style="font-size: 13px; color: #0d9488; font-weight: 500;">
                                <?= htmlspecialchars($ex['position']) ?>
                            </td>
                            <td style="font-size: 12.5px; color: #64748b;">
                                <?= htmlspecialchars($ex['department'] ?? '-') ?>
                            </td>
                            <td>
                                <?php if ($ex['level'] == 1): ?>
                                    <span class="badge" style="background:#fef3c7;color:#b45309;"><i class="fa-solid fa-crown"></i> ระดับ 1</span>
                                <?php elseif ($ex['level'] == 2): ?>
                                    <span class="badge" style="background:#e0f2fe;color:#0369a1;"><i class="fa-solid fa-user-tie"></i> ระดับ 2</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><i class="fa-solid fa-network-wired"></i> ระดับ 3</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-weight: 600; font-family: monospace;"><?= $ex['sort_order'] ?></span>
                            </td>
                            <td style="font-size: 12px; color: #64748b;">
                                <?php if (!empty($ex['email'])): ?>
                                    <div><i class="fa-regular fa-envelope"></i> <?= htmlspecialchars($ex['email']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($ex['phone'])): ?>
                                    <div><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($ex['phone']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btns" style="justify-content: flex-end;">
                                    <a href="executive-form.php?id=<?= $ex['id'] ?>" class="btn-action btn-edit" title="แก้ไข">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <a href="executives.php?action=delete&id=<?= $ex['id'] ?>" 
                                       class="btn-action btn-delete confirm-delete" 
                                       data-title="<?= htmlspecialchars($ex['name']) ?>" 
                                       title="ลบ">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" style="text-align: center; padding: 30px;">ยังไม่มีข้อมูลผู้บริหารในผังองค์กร</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
