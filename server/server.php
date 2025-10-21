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

$createTableSql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    klas VARCHAR(5) NOT NULL
)";

if ($conn->query($createTableSql) === FALSE) {
    die("Error creating table: " . $conn->error);
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo("Connected successfully");
?>
