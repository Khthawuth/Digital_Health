<?php
// admin/includes/admin_header.php - Admin Dashboard Header & Navigation
require_once __DIR__ . '/auth_check.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$siteTitle = getSetting('site_name', 'ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>ระบบจัดการหลังบ้าน (Admin Dashboard)</title>
    
    <!-- Google Fonts & Font Awesome 6 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,300;0,400;0,500;0,600;0,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Admin Stylesheet -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<!-- Sidebar -->
<aside class="admin-sidebar">
    <a href="index.php" class="admin-brand">
        <div class="admin-brand-icon">
            <i class="fa-solid fa-laptop-medical"></i>
        </div>
        <div class="admin-brand-text">
            <h2>ระบบหลังบ้าน</h2>
            <span>Digital Health Portal</span>
        </div>
    </a>

    <ul class="admin-nav">
        <li class="admin-nav-item <?= $currentPage === 'index.php' ? 'active' : '' ?>">
            <a href="index.php">
                <i class="fa-solid fa-gauge-high"></i>
                <span>แดชบอร์ดภาพรวม</span>
            </a>
        </li>
        <li class="admin-nav-item <?= in_array($currentPage, ['banners.php', 'banner-form.php']) ? 'active' : '' ?>">
            <a href="banners.php">
                <i class="fa-solid fa-images"></i>
                <span>จัดการฮีโร่แบนเนอร์</span>
            </a>
        </li>
        <li class="admin-nav-item <?= in_array($currentPage, ['news.php', 'news-form.php']) ? 'active' : '' ?>">
            <a href="news.php">
                <i class="fa-regular fa-newspaper"></i>
                <span>ข่าวสารประชาสัมพันธ์</span>
            </a>
        </li>
        <li class="admin-nav-item <?= in_array($currentPage, ['executives.php', 'executive-form.php']) ? 'active' : '' ?>">
            <a href="executives.php">
                <i class="fa-solid fa-sitemap"></i>
                <span>จัดการผังบริหาร</span>
            </a>
        </li>
        <li class="admin-nav-item <?= $currentPage === 'duty-schedules.php' ? 'active' : '' ?>">
            <a href="duty-schedules.php">
                <i class="fa-solid fa-calendar-days"></i>
                <span>จัดการตารางเวรเจ้าหน้าที่</span>
            </a>
        </li>
        <li class="admin-nav-item <?= $currentPage === 'messages.php' ? 'active' : '' ?>">
            <a href="messages.php">
                <i class="fa-regular fa-envelope"></i>
                <span>ข้อความติดต่อ</span>
            </a>
        </li>
        <li class="admin-nav-item <?= $currentPage === 'settings.php' ? 'active' : '' ?>">
            <a href="settings.php">
                <i class="fa-solid fa-sliders"></i>
                <span>ตั้งค่าระบบ & 2FA</span>
            </a>
        </li>
        <li class="admin-nav-item" style="margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 10px;">
            <a href="../index.php" target="_blank" style="color: #38bdf8;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>ดูหน้าเว็บไซต์หลัก</span>
            </a>
        </li>
    </ul>

    <div class="admin-sidebar-footer">
        <a href="../logout.php" class="admin-logout-btn">
            <i class="fa-solid fa-power-off"></i>
            <span>ออกจากระบบ</span>
        </a>
    </div>
</aside>

<!-- Main Wrapper -->
<div class="admin-main">
    <!-- Topbar -->
    <header class="admin-topbar">
        <div style="display: flex; align-items: center; gap: 15px;">
            <button id="adminSidebarToggle" class="btn btn-light btn-sm" style="display: none;" aria-label="Toggle Menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="admin-page-title">
                <h1><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'ผู้ดูแลระบบสุขภาพดิจิทัล' ?></h1>
            </div>
        </div>

        <div class="admin-user-profile">
            <div style="text-align: right; display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 13.5px; color: #1e293b;">
                    <?= htmlspecialchars($currentUser['fullname'] ?? 'Admin') ?> (<?= htmlspecialchars($currentUser['username'] ?? 'admin') ?>)
                </span>
                <span style="font-size: 12px; color: #0d9488;">
                    <i class="fa-solid fa-shield-halved"></i> 2FA: <?= htmlspecialchars($adminEmail) ?>
                </span>
            </div>
            <div class="admin-avatar">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>
    </header>

    <!-- Flash Message Notification -->
    <?php $flash = getFlash(); ?>
    <?php if ($flash): ?>
    <div style="padding: 20px 30px 0 30px;">
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" style="margin-bottom: 0;">
            <i class="fa-solid fa-circle-info"></i>
            <span><?= htmlspecialchars($flash['message']) ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content Area -->
    <main class="admin-body">
