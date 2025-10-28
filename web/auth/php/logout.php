<?php
require_once(__DIR__ . '/../../utils/authUtil.php');
start_session();

session_unset();
session_destroy();

header("Location: ../../auth/login.html");
exit;
?>