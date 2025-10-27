<?php
require_once(__DIR__ . '/../auth/php/middleware.php');
require_auth();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>admin panel</title>
    <link href="css/Panel.css" rel="stylesheet">
</head>
<body>
    <div class="navbar">
        <form action="../auth/php/logout.php"> <input type="submit" value="Logout" class="logout"/>
    </div>
    <div class="admin-panel">
        <h1>Admin Panel</h1>
        <nav>
            <ul>
                <li><a href="users/manage_users.php">Manage Users</a></li>
                <li><a href="users/manage_teachers.php">Manager Teachers</a></li>
                <li><a href="rooster/rooster.php">Manager Rooster</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>
