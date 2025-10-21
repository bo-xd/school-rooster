<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../auth/login.html");
    exit;
}

$user_klas = $_SESSION['klas'];

$monday_sql_date = date("Y-m-d", strtotime('monday this week'));
$tuesday_sql_date = date("Y-m-d", strtotime('tuesday this week'));
$wednesday_sql_date = date("Y-m-d", strtotime('wednesday this week'));
$thursday_sql_date = date("Y-m-d", strtotime('thursday this week'));
$friday_sql_date = date("Y-m-d", strtotime('friday this week'));

$monday_display = date("n/j/y", strtotime('monday this week'));
$tuesday_display = date("n/j/y", strtotime('tuesday this week'));
$wednesday_display = date("n/j/y", strtotime('wednesday this week'));
$thursday_display = date("n/j/y", strtotime('thursday this week'));
$friday_display = date("n/j/y", strtotime('friday this week'));


require_once(__DIR__ . '/../../server/server.php');

$rooster_data = [];

/** @var TYPE_NAME $conn */
$stmt = $conn->prepare("
    SELECT schedule_date, subject, teacher, room, begin_time, end_time 
    FROM schedule 
    WHERE klas = ? AND schedule_date BETWEEN ? AND ? 
    ORDER BY schedule_date, begin_time, end_time
");
$stmt->bind_param("sss", $user_klas, $monday_sql_date, $friday_sql_date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $rooster_data[$row['schedule_date']][] = $row;
}
$stmt->close();
$conn->close();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/Rooster.css"/>
    <title>Rooster for <?php echo htmlspecialchars($user_klas); ?></title>
</head>
<body>
<header class="navbar">
        <span style="color: white; margin-left: 15px;">
            Ingelogd als: <?php echo htmlspecialchars($_SESSION['username']); ?>
            (Klas: <?php echo htmlspecialchars($user_klas); ?>)
        </span>
    <form action="../auth/login.html"> <input type="submit" value="Logout" class="logout"/>
    </form>
</header>
<div class="Agenda">

    <div class="Maandag">
        <h4 class="DateMaandag"><?php echo $monday_display; ?><br>Maandag</h4>
        <ul class="TasksMaandag">
            <?php if (empty($rooster_data[$monday_sql_date])): ?>
                <li class="schedule-item">Geen lessen</li>
            <?php else: ?>
                <?php foreach ($rooster_data[$monday_sql_date] as $item): ?>
                    <li class="schedule-item">
                        <div class="subject"><?php echo htmlspecialchars($item['subject']); ?></div>
                        <div class="teacher"><?php echo htmlspecialchars($item['teacher']); ?></div>
                        <div class="room"><?php echo htmlspecialchars($item['room']); ?></div>
                        <div class="time-range">
                            <span class="begin_time">
                                <?php
                                    $bt = $item['begin_time'];
                                    echo htmlspecialchars(substr($bt, 0, 2) . ':' . substr($bt, 2));
                                ?>
                            </span>
                            <span class="end_time">
                                <?php
                                    $et = $item['end_time'];
                                    echo htmlspecialchars(substr($et, 0, 2) . ':' . substr($et, 2));
                                ?>
                            </span>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <div class="Dinsdag">
        <h4 class="DateDinsdag"><?php echo $tuesday_display; ?><br>Dinsdag</h4>
        <ul class="TasksDinsdag">
            <?php if (empty($rooster_data[$tuesday_sql_date])): ?>
                <li class="schedule-item">Geen lessen</li>
            <?php else: ?>
                <?php foreach ($rooster_data[$tuesday_sql_date] as $item): ?>
                    <li class="schedule-item">
                        <div class="subject"><?php echo htmlspecialchars($item['subject']); ?></div>
                        <div class="teacher"><?php echo htmlspecialchars($item['teacher']); ?></div>
                        <div class="room"><?php echo htmlspecialchars($item['room']); ?></div>
                        <div class="time-range">
                            <span class="begin_time">
                                <?php
                                    $bt = $item['begin_time'];
                                    echo htmlspecialchars(substr($bt, 0, 2) . ':' . substr($bt, 2));
                                ?>
                            </span>
                            <span class="end_time">
                                <?php
                                    $et = $item['end_time'];
                                    echo htmlspecialchars(substr($et, 0, 2) . ':' . substr($et, 2));
                                ?>
                            </span>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <div class="Woensdag">
        <h4 class="DateWoensdag"><?php echo $wednesday_display; ?><br>Woensdag</h4>
        <ul class="TasksWoensdag">
            <?php if (empty($rooster_data[$wednesday_sql_date])): ?>
                <li class="schedule-item">Geen lessen</li>
            <?php else: ?>
                <?php foreach ($rooster_data[$wednesday_sql_date] as $item): ?>
                    <li class="schedule-item">
                        <div class="subject"><?php echo htmlspecialchars($item['subject']); ?></div>
                        <div class="teacher"><?php echo htmlspecialchars($item['teacher']); ?></div>
                        <div class="room"><?php echo htmlspecialchars($item['room']); ?></div>
                        <div class="time-range">
                            <span class="begin_time">
                                <?php
                                    $bt = $item['begin_time'];
                                    echo htmlspecialchars(substr($bt, 0, 2) . ':' . substr($bt, 2));
                                ?>
                            </span>
                            <span class="end_time">
                                <?php
                                    $et = $item['end_time'];
                                    echo htmlspecialchars(substr($et, 0, 2) . ':' . substr($et, 2));
                                ?>
                            </span>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <div class="Donderdag">
        <h4 class="DateDonderdag"><?php echo $thursday_display; ?><br>Donderdag</h4>
        <ul class="TasksDonderdag">
            <?php if (empty($rooster_data[$thursday_sql_date])): ?>
                <li class="schedule-item">Geen lessen</li>
            <?php else: ?>
                <?php foreach ($rooster_data[$thursday_sql_date] as $item): ?>
                    <li class="schedule-item">
                        <div class="subject"><?php echo htmlspecialchars($item['subject']); ?></div>
                        <div class="teacher"><?php echo htmlspecialchars($item['teacher']); ?></div>
                        <div class="room"><?php echo htmlspecialchars($item['room']); ?></div>
                        <div class="time-range">
                            <span class="begin_time">
                                <?php
                                    $bt = $item['begin_time'];
                                    echo htmlspecialchars(substr($bt, 0, 2) . ':' . substr($bt, 2));
                                ?>
                            </span>
                            <span class="end_time">
                                <?php
                                    $et = $item['end_time'];
                                    echo htmlspecialchars(substr($et, 0, 2) . ':' . substr($et, 2));
                                ?>
                            </span>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <div class="Vrijdag">
        <h4 class="DateVrijdag"><?php echo $friday_display; ?><br>Vrijdag</h4>
        <ul class="TasksVrijdag">
            <?php if (empty($rooster_data[$friday_sql_date])): ?>
                <li class="schedule-item">Geen lessen</li>
            <?php else: ?>
                <?php foreach ($rooster_data[$friday_sql_date] as $item): ?>
                    <li class="schedule-item">
                        <div class="subject"><?php echo htmlspecialchars($item['subject']); ?></div>
                        <div class="teacher"><?php echo htmlspecialchars($item['teacher']); ?></div>
                        <div class="room"><?php echo htmlspecialchars($item['room']); ?></div>
                        <div class="time-range">
                            <span class="begin_time">
                                <?php
                                    $bt = $item['begin_time'];
                                    echo htmlspecialchars(substr($bt, 0, 2) . ':' . substr($bt, 2));
                                ?>
                            </span>
                            <span class="end_time">
                                <?php
                                    $et = $item['end_time'];
                                    echo htmlspecialchars(substr($et, 0, 2) . ':' . substr($et, 2));
                                ?>
                            </span>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

</div>
</body>
</html>