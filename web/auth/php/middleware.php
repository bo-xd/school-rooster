<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function require_auth(string $loginPath = '../auth/login.html')
{
    if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin') {
        return;
    }

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || (!isset($_SESSION['username']) && !isset($_SESSION['klas']))) {
        header("Location: $loginPath");
        exit;
    }
}
?>