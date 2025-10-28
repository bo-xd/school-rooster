<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('session.use_strict_mode', 1);
require_once(__DIR__ . '/../../../server/server.php');
require_once(__DIR__ . '/../../utils/authUtil.php');

start_session();


/** @var mysqli $conn */
$conn = get_db_connection();

if ($conn->connect_error) {
    header("Location: ../login.html?error=dberr");
    exit;
}

function showerror($code = 'invalid', $username = '') {
    $location = '../login.html?error=' . urlencode($code);
    if (!empty($username)) {
        $location .= '&user=' . urlencode($username);
    }
    header("Location: " . $location);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (empty($username) || empty($password)) {
        showerror('empty', $username);
        exit;
    }

    $stmt = $conn->prepare("SELECT password, klas, username FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $row['username'];
            generate_session_id();

            if ($row['username'] === 'admin') {
                header("Location: ../../admin/Panel.php");
                exit;
            }

            $_SESSION['klas'] = $row['klas'];
            header("Location: ../../rooster/Rooster.php");
            exit;
        } else {
            // Invalid password
            showerror('invalid', $username);
        }
    } else {
        // Username not found
        showerror('invalid', $username);
    }
} else {
    // Invalid method
    showerror('method');
}
?>