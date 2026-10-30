<?php
// includes/header.php - Global Header & Navigation
require_once __DIR__ . '/../config/functions.php';

$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
$siteTitle = getSetting('site_name', 'ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล');
$siteShortName = getSetting('site_short_name', 'สุขภาพดิจิทัล');
$hotline = getSetting('contact_phone', '02-590-3000');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?><?= htmlspecialchars($siteTitle) ?></title>
    <meta name="description" content="<?= isset($pageDesc) ? htmlspecialchars($pageDesc) : 'ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล ประชาสัมพันธ์ข่าวสาร บริการการแพทย์ทางไกล และผังโครงสร้างผู้บริหาร' ?>">
    
    <!-- Google Fonts & Font Awesome 6 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Main Navigation Header (แถบสีขาวขนาดใหญ่ สมดุล และทันสมัย) -->
<header class="site-header">
    <div class="container navbar">
        <!-- Logo & Hospital Programmer Branding -->
        <a href="index.php" class="nav-brand" title="กลุ่มงานสุขภาพดิจิทัล (Hospital Software & Digital Health Division)">
            <div class="brand-icon">
                <i class="fa-solid fa-laptop-code"></i>
            </div>
            <div class="brand-text">
                <div class="brand-heading-row">
                    <h1><?= htmlspecialchars($siteShortName) ?></h1>
                    <span class="brand-dev-chip"><i class="fa-solid fa-code"></i> ทีมโปรแกรมเมอร์ รพ.</span>
                </div>
                <p>HOSPITAL SOFTWARE &amp; DIGITAL HEALTH DIVISION</p>
            </div>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle navigation menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- Navigation Links -->
        <ul class="nav-menu" id="navMenu">
            <li>
                <a href="index.php" class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-house"></i> หน้าแรก
                </a>
            </li>
            <li>
                <a href="news.php" class="nav-link <?= in_array($currentPage, ['news.php', 'news-detail.php']) ? 'active' : '' ?>">
                    <i class="fa-regular fa-newspaper"></i> ข่าวประชาสัมพันธ์
                </a>
            </li>
            <li>
                <a href="organization.php" class="nav-link <?= $currentPage === 'organization.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-sitemap"></i> ผังบริหาร
                </a>
            </li>
            <li>
                <a href="duty-schedule.php" class="nav-link <?= $currentPage === 'duty-schedule.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-calendar-days"></i> ปฏิทินตารางเวร
                </a>
            </li>
            <li>
                <a href="services.php" class="nav-link <?= $currentPage === 'services.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-laptop-medical"></i> บริการดิจิทัล
                </a>
            </li>
            <li>
                <a href="about.php" class="nav-link <?= $currentPage === 'about.php' ? 'active' : '' ?>">
                    <i class="fa-regular fa-building"></i> เกี่ยวกับเรา
                </a>
            </li>
            <li>
                <a href="contact.php" class="nav-link <?= $currentPage === 'contact.php' ? 'active' : '' ?>">
                    <i class="fa-regular fa-envelope"></i> ติดต่อเรา
                </a>
            </li>
        </ul>

        <!-- Right Action Button & User Info (Desktop) -->
        <div class="nav-actions">
            <?php if (isAdmin()): ?>
                <div class="nav-user-badge">
                    <span class="user-avatar-dot"><i class="fa-solid fa-user-shield"></i></span>
                    <span class="user-name-label"><?= htmlspecialchars($currentUser['fullname'] ?? 'Admin') ?></span>
                    <span class="badge badge-admin-chip">ADMIN</span>
                </div>
                <a href="admin/index.php" class="btn btn-primary btn-nav-action">
                    <i class="fa-solid fa-screwdriver-wrench"></i> ระบบหลังบ้าน
                </a>
                <a href="logout.php" class="btn btn-nav-icon" title="ออกจากระบบ">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            <?php elseif (isLoggedIn()): ?>
                <div class="nav-user-badge">
                    <span class="user-avatar-dot"><i class="fa-solid fa-circle-user"></i></span>
                    <span class="user-name-label"><?= htmlspecialchars($currentUser['fullname'] ?? 'ผู้ใช้งาน') ?></span>
                </div>
                <a href="logout.php" class="btn btn-light btn-nav-action" title="ออกจากระบบ">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> ออกจากระบบ
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-primary btn-nav-action">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> เข้าสู่ระบบ
                </a>
                <a href="register.php" class="btn btn-primary btn-nav-action">
                    <i class="fa-solid fa-user-plus"></i> สมัครสมาชิก
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Mobile Off-Canvas Navigation Drawer (สำหรับโทรศัพท์มือถือและแท็บเล็ต) -->
<div class="mobile-drawer-backdrop" id="mobileDrawerBackdrop"></div>
<aside class="mobile-drawer" id="mobileDrawer">
    <div class="mobile-drawer-header">
        <div class="nav-brand" style="gap: 12px;">
            <div class="brand-icon" style="width: 44px; height: 44px; font-size: 20px;">
                <i class="fa-solid fa-laptop-code"></i>
            </div>
            <div class="brand-text">
                <div style="font-size: 16px; font-weight: 700; color: var(--dark-navy); line-height: 1.2;"><?= htmlspecialchars($siteShortName) ?></div>
                <div style="font-size: 11px; color: var(--primary); font-weight: 600; line-height: 1.2; margin-top: 2px;">
                    <i class="fa-solid fa-code"></i> Hospital Developer Team
                </div>
            </div>
        </div>
        <button class="mobile-drawer-close" id="mobileDrawerClose" aria-label="Close navigation menu">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="mobile-drawer-body">
        <div class="mobile-drawer-section-title">เมนูนำทางหลัก</div>
        <ul class="mobile-nav-links">
            <li>
                <a href="index.php" class="mobile-nav-item <?= $currentPage === 'index.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-house"></i>
                    <span>หน้าแรก</span>
                </a>
            </li>
            <li>
                <a href="news.php" class="mobile-nav-item <?= in_array($currentPage, ['news.php', 'news-detail.php']) ? 'active' : '' ?>">
                    <i class="fa-regular fa-newspaper"></i>
                    <span>ข่าวประชาสัมพันธ์</span>
                </a>
            </li>
            <li>
                <a href="organization.php" class="mobile-nav-item <?= $currentPage === 'organization.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-sitemap"></i>
                    <span>ผังบริหารองค์กร</span>
                </a>
            </li>
            <li>
                <a href="duty-schedule.php" class="mobile-nav-item <?= $currentPage === 'duty-schedule.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>ปฏิทินตารางเวร</span>
                    <span class="mobile-nav-badge">ใหม่</span>
                </a>
            </li>
            <li>
                <a href="services.php" class="mobile-nav-item <?= $currentPage === 'services.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-laptop-medical"></i>
                    <span>บริการสุขภาพดิจิทัล</span>
                </a>
            </li>
            <li>
                <a href="about.php" class="mobile-nav-item <?= $currentPage === 'about.php' ? 'active' : '' ?>">
                    <i class="fa-regular fa-building"></i>
                    <span>เกี่ยวกับเรา</span>
                </a>
            </li>
            <li>
                <a href="contact.php" class="mobile-nav-item <?= $currentPage === 'contact.php' ? 'active' : '' ?>">
                    <i class="fa-regular fa-envelope"></i>
                    <span>ติดต่อสอบถาม</span>
                </a>
            </li>
        </ul>

        <div class="mobile-drawer-section-title" style="margin-top: 24px;">ระบบสมาชิก & จัดการ</div>
        <div class="mobile-drawer-actions">
            <?php if (isAdmin()): ?>
                <a href="admin/index.php" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <i class="fa-solid fa-screwdriver-wrench"></i> เข้าสู่ระบบหลังบ้าน
                </a>
                <a href="logout.php" class="btn btn-light" style="width: 100%; justify-content: center; margin-top: 8px;">
                    <i class="fa-solid fa-power-off"></i> ออกจากระบบ (Admin)
                </a>
            <?php elseif (isLoggedIn()): ?>
                <div style="font-size: 13px; color: var(--slate-dark); margin-bottom: 8px; text-align: center;">
                    ยินดีต้อนรับ, <strong><?= htmlspecialchars($currentUser['fullname'] ?? 'ผู้ใช้งาน') ?></strong>
                </div>
                <a href="logout.php" class="btn btn-light" style="width: 100%; justify-content: center;">
                    <i class="fa-solid fa-power-off"></i> ออกจากระบบ
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-primary" style="width: 100%; justify-content: center;">
                    <i class="fa-solid fa-lock"></i> เข้าสู่ระบบ (Admin/User)
                </a>
                <a href="register.php" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 8px;">
                    <i class="fa-solid fa-user-plus"></i> ลงทะเบียนประชาชน
                </a>
            <?php endif; ?>
        </div>

        <div class="mobile-drawer-footer">
            <div class="mobile-hotline-card">
                <i class="fa-solid fa-headset text-primary" style="font-size: 20px;"></i>
                <div>
                    <div style="font-size: 11px; color: var(--slate-medium);">สายด่วนศูนย์สุขภาพดิจิทัล</div>
                    <div style="font-size: 13.5px; font-weight: 700; color: var(--dark-navy);"><?= htmlspecialchars($hotline) ?></div>
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- Flash Notification Area -->
<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
<div class="container" style="margin-top: 20px;">
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
        <i class="fa-solid fa-circle-info"></i>
        <span><?= htmlspecialchars($flash['message']) ?></span>
    </div>
</div>
<?php endif; ?>
