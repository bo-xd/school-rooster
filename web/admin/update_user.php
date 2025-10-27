<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../auth/php/middleware.php');
require_auth();

$db = mysqli_connect("localhost", "root", "", "DBAgenda");
if (!$db) {
    die('Database connection error');
}

$error = '';
$user = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $klas = isset($_POST['klas']) ? trim($_POST['klas']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($id <= 0 || $username === '') {
        $error = 'Invalid input.';
        $user = ['id' => $id, 'username' => $username, 'klas' => $klas];
    } else {
        $check = mysqli_prepare($db, "SELECT id FROM users WHERE username = ? AND id <> ?");
        if ($check) {
            mysqli_stmt_bind_param($check, "si", $username, $id);
            mysqli_stmt_execute($check);
            mysqli_stmt_store_result($check);
            if (mysqli_stmt_num_rows($check) > 0) {
                $error = 'Username already taken by another user.';
                $user = ['id' => $id, 'username' => $username, 'klas' => $klas];
                mysqli_stmt_close($check);
            } else {
                mysqli_stmt_close($check);
                if ($password !== '') {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = mysqli_prepare($db, "UPDATE users SET username = ?, klas = ?, password = ? WHERE id = ?");
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "sssi", $username, $klas, $hash, $id);
                    }
                } else {
                    $stmt = mysqli_prepare($db, "UPDATE users SET username = ?, klas = ? WHERE id = ?");
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "ssi", $username, $klas, $id);
                    }
                }

                if (isset($stmt) && $stmt && mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    header('Location: manage_users.php');
                    exit;
                } else {
                    $error = 'Failed to update user.';
                    if (isset($stmt) && $stmt) mysqli_stmt_close($stmt);
                    $user = ['id' => $id, 'username' => $username, 'klas' => $klas];
                }
            }
        } else {
            $error = 'Database error.';
        }
    }
}

if ($user === null) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id > 0) {
        $stmt = mysqli_prepare($db, "SELECT id, username, klas FROM users WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $uid, $uname, $uklas);
            if (mysqli_stmt_fetch($stmt)) {
                $user = ['id' => $uid, 'username' => $uname, 'klas' => $uklas];
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit User</title>
    <style>
        label { display: block; margin: 8px 0; }
    </style>
</head>
<body>
    <h1>Edit User</h1>
    <?php if ($error !== ''): ?>
        <p style="color: red"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($user === null): ?>
        <p>User not found.</p>
        <p><a href="manage_users.php">Back</a></p>
    <?php else: ?>
        <form method="post" action="update_user.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
            <label>Username: <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required></label>
            <label>Klas: <input type="text" name="klas" value="<?php echo htmlspecialchars($user['klas']); ?>"></label>
            <label>New password (leave blank to keep current): <input type="password" name="password"></label>
            <div style="margin-top:10px;">
                <button type="submit">Save</button>
                <a href="manage_users.php" style="margin-left:10px;">Cancel</a>
            </div>
        </form>
    <?php endif; ?>
</body>
</html>
