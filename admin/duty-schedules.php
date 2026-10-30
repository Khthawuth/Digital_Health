<?php
// admin/duty-schedules.php - จัดการตารางเวรปฏิบัติหน้าที่ของเจ้าหน้าที่
$pageTitle = "จัดการตารางเวรเจ้าหน้าที่";
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM duty_schedules WHERE id = :id");
    $stmt->execute([':id' => $id]);
    setFlash('success', 'ลบรายการตารางเวรเรียบร้อยแล้ว');
    header('Location: duty-schedules.php');
    exit;
}

// Handle Edit Fetch
$editItem = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM duty_schedules WHERE id = :id");
    $stmt->execute([':id' => (int)$_GET['id']]);
    $editItem = $stmt->fetch();
}

// Handle Form Submission (Add / Update)
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = !empty($_POST['duty_id']) ? (int)$_POST['duty_id'] : null;
    $staffName = trim($_POST['staff_name'] ?? '');
    $staffPosition = trim($_POST['staff_position'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $dutyDate = trim($_POST['duty_date'] ?? '');
    $shiftType = trim($_POST['shift_type'] ?? 'morning');
    $shiftName = trim($_POST['shift_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $note = trim($_POST['note'] ?? '');

    // Default shift names if left empty
    if (empty($shiftName)) {
        if ($shiftType === 'morning') $shiftName = 'เวรเช้า (08:30 - 16:30 น.)';
        elseif ($shiftType === 'afternoon') $shiftName = 'เวรบ่าย (16:30 - 00:30 น.)';
        elseif ($shiftType === 'night') $shiftName = 'เวรดึก (00:30 - 08:30 น.)';
        elseif ($shiftType === 'on_call') $shiftName = 'เวรออนคอลฉุกเฉิน (24 ชม.)';
        else $shiftName = 'เวรเต็มวัน (08:30 - 20:30 น.)';
    }

    if (empty($staffName) || empty($dutyDate)) {
        $error = 'กรุณากรอกชื่อเจ้าหน้าที่ และวันที่ปฏิบัติเวร';
    }

    if (empty($error)) {
        if ($id) {
            // Update
            $sql = "UPDATE duty_schedules SET staff_name = :name, staff_position = :pos, department = :dept, 
                    duty_date = :date, shift_type = :type, shift_name = :sname, phone = :phone, note = :note 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $staffName, ':pos' => $staffPosition, ':dept' => $department,
                ':date' => $dutyDate, ':type' => $shiftType, ':sname' => $shiftName,
                ':phone' => $phone, ':note' => $note, ':id' => $id
            ]);
            setFlash('success', 'บันทึกการแก้ไขตารางเวรเรียบร้อยแล้ว');
        } else {
            // Insert
            $sql = "INSERT INTO duty_schedules (staff_name, staff_position, department, duty_date, shift_type, shift_name, phone, note) 
                    VALUES (:name, :pos, :dept, :date, :type, :sname, :phone, :note)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $staffName, ':pos' => $staffPosition, ':dept' => $department,
                ':date' => $dutyDate, ':type' => $shiftType, ':sname' => $shiftName,
                ':phone' => $phone, ':note' => $note
            ]);
            setFlash('success', 'เพิ่มตารางเวรเจ้าหน้าที่ใหม่เรียบร้อยแล้ว');
        }
        header('Location: duty-schedules.php');
        exit;
    }
}

// Filter parameters for table
$filterMonth = isset($_GET['filter_month']) ? (int)$_GET['filter_month'] : (int)date('n');
$filterYear = isset($_GET['filter_year']) ? (int)$_GET['filter_year'] : (int)date('Y');
$filterDept = trim($_GET['filter_dept'] ?? '');

$where = ["MONTH(duty_date) = :m AND YEAR(duty_date) = :y"];
$params = [':m' => $filterMonth, ':y' => $filterYear];

if (!empty($filterDept)) {
    $where[] = "department = :dept";
    $params[':dept'] = $filterDept;
}

$whereSql = "WHERE " . implode(" AND ", $where);
$sql = "SELECT * FROM duty_schedules $whereSql ORDER BY duty_date DESC, FIELD(shift_type, 'morning', 'afternoon', 'night', 'full_day', 'on_call'), id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$schedules = $stmt->fetchAll();

// Distinct Departments for dropdown suggestions
$departments = $pdo->query("SELECT DISTINCT department FROM duty_schedules WHERE department IS NOT NULL AND department != '' ORDER BY department ASC")->fetchAll(PDO::FETCH_COLUMN);

// Thai month names
$thaiMonths = [
    1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
    5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
    9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
];
?>

