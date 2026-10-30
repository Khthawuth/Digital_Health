<?php
// duty-schedule.php - ปฏิทินตารางเวรปฏิบัติหน้าที่ของบุคลากรและเจ้าหน้าที่
require_once __DIR__ . '/config/functions.php';

$pageTitle = "ปฏิทินตารางเวรเจ้าหน้าที่ - ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล";
$pageDesc = "ตารางเวรปฏิบัติหน้าที่ประจำวัน เจ้าหน้าที่ศูนย์เฝ้าระวังไซเบอร์ NOC/SOC และทีมการแพทย์ทางไกล Telemedicine";

$pdo = getDBConnection();

// Month and Year parameters (Defaults to current month/year)
$currentYear = (int)date('Y');
$currentMonth = (int)date('n');

$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : $currentYear;
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : $currentMonth;

// Validate month & year
if ($selectedMonth < 1) { $selectedMonth = 12; $selectedYear--; }
if ($selectedMonth > 12) { $selectedMonth = 1; $selectedYear++; }

// Filter parameters
$deptFilter = trim($_GET['dept'] ?? '');
$shiftFilter = trim($_GET['shift'] ?? '');
$viewMode = trim($_GET['view'] ?? 'calendar'); // 'calendar' or 'list'

// Calculate dates for the month
$firstDayOfMonth = mktime(0, 0, 0, $selectedMonth, 1, $selectedYear);
$daysInMonth = (int)date('t', $firstDayOfMonth);
$firstDayOfWeek = (int)date('w', $firstDayOfMonth); // 0 (Sun) to 6 (Sat)

$startDateStr = sprintf('%04d-%02d-01', $selectedYear, $selectedMonth);
$endDateStr = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $daysInMonth);

// Fetch Duty Schedules for this month
$where = ["duty_date BETWEEN :start_date AND :end_date"];
$params = [':start_date' => $startDateStr, ':end_date' => $endDateStr];

if (!empty($deptFilter)) {
    $where[] = "department = :dept";
    $params[':dept'] = $deptFilter;
}

if (!empty($shiftFilter)) {
    $where[] = "shift_type = :shift";
    $params[':shift'] = $shiftFilter;
}

$whereSql = "WHERE " . implode(" AND ", $where);
$sql = "SELECT * FROM duty_schedules $whereSql ORDER BY duty_date ASC, FIELD(shift_type, 'morning', 'afternoon', 'night', 'full_day', 'on_call'), id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$allDutys = $stmt->fetchAll();

// Group schedules by date
$dutiesByDate = [];
foreach ($allDutys as $d) {
    $dateKey = $d['duty_date'];
    if (!isset($dutiesByDate[$dateKey])) {
        $dutiesByDate[$dateKey] = [];
    }
    $dutiesByDate[$dateKey][] = $d;
}

// Fetch list of distinct departments for filter
$deptList = $pdo->query("SELECT DISTINCT department FROM duty_schedules WHERE department IS NOT NULL AND department != '' ORDER BY department ASC")->fetchAll(PDO::FETCH_COLUMN);

// Month Navigation Links
$prevMonth = $selectedMonth - 1;
$prevYear = $selectedYear;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }

$nextMonth = $selectedMonth + 1;
$nextYear = $selectedYear;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

$thaiMonths = [
    1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
    5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
    9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
];

$thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];

include __DIR__ . '/includes/header.php';
?>

