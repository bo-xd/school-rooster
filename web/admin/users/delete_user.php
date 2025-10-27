<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../../auth/php/middleware.php');
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo 'Method not allowed';
    exit;
}

$db = mysqli_connect("localhost", "root", "", "DBAgenda");
if (!$db) {
    die('Database connection error');
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    header('Location: manage_users.php');
    exit;
}

$stmt = mysqli_prepare($db, "DELETE FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
if ($stmt && mysqli_stmt_execute($stmt)) {
    header('Location: manage_users.php');
    exit;
} else {
    echo 'Failed to delete user.';
}