<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px;">
    <!-- Schedules List Table -->
    <div>
        <div class="admin-card">
            <div class="admin-card-header">
                <div>
                    <h3 class="admin-card-title"><i class="fa-solid fa-calendar-days text-primary"></i> รายการตารางเวรเจ้าหน้าที่ (<?= count($schedules) ?> รายการ)</h3>
                    <p style="font-size: 13px; color: #64748b; margin: 0;">ตารางเวรจะแสดงบนหน้าปฏิทินของเว็บไซต์หลัก (duty-schedule.php)</p>
                </div>
                <a href="../duty-schedule.php" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> ดูหน้าปฏิทินสาธารณะ
                </a>
            </div>

            <!-- Filter Toolbar -->
            <div style="padding: 16px 24px; background: #f8fafc; border-bottom: 1px solid var(--admin-border);">
                <form action="duty-schedules.php" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <div style="width: 170px;">
                        <select name="filter_month" class="form-control" onchange="this.form.submit()">
                            <?php foreach ($thaiMonths as $mNum => $mName): ?>
                                <option value="<?= $mNum ?>" <?= $filterMonth == $mNum ? 'selected' : '' ?>>
                                    <?= $mName ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="width: 130px;">
                        <select name="filter_year" class="form-control" onchange="this.form.submit()">
                            <option value="2025" <?= $filterYear == 2025 ? 'selected' : '' ?>>2568</option>
                            <option value="2026" <?= $filterYear == 2026 ? 'selected' : '' ?>>2569</option>
                            <option value="2027" <?= $filterYear == 2027 ? 'selected' : '' ?>>2570</option>
                        </select>
                    </div>

                    <div style="flex-grow: 1;">
                        <select name="filter_dept" class="form-control" onchange="this.form.submit()">
                            <option value="">-- ทุกกลุ่มงาน/ฝ่าย --</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= htmlspecialchars($d) ?>" <?= $filterDept === $d ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if (!empty($filterDept)): ?>
                        <a href="duty-schedules.php" class="btn btn-light btn-sm">ล้างตัวกรอง</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table -->
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 110px;">วันที่</th>
                            <th>กะเวร</th>
                            <th>เจ้าหน้าที่</th>
                            <th>ตำแหน่ง / สังกัด</th>
                            <th>เบอร์ติดต่อ</th>
                            <th style="text-align: right;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($schedules)): ?>
                            <?php foreach ($schedules as $item): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; font-size: 13px; color: #0f172a;">
                                            <?= thaiDate($item['duty_date']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="shift-badge-pill shift-<?= htmlspecialchars($item['shift_type']) ?>">
                                            <?= htmlspecialchars($item['shift_name']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; font-size: 13.5px; color: #0f172a;">
                                            <?= htmlspecialchars($item['staff_name']) ?>
                                        </div>
                                        <?php if (!empty($item['note'])): ?>
                                            <div style="font-size: 11.5px; color: #64748b;">
                                                <i class="fa-regular fa-comment-dots"></i> <?= htmlspecialchars($item['note']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-size: 13px; color: #334155;"><?= htmlspecialchars($item['staff_position'] ?? '-') ?></div>
                                        <div style="font-size: 11.5px; color: #0d9488;"><?= htmlspecialchars($item['department'] ?? '-') ?></div>
                                    </td>
                                    <td style="font-size: 12.5px;">
                                        <?= htmlspecialchars($item['phone'] ?? '-') ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <div class="action-btns" style="justify-content: flex-end;">
                                            <a href="duty-schedules.php?action=edit&id=<?= $item['id'] ?>" class="btn-action btn-edit" title="แก้ไข">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <a href="duty-schedules.php?action=delete&id=<?= $item['id'] ?>" 
                                               class="btn-action btn-delete confirm-delete" 
                                               data-title="เวรวันที่ <?= $item['duty_date'] ?> (<?= htmlspecialchars($item['staff_name']) ?>)" 
                                               title="ลบ">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px; color: #94a3b8;">
                                    ยังไม่มีตารางเวรในเดือนนี้ กรุณาเพิ่มรายการทางด้านขวามือ
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add / Edit Duty Form Card -->
    <div>
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="fa-solid fa-<?= $editItem ? 'pen-to-square' : 'calendar-plus' ?> text-primary"></i> 
                    <?= $editItem ? 'แก้ไขรายการตารางเวร' : 'เพิ่มตารางเวรเจ้าหน้าที่ใหม่' ?>
                </h3>
                <?php if ($editItem): ?>
                    <a href="duty-schedules.php" class="btn btn-light btn-sm"><i class="fa-solid fa-xmark"></i> ยกเลิก</a>
                <?php endif; ?>
            </div>

            <div style="padding: 24px;">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="duty-schedules.php" method="POST">
                    <?php if ($editItem): ?>
                        <input type="hidden" name="duty_id" value="<?= $editItem['id'] ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label">ชื่อ - นามสกุล เจ้าหน้าที่ <span style="color:red;">*</span></label>
                        <input type="text" name="staff_name" required 
                               value="<?= htmlspecialchars($editItem['staff_name'] ?? '') ?>" 
                               class="form-control" placeholder="เช่น นพ. ศุภชัย วิทยานุกูล หรือ นายเอกภาพ วงศ์สวรรค์">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group">
                            <label class="form-label">ตำแหน่ง</label>
                            <input type="text" name="staff_position" 
                                   value="<?= htmlspecialchars($editItem['staff_position'] ?? '') ?>" 
                                   class="form-control" placeholder="เช่น แพทย์เวร หรือ นักวิชาการคอมพิวเตอร์">
                        </div>

                        <div class="form-group">
                            <label class="form-label">เบอร์โทรศัพท์ติดต่อ</label>
                            <input type="text" name="phone" 
                                   value="<?= htmlspecialchars($editItem['phone'] ?? '') ?>" 
                                   class="form-control" placeholder="เช่น 081-456-7890">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">กลุ่มงาน / ฝ่ายที่สังกัด</label>
                        <input type="text" name="department" list="deptSuggestions" 
                               value="<?= htmlspecialchars($editItem['department'] ?? 'ฝ่ายระบบการแพทย์ทางไกล (Telemedicine)') ?>" 
                               class="form-control" placeholder="ระบุหรือเลือกกลุ่มงาน">
                        <datalist id="deptSuggestions">
                            <option value="ฝ่ายระบบการแพทย์ทางไกล (Telemedicine)">
                            <option value="ศูนย์เฝ้าระวังไซเบอร์ (NOC/SOC 24/7)">
                            <option value="กลุ่มงานระบบเวชระเบียนและข้อมูลสุขภาพ">
                            <option value="กลุ่มงานนวัตกรรมและปัญญาประดิษฐ์ (AI)">
                            <option value="กลุ่มงานสื่อสารองค์กรและบริการประชาชน">
                        </datalist>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group">
                            <label class="form-label">วันที่ปฏิบัติหน้าที่เวร <span style="color:red;">*</span></label>
                            <input type="date" name="duty_date" required 
                                   value="<?= htmlspecialchars($editItem['duty_date'] ?? date('Y-m-d')) ?>" 
                                   class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="form-label">ประเภทกะเวร <span style="color:red;">*</span></label>
                            <select name="shift_type" id="shiftTypeSelect" required class="form-control" onchange="autoFillShiftName()">
                                <option value="morning" <?= ($editItem['shift_type'] ?? 'morning') === 'morning' ? 'selected' : '' ?>>☀️ เวรเช้า (08:30 - 16:30 น.)</option>
                                <option value="afternoon" <?= ($editItem['shift_type'] ?? '') === 'afternoon' ? 'selected' : '' ?>>⛅ เวรบ่าย (16:30 - 00:30 น.)</option>
                                <option value="night" <?= ($editItem['shift_type'] ?? '') === 'night' ? 'selected' : '' ?>>🌙 เวรดึก (00:30 - 08:30 น.)</option>
                                <option value="on_call" <?= ($editItem['shift_type'] ?? '') === 'on_call' ? 'selected' : '' ?>>🚨 เวรออนคอลฉุกเฉิน (24 ชม.)</option>
                                <option value="full_day" <?= ($editItem['shift_type'] ?? '') === 'full_day' ? 'selected' : '' ?>>⚡ เวรเต็มวัน (08:30 - 20:30 น.)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">ชื่อแสดงผลกะเวลา (ปรับแก้ได้)</label>
                        <input type="text" name="shift_name" id="shiftNameInput" 
                               value="<?= htmlspecialchars($editItem['shift_name'] ?? 'เวรเช้า (08:30 - 16:30 น.)') ?>" 
                               class="form-control" placeholder="เช่น เวรเช้า (08:30 - 16:30 น.)">
                    </div>

                    <div class="form-group">
                        <label class="form-label">หมายเหตุ / ภารกิจเฉพาะกิจ</label>
                        <textarea name="note" rows="3" class="form-control" 
                                  placeholder="เช่น ตรวจเคสฉุกเฉิน, ดูแลระบบเกตเวย์ รพ., เฝ้าระวังความมั่นคงปลอดภัยไซเบอร์..."><?= htmlspecialchars($editItem['note'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-weight: 600;">
                        <i class="fa-solid fa-floppy-disk"></i> <?= $editItem ? 'บันทึกการแก้ไขตารางเวร' : 'บันทึกตารางเวรใหม่' ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function autoFillShiftName() {
    const type = document.getElementById('shiftTypeSelect').value;
    const input = document.getElementById('shiftNameInput');
    if (type === 'morning') input.value = 'เวรเช้า (08:30 - 16:30 น.)';
    else if (type === 'afternoon') input.value = 'เวรบ่าย (16:30 - 00:30 น.)';
    else if (type === 'night') input.value = 'เวรดึก (00:30 - 08:30 น.)';
    else if (type === 'on_call') input.value = 'เวรออนคอลฉุกเฉิน (24 ชม.)';
    else if (type === 'full_day') input.value = 'เวรเต็มวัน (08:30 - 20:30 น.)';
}
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
