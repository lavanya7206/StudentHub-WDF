<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: a5.php');
    exit;
}
$timeout = 900;

if (
    !isset($_SESSION['last_activity']) ||
    time() - $_SESSION['last_activity'] >= $timeout
) {
    $_SESSION = [];
    session_destroy();

    header('Location: a5.php?timeout=1');
    exit;
}
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied. Admin privileges required.');
}

$_SESSION['last_activity'] = time();
?>
