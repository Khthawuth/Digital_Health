<?php
// admin/includes/auth_check.php - Admin Session Guard
require_once __DIR__ . '/../../config/functions.php';

if (!isLoggedIn()) {
    setFlash('warning', 'กรุณาเข้าสู่ระบบก่อนเข้าใช้งานส่วนผู้ดูแลระบบ');
    header('Location: ../login.php');
    exit;
}

if (!isAdmin()) {
    setFlash('danger', 'คุณไม่มีสิทธิ์เข้าถึงหน้าระบบผู้ดูแลระบบ (Admin Only)');
    header('Location: ../index.php');
    exit;
}

$currentUser = getCurrentUser();
$adminEmail = $currentUser['email'] ?? 'Khthawuth.leo@gmail.com';
