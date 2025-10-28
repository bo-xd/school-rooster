<?php
function start_session() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        $cookieParams = session_get_cookie_params();
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        session_set_cookie_params([
            'lifetime' => $cookieParams['lifetime'],
            'path' => $cookieParams['path'],
            'domain' => $cookieParams['domain'],
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }

    if (!empty($_SESSION['deleted_time']) && $_SESSION['deleted_time'] < time() - 180) {
        session_unset();
        session_destroy();
    }
}

function generate_session_id() {
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }

    session_regenerate_id(true);

    if (isset($_SESSION['deleted_time'])) {
        unset($_SESSION['deleted_time']);
    }
}

function generate_csrf_token(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $max_age = 3600;
    if (!empty($_SESSION['csrf_token']) && !empty($_SESSION['csrf_token_time']) && ($_SESSION['csrf_token_time'] >= time() - $max_age)) {
        return $_SESSION['csrf_token'];
    }

    try {
        $token = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        $token = bin2hex(openssl_random_pseudo_bytes(32));
    }

    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();
    return $token;
}

function verify_csrf_token(?string $token): bool {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }

    $max_age = 3600;
    if (empty($_SESSION['csrf_token_time']) || $_SESSION['csrf_token_time'] < time() - $max_age) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

?>