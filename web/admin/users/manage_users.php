<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../../auth/php/middleware.php');
require_auth();
$csrf = generate_csrf_token();

$db = mysqli_connect("localhost", "root", "", "DBAgenda");
if (!$db) {
    die('Database connection error');
}
$users = mysqli_query($db, "SELECT * FROM `users`");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../css/users.css">
</head>
<body>
    <div class="navbar">
        <form action="../../auth/php/logout.php" method="post"> <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>" /> <input type="submit" value="Logout" class="logout"/>
    </div>
    <h1>Manage Users</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Klas</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
<?php
while ($user = mysqli_fetch_assoc($users)) {
    ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['klas']); ?></td>
                <td>
                    <a href="update_user.php?id=<?php echo urlencode($user['id']); ?>">Edit</a>
                    &nbsp;|&nbsp;
                    <form class="inline" method="post" action="delete_user.php" onsubmit="return confirm('Delete this user?');">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
    <?php
}
?>
        </tbody>
    </table>

    <p><a href="../Panel.php">Back to Panel</a></p>
</body>
</html>
