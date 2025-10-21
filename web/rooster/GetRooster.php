<?php
session_start();
require_once(__DIR__ . '/../../server/server.php');

header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$user_klas = $_SESSION['klas'];

$rooster_data = [ 1 => [], 2 => [], 3 => [], 4 => [], 5 => [] ];

/** @var TYPE_NAME $conn */
$stmt = $conn->prepare("SELECT day_of_week, subject, teacher, room, begin_time, end_time FROM schedule WHERE klas = ? ORDER BY time");
$stmt->bind_param("s", $user_klas);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $rooster_data[$row['day_of_week']][] = $row;
}
$stmt->close();
$conn->close();

echo json_encode(['status' => 'success', 'data' => $rooster_data]);
?>