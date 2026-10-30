<?php
// logout.php - ออกจากระบบ
require_once __DIR__ . '/config/functions.php';

// Unset all session values
$_SESSION = [];

// Destroy session cookie if set
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Start fresh session to set flash notice
session_start();
setFlash('info', 'คุณได้ออกจากระบบเรียบร้อยแล้ว');
header('Location: login.php');
exit;
