<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DBAgenda";

$conn = new mysqli($servername, $username, $password, $dbname);
$createDbSql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($createDbSql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

$conn->select_db($dbname);

$createTableSql = "CREATE TABLE IF NOT EXISTS schedule (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    klas VARCHAR(10) NOT NULL,
    day_of_week INT(1) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    teacher VARCHAR(10) NOT NULL,
    room VARCHAR(10) NOT NULL,
    time VARCHAR(10) NOT NULL
)";

if ($conn->query($createTableSql) === FALSE) {
    die("Error creating table: " . $conn->error);
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
