<?php
declare(strict_types=1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function getcurrentuser(): ?array {
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        return [
            'username' => $_SESSION['username'] ?? null,
            'klas' => $_SESSION['klas'] ?? null
        ];
    }
    return null;
}

function require_auth(string $loginPath = '../auth/login.html'): void {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: $loginPath");
        exit;
    }
}


function ensure_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

?>