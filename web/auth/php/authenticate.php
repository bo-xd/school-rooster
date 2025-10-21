<?php
session_start();
error_reporting(E_ALL);
require_once(__DIR__ . '/../../../server/server.php');

/** @var TYPE_NAME $conn */
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Username and password are required.']);
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
            $_SESSION['klas'] = $row['klas'];

            header("Location: ../../rooster/Rooster.php");
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>