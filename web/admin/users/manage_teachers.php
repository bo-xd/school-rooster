<?php
require_once(__DIR__ . '/../../auth/php/middleware.php');
require_auth();
$csrf = generate_csrf_token();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Teacher Manager</title>
    <link href="../css/teacher.css" rel="stylesheet"/>
</head>
<body>
    <div class="navbar">
        <form action="../../auth/php/logout.php" method="post"> <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>" /> <input type="submit" value="Logout" class="logout"/>
    </div>
</body>
</html>
