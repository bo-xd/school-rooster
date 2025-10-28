<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DBAgenda";

function get_db_connection() {
    global $servername, $username, $password, $dbname;

    $conn = new mysqli($servername, $username, $password);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $createDbSql = "CREATE DATABASE IF NOT EXISTS `$dbname`";
    if ($conn->query($createDbSql) === FALSE) {
        die("Error creating database: " . $conn->error);
    }

    if (!$conn->select_db($dbname)) {
        die("Error selecting database $dbname: " . $conn->error);
    }

    $createScheduleTable = "CREATE TABLE IF NOT EXISTS schedule (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        room VARCHAR(10) NOT NULL,
        klas VARCHAR(20) DEFAULT NULL,
        schedule_date DATE NOT NULL,
        subject VARCHAR(100) NOT NULL,
        teacher VARCHAR(50) NOT NULL,
        begin_time VARCHAR(10) NOT NULL,
        end_time VARCHAR(10) NOT NULL
    )";

    if ($conn->query($createScheduleTable) === FALSE) {
        die("Error creating schedule table: " . $conn->error);
    }

    $dbEsc = $conn->real_escape_string($dbname);
    $colCheckSql = "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='" . $dbEsc . "' AND TABLE_NAME='schedule' AND COLUMN_NAME='klas'";
    $colRes = $conn->query($colCheckSql);
    if ($colRes === FALSE) {
        $colRes = $conn->query("SHOW COLUMNS FROM schedule LIKE 'klas'");
        if ($colRes === FALSE) {
            die("Error checking for 'klas' column: " . $conn->error);
        }
        $hasKlas = ($colRes->num_rows > 0);
    } else {
        $row = $colRes->fetch_assoc();
        $hasKlas = ((int)($row['cnt'] ?? 0) > 0);
    }

    if (!$hasKlas) {
        if ($conn->query("ALTER TABLE schedule ADD COLUMN klas VARCHAR(20) DEFAULT NULL") === FALSE) {
            die("Error adding klas column: " . $conn->error);
        }
    }

    $createTeacherTable = "CREATE TABLE IF NOT EXISTS teacher (
       teacher VARCHAR(50) NOT NULL
    )";
    if ($conn->query($createTeacherTable) === FALSE) {
        die("Error creating teacher table: " . $conn->error);
    }

    $createSubjectTable = "CREATE TABLE IF NOT EXISTS subject (
        subject VARCHAR(100) NOT NULL
    )";
    if ($conn->query($createSubjectTable) === FALSE) {
        die("Error creating subject table: " . $conn->error);
    }

    $createRoomTable = "CREATE TABLE IF NOT EXISTS room (
        room VARCHAR(10)  NOT NULL
    )";
    if ($conn->query($createRoomTable) === FALSE) {
        die("Error creating room table: " . $conn->error);
    }

    $createUsersTable = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        klas VARCHAR(20) DEFAULT NULL
    )";
    if ($conn->query($createUsersTable) === FALSE) {
        die("Error creating users table: " . $conn->error);
    }

    $GLOBALS['conn'] = $conn;

    return $conn;
}

if (!isset($conn) || !($conn instanceof mysqli) || !@$conn->ping()) {
    $conn = get_db_connection();
}

?>
