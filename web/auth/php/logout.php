<?php
require_once(__DIR__ . '/../../utils/authUtil.php');
start_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo 'Invalid request method.';
    exit;
} else header('Content-Type: application/json');

$token = $_POST['csrf'] ?? null;

if (verify_csrf_token($token)) {
    unset($_SESSION['csrf_token']);
    unset($_SESSION['csrf_token_time']);
} else {
    http_response_code(403);
    echo 'Invalid CSRF token.';
    exit;
}

session_unset();
session_destroy();

header("Location: ../../auth/login.html");
exit;
?>