<?php
declare(strict_types=1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
function require_auth(string $loginPath = '../auth/login.html') {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: $loginPath");
        exit;
    }
}

?>