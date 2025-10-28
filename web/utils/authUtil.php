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
        };

        session_start();

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
?>