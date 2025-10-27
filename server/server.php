<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DBAgenda";

/**
 * Return a persistent mysqli connection (singleton). Creates database and tables if needed.
 * Use $conn = get_db_connection(); in including scripts, or rely on the global $conn set below.
 *
 * @return mysqli
 */
function get_db_connection() {
    global $servername, $username, $password, $dbname;

    // If a connection already exists and is valid, return it
    if (isset($GLOBALS['conn']) && $GLOBALS['conn'] instanceof mysqli && $GLOBALS['conn']->ping()) {
        return $GLOBALS['conn'];
    }

    // Create connection without specifying database so we can create it if missing
    $conn = new mysqli($servername, $username, $password);

    if ($conn->connect_error) {
        // Use a clear error for web pages to handle
        die("Connection failed: " . $conn->connect_error);
    }

    // Create database if it doesn't exist
    $createDbSql = "CREATE DATABASE IF NOT EXISTS `$dbname`";
    if ($conn->query($createDbSql) === FALSE) {
        die("Error creating database: " . $conn->error);
    }

    // Select the database
    if (!$conn->select_db($dbname)) {
        die("Error selecting database $dbname: " . $conn->error);
    }

    // Create tables (run each CREATE TABLE separately)
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

    // Ensure 'klas' exists (some installations may have an older schedule table without this column)
    // Robust check using INFORMATION_SCHEMA to avoid relying on ALTER ... IF NOT EXISTS support
    $dbEsc = $conn->real_escape_string($dbname);
    $colCheckSql = "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='" . $dbEsc . "' AND TABLE_NAME='schedule' AND COLUMN_NAME='klas'";
    $colRes = $conn->query($colCheckSql);
    if ($colRes === FALSE) {
        // If information_schema query fails, try a SHOW COLUMNS fallback
        $colRes = $conn->query("SHOW COLUMNS FROM schedule LIKE 'klas'");
        if ($colRes === FALSE) {
            // Give up with a helpful error
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

    // Users table used by auth
    $createUsersTable = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        klas VARCHAR(20) DEFAULT NULL
    )";
    if ($conn->query($createUsersTable) === FALSE) {
        die("Error creating users table: " . $conn->error);
    }

    // Store in global so subsequent includes reuse the same mysqli object
    $GLOBALS['conn'] = $conn;

    return $conn;
}

// Initialize a global $conn variable for backward compatibility
if (!isset($conn) || !($conn instanceof mysqli) || !@$conn->ping()) {
    $conn = get_db_connection();
}

// Note: Do NOT close $conn here. Closing should be the responsibility of the script that opened/owns it.
?>
