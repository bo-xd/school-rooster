<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once(__DIR__ . '/../../../server/server.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $klas = isset($_POST['klas']) ? $_POST['klas'] : null;

    if (!$username || !$password || !$klas) {
        die("All fields are required.");
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("Username already exists.");
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO users (username, password, klas) VALUES (?, ?, ?)");
    $hashedPassword = password_hash(trim($password), PASSWORD_DEFAULT);
    $stmt->bind_param("sss", $username, $hashedPassword, $klas);

    if ($stmt->execute() === FALSE) {
        die("Error inserting data: " . $stmt->error);
    }
    $stmt->close();

    echo "Account created successfully";
}
?>