<!-- Header Breadcrumb -->
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 45px 0;">
    <div class="container">
        <div style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: #94a3b8; margin-bottom: 10px;">
            <a href="index.php" style="color: #cbd5e1;">หน้าแรก</a>
            <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
            <span style="color: #38bdf8;">ปฏิทินตารางเวร</span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
            <div>
                <h1 style="font-size: 32px; font-weight: 700; color: white; margin-bottom: 8px;">
                    <i class="fa-solid fa-calendar-days text-primary"></i> ปฏิทินตารางเวรเจ้าหน้าที่
                </h1>
                <p style="color: #cbd5e1; font-size: 15px; max-width: 720px; margin: 0;">
                    ตารางเวรปฏิบัติหน้าที่ ทีมแพทย์ พยาบาล และวิศวกรระบบความมั่นคงปลอดภัยไซเบอร์ ศูนย์สุขภาพดิจิทัล
                </p>
            </div>
            <div style="display: flex; gap: 10px;">
                <?php if (isAdmin()): ?>
                    <a href="admin/duty-schedules.php" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-gear"></i> จัดการตารางเวร (Admin)
                    </a>
                <?php endif; ?>
                <button type="button" onclick="window.print()" class="btn btn-light btn-sm">
                    <i class="fa-solid fa-print"></i> พิมพ์ตารางเวร
                </button>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <div class="container">
        <!-- Control Toolbar: Month Picker, Filters, View Switcher -->
        <div style="background: white; border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); padding: 22px; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
                <!-- Month & Year Navigation -->
                <div style="display: flex; align-items: center; gap: 12px;">
                    <a href="duty-schedule.php?year=<?= $prevYear ?>&month=<?= $prevMonth ?><?= !empty($deptFilter) ? '&dept=' . urlencode($deptFilter) : '' ?><?= !empty($shiftFilter) ? '&shift=' . urlencode($shiftFilter) : '' ?>&view=<?= $viewMode ?>" 
                       class="btn btn-light btn-sm" title="เดือนก่อนหน้า">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>

                    <h2 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; min-width: 220px; text-align: center;">
                        <?= $thaiMonths[$selectedMonth] ?> <?= $selectedYear + 543 ?>
                    </h2>

                    <a href="duty-schedule.php?year=<?= $nextYear ?>&month=<?= $nextMonth ?><?= !empty($deptFilter) ? '&dept=' . urlencode($deptFilter) : '' ?><?= !empty($shiftFilter) ? '&shift=' . urlencode($shiftFilter) : '' ?>&view=<?= $viewMode ?>" 
                       class="btn btn-light btn-sm" title="เดือนถัดไป">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>

                    <a href="duty-schedule.php?year=<?= $currentYear ?>&month=<?= $currentMonth ?>&view=<?= $viewMode ?>" 
                       class="btn btn-outline-primary btn-sm" style="margin-left: 8px;">
                        วันนี้
                    </a>
                </div>

                <!-- Shift Legend & View Switcher -->
                <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
                    <!-- Legend -->
                    <div style="display: flex; gap: 10px; font-size: 12px; align-items: center;">
                        <span style="display: inline-flex; align-items: center; gap: 4px;">
                            <span class="shift-dot shift-morning"></span> เวรเช้า
                        </span>
                        <span style="display: inline-flex; align-items: center; gap: 4px;">
                            <span class="shift-dot shift-afternoon"></span> เวรบ่าย
                        </span>
                        <span style="display: inline-flex; align-items: center; gap: 4px;">
                            <span class="shift-dot shift-night"></span> เวรดึก
                        </span>
                        <span style="display: inline-flex; align-items: center; gap: 4px;">
                            <span class="shift-dot shift-oncall"></span> ออนคอล 24 ชม.
                        </span>
                    </div>

                    <!-- View Switcher -->
                    <div style="display: inline-flex; border: 1px solid var(--border-color); border-radius: 6px; overflow: hidden;">
                        <a href="duty-schedule.php?year=<?= $selectedYear ?>&month=<?= $selectedMonth ?><?= !empty($deptFilter) ? '&dept=' . urlencode($deptFilter) : '' ?><?= !empty($shiftFilter) ? '&shift=' . urlencode($shiftFilter) : '' ?>&view=calendar" 
                           class="btn btn-sm <?= $viewMode === 'calendar' ? 'btn-primary' : 'btn-light' ?>" style="border-radius: 0;">
                            <i class="fa-solid fa-calendar-days"></i> ปฏิทิน
                        </a>
                        <a href="duty-schedule.php?year=<?= $selectedYear ?>&month=<?= $selectedMonth ?><?= !empty($deptFilter) ? '&dept=' . urlencode($deptFilter) : '' ?><?= !empty($shiftFilter) ? '&shift=' . urlencode($shiftFilter) : '' ?>&view=list" 
                           class="btn btn-sm <?= $viewMode === 'list' ? 'btn-primary' : 'btn-light' ?>" style="border-radius: 0;">
                            <i class="fa-solid fa-list-ul"></i> รายการ
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter Dropdowns Form -->
            <form action="duty-schedule.php" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; padding-top: 14px; border-top: 1px solid var(--border-color);">
                <input type="hidden" name="year" value="<?= $selectedYear ?>">
                <input type="hidden" name="month" value="<?= $selectedMonth ?>">
                <input type="hidden" name="view" value="<?= htmlspecialchars($viewMode) ?>">

                <div style="flex-grow: 1; min-width: 220px;">
                    <select name="dept" class="form-control" onchange="this.form.submit()">
                        <option value="">-- กรองตามกลุ่มงาน / ฝ่าย (ทั้งหมด) --</option>
                        <?php foreach ($deptList as $d): ?>
                            <option value="<?= htmlspecialchars($d) ?>" <?= $deptFilter === $d ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="width: 220px;">
                    <select name="shift" class="form-control" onchange="this.form.submit()">
                        <option value="">-- กะเวรทั้งหมด --</option>
                        <option value="morning" <?= $shiftFilter === 'morning' ? 'selected' : '' ?>>☀️ เวรเช้า (08:30 - 16:30 น.)</option>
                        <option value="afternoon" <?= $shiftFilter === 'afternoon' ? 'selected' : '' ?>>⛅ เวรบ่าย (16:30 - 00:30 น.)</option>
                        <option value="night" <?= $shiftFilter === 'night' ? 'selected' : '' ?>>🌙 เวรดึก (00:30 - 08:30 น.)</option>
                        <option value="on_call" <?= $shiftFilter === 'on_call' ? 'selected' : '' ?>>🚨 เวรออนคอลฉุกเฉิน (24 ชม.)</option>
                    </select>
                </div>

                <?php if (!empty($deptFilter) || !empty($shiftFilter)): ?>
                    <a href="duty-schedule.php?year=<?= $selectedYear ?>&month=<?= $selectedMonth ?>&view=<?= $viewMode ?>" class="btn btn-light btn-sm">
                        <i class="fa-solid fa-rotate-left"></i> ล้างตัวกรอง
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($viewMode === 'calendar'): ?>
            <!-- Mobile Touch Scroll Hint -->
            <div class="mobile-calendar-hint" style="display: none; background: #e0f2fe; color: #0369a1; padding: 8px 12px; border-radius: 8px; font-size: 12px; margin-bottom: 12px; align-items: center; gap: 8px;">
                <i class="fa-solid fa-hand-pointer"></i> <span>เลื่อนซ้าย-ขวาบนปฏิทินเพื่อดูวัน หรือกดสลับเป็น <strong>"มุมมองรายการ"</strong> ด้านบน</span>
            </div>

            <!-- CALENDAR GRID VIEW -->
            <div class="calendar-wrapper">
                <!-- Day Headers -->
                <div class="calendar-header-row">
                    <?php foreach ($thaiDays as $dIdx => $dName): ?>
                        <div class="calendar-header-cell <?= $dIdx === 0 ? 'text-danger' : ($dIdx === 6 ? 'text-primary' : '') ?>">
                            <?= $dName ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Calendar Days Grid -->
                <div class="calendar-days-grid">
                    <!-- Blank cells for days before the 1st of the month -->
                    <?php for ($i = 0; $i < $firstDayOfWeek; $i++): ?>
                        <div class="calendar-day-cell blank"></div>
                    <?php endfor; ?>

                    <!-- Month Days -->
                    <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
                        <?php 
                            $dateStr = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $day);
                            $isToday = ($dateStr === date('Y-m-d'));
                            $dayOfWeek = (date('w', strtotime($dateStr)));
                            $duties = $dutiesByDate[$dateStr] ?? [];
                        ?>
                        <div class="calendar-day-cell <?= $isToday ? 'is-today' : '' ?>">
                            <div class="day-number-header">
                                <span class="day-num <?= $dayOfWeek == 0 ? 'text-danger' : '' ?>"><?= $day ?></span>
                                <?php if ($isToday): ?>
                                    <span class="today-badge">วันนี้</span>
                                <?php endif; ?>
                            </div>

                            <div class="day-duties-list">
                                <?php if (!empty($duties)): ?>
                                    <?php foreach ($duties as $dt): ?>
                                        <div class="duty-pill-item shift-<?= htmlspecialchars($dt['shift_type']) ?>" 
                                             onclick="showDutyModal(<?= htmlspecialchars(json_encode($dt)) ?>)" 
                                             title="<?= htmlspecialchars($dt['staff_name']) ?> - <?= htmlspecialchars($dt['shift_name']) ?>">
                                            <span class="duty-pill-shift">
                                                <?php 
                                                    if ($dt['shift_type'] === 'morning') echo '☀️ เช้า:';
                                                    elseif ($dt['shift_type'] === 'afternoon') echo '⛅ บ่าย:';
                                                    elseif ($dt['shift_type'] === 'night') echo '🌙 ดึก:';
                                                    else echo '🚨 ออนคอล:';
                                                ?>
                                            </span>
                                            <span class="duty-pill-name"><?= htmlspecialchars($dt['staff_name']) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

        <?php else: ?>
            <!-- LIST / TABLE VIEW -->
            <div style="background: white; border-radius: var(--radius-md); border: 1px solid var(--border-color); overflow: hidden; box-shadow: var(--shadow-sm);">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">วันที่ปฏิบัติเวร</th>
                            <th>กะเวร / เวลา</th>
                            <th>เจ้าหน้าที่ปฏิบัติหน้าที่</th>
                            <th>ตำแหน่ง</th>
                            <th>กลุ่มงาน / ฝ่าย</th>
                            <th>เบอร์โทรศัพท์ติดต่อ</th>
                            <th>หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($allDutys)): ?>
                            <?php foreach ($allDutys as $dt): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a;">
                                            <?= thaiDate($dt['duty_date']) ?>
                                        </div>
                                        <div style="font-size: 11.5px; color: #64748b;">
                                            วัน<?= $thaiDays[date('w', strtotime($dt['duty_date']))] ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="shift-badge-pill shift-<?= htmlspecialchars($dt['shift_type']) ?>">
                                            <?= htmlspecialchars($dt['shift_name']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #0f172a; font-size: 14px;">
                                            <?= htmlspecialchars($dt['staff_name']) ?>
                                        </div>
                                    </td>
                                    <td style="font-size: 13px; color: #475569;">
                                        <?= htmlspecialchars($dt['staff_position'] ?? '-') ?>
                                    </td>
                                    <td style="font-size: 13px; color: #0d9488;">
                                        <?= htmlspecialchars($dt['department'] ?? '-') ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($dt['phone'])): ?>
                                            <a href="tel:<?= htmlspecialchars($dt['phone']) ?>" style="color: #0284c7; font-weight: 500;">
                                                <i class="fa-solid fa-phone"></i> <?= htmlspecialchars($dt['phone']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span style="color: #94a3b8;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-size: 12.5px; color: #64748b;">
                                        <?= htmlspecialchars($dt['note'] ?? '-') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                    <i class="fa-regular fa-calendar-xmark" style="font-size: 36px; display: block; margin-bottom: 10px;"></i>
                                    ไม่พบตารางเวรในเดือนนี้ หรือตามเงื่อนไขที่เลือก
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal for Duty Details -->
<div id="dutyModal" class="duty-modal-backdrop" onclick="closeDutyModal(event)">
    <div class="duty-modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 18px; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-user-clock text-primary"></i> รายละเอียดการปฏิบัติหน้าที่เวร
            </h3>
            <button type="button" onclick="document.getElementById('dutyModal').style.display='none'" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #94a3b8;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 14px; font-size: 14px;">
            <div>
                <span style="color: #64748b; font-size: 12px; display: block;">วันที่:</span>
                <strong id="modalDate" style="font-size: 16px; color: #0f172a;"></strong>
            </div>

            <div>
                <span style="color: #64748b; font-size: 12px; display: block;">กะเวร:</span>
                <span id="modalShift" class="shift-badge-pill"></span>
            </div>

            <div>
                <span style="color: #64748b; font-size: 12px; display: block;">ชื่อเจ้าหน้าที่:</span>
                <div id="modalName" style="font-size: 16px; font-weight: 700; color: #0d9488;"></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <span style="color: #64748b; font-size: 12px; display: block;">ตำแหน่ง:</span>
                    <span id="modalPos" style="color: #334155;"></span>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 12px; display: block;">เบอร์โทรศัพท์:</span>
                    <span id="modalPhone" style="color: #0284c7; font-weight: 600;"></span>
                </div>
            </div>

            <div>
                <span style="color: #64748b; font-size: 12px; display: block;">กลุ่มงาน / สังกัด:</span>
                <span id="modalDept" style="color: #334155;"></span>
            </div>

            <div>
                <span style="color: #64748b; font-size: 12px; display: block;">รายละเอียด / ภารกิจ:</span>
                <div id="modalNote" style="background: #f8fafc; padding: 10px; border-radius: 6px; color: #475569; font-size: 13px;"></div>
            </div>
        </div>

        <div style="margin-top: 24px; text-align: right;">
            <button type="button" onclick="document.getElementById('dutyModal').style.display='none'" class="btn btn-light btn-sm">
                ปิดหน้าต่าง
            </button>
        </div>
    </div>
</div>

<script>
function showDutyModal(data) {
    document.getElementById('modalDate').textContent = data.duty_date;
    document.getElementById('modalShift').textContent = data.shift_name;
    document.getElementById('modalShift').className = 'shift-badge-pill shift-' + data.shift_type;
    document.getElementById('modalName').textContent = data.staff_name;
    document.getElementById('modalPos').textContent = data.staff_position || '-';
    document.getElementById('modalDept').textContent = data.department || '-';
    document.getElementById('modalPhone').textContent = data.phone || '-';
    document.getElementById('modalNote').textContent = data.note || 'ไม่มีข้อมูลเพิ่มเติม';
    document.getElementById('dutyModal').style.display = 'flex';
}

function closeDutyModal(e) {
    if (e.target.id === 'dutyModal') {
        document.getElementById('dutyModal').style.display = 'none';
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
