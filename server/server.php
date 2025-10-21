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
    schedule_date INT(10) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    teacher VARCHAR(10) NOT NULL,
    room VARCHAR(10) NOT NULL,
    begin_time VARCHAR(4) NOT NULL,
    end_time VARCHAR(4) NOT NULL
)";

if ($conn->query($createTableSql) === FALSE) {
    die("Error creating table: " . $conn->error);
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

