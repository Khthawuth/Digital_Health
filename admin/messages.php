<?php
// admin/messages.php - จัดการข้อความติดต่อสอบถามจากประชาชน
$pageTitle = "ข้อความติดต่อสอบถาม";
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();

// Mark as Read
if (isset($_GET['action']) && $_GET['action'] === 'read' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = :id")->execute([':id' => $id]);
    setFlash('info', 'ทำเครื่องหมายว่าอ่านแล้ว');
    header('Location: messages.php');
    exit;
}

// Delete Message
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $pdo->prepare("DELETE FROM contact_messages WHERE id = :id")->execute([':id' => $id]);
    setFlash('success', 'ลบข้อความติดต่อเรียบร้อยแล้ว');
    header('Location: messages.php');
    exit;
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
?>

<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h3 class="admin-card-title"><i class="fa-regular fa-envelope text-primary"></i> กล่องข้อความติดต่อสอบถาม (<?= count($messages) ?> ข้อความ)</h3>
            <p style="font-size: 13px; color: #64748b; margin: 0;">ข้อความที่ส่งเข้ามาผ่านแบบฟอร์มหน้าติดต่อเรา (contact.php)</p>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>สถานะ</th>
                    <th>ผู้ติดต่อ</th>
                    <th>หัวข้อเรื่อง</th>
                    <th>ข้อความรายละเอียด</th>
                    <th>วันที่ส่ง</th>
                    <th style="text-align: right;">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $m): ?>
                        <tr style="<?= $m['status'] === 'unread' ? 'background: #f0fdfa;' : '' ?>">
                            <td>
                                <?php if ($m['status'] === 'unread'): ?>
                                    <span class="badge" style="background:#fef3c7;color:#92400e;"><i class="fa-solid fa-envelope"></i> ยังไม่อ่าน</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><i class="fa-solid fa-envelope-open"></i> อ่านแล้ว</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 13.5px; color: #0f172a;"><?= htmlspecialchars($m['name']) ?></div>
                                <div style="font-size: 12px; color: #64748b;">
                                    <a href="mailto:<?= htmlspecialchars($m['email']) ?>" style="color: #0d9488;"><?= htmlspecialchars($m['email']) ?></a>
                                    <?= !empty($m['phone']) ? ' | ' . htmlspecialchars($m['phone']) : '' ?>
                                </div>
                            </td>
                            <td style="font-weight: 500; font-size: 13px; color: #1e293b;">
                                <?= htmlspecialchars($m['subject']) ?>
                            </td>
                            <td style="font-size: 13px; color: #475569; max-width: 320px;">
                                <?= nl2br(htmlspecialchars($m['message'])) ?>
                            </td>
                            <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                <?= thaiDate($m['created_at'], true) ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btns" style="justify-content: flex-end;">
                                    <?php if ($m['status'] === 'unread'): ?>
                                        <a href="messages.php?action=read&id=<?= $m['id'] ?>" class="btn-action" title="ทำเครื่องหมายว่าอ่านแล้ว">
                                            <i class="fa-solid fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="mailto:<?= htmlspecialchars($m['email']) ?>?subject=ตอบกลับ: <?= urlencode($m['subject']) ?>" class="btn-action" title="ตอบกลับทางอีเมล">
                                        <i class="fa-solid fa-reply"></i>
                                    </a>
                                    <a href="messages.php?action=delete&id=<?= $m['id'] ?>" 
                                       class="btn-action btn-delete confirm-delete" 
                                       data-title="ข้อความจาก <?= htmlspecialchars($m['name']) ?>" 
                                       title="ลบข้อความ">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center; padding: 30px; color: #94a3b8;">ยังไม่มีข้อความติดต่อส่งเข้ามา</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
