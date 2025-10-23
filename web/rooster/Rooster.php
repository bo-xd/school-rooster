<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '../../auth/php/middleware.php');
require_auth();

$user_klas = $_SESSION['klas'];

$week_offset = isset($_GET['week']) ? intval($_GET['week']) : 0;

$base_monday = strtotime('monday this week');
$monday_ts = strtotime("{$week_offset} weeks", $base_monday);
$tuesday_ts = strtotime('+1 day', $monday_ts);
$wednesday_ts = strtotime('+2 days', $monday_ts);
$thursday_ts = strtotime('+3 days', $monday_ts);
$friday_ts = strtotime('+4 days', $monday_ts);

$monday_sql_date = date("Y-m-d", $monday_ts);
$tuesday_sql_date = date("Y-m-d", $tuesday_ts);
$wednesday_sql_date = date("Y-m-d", $wednesday_ts);
$thursday_sql_date = date("Y-m-d", $thursday_ts);
$friday_sql_date = date("Y-m-d", $friday_ts);

$monday_display = date("n/j/y", $monday_ts);
$tuesday_display = date("n/j/y", $tuesday_ts);
$wednesday_display = date("n/j/y", $wednesday_ts);
$thursday_display = date("n/j/y", $thursday_ts);
$friday_display = date("n/j/y", $friday_ts);

$prev_week = $week_offset - 1;
$next_week = $week_offset + 1;

require_once(__DIR__ . '/../../server/server.php');

$rooster_data = [];

$sql = "SELECT schedule_date, subject, teacher, room, begin_time, end_time
    FROM schedule
    WHERE klas = ? AND schedule_date BETWEEN ? AND ?
    ORDER BY schedule_date, begin_time, end_time";

/** @var TYPE_NAME $conn */
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $user_klas, $monday_sql_date, $friday_sql_date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $rooster_data[$row['schedule_date']][] = $row;
}
$stmt->close();
$conn->close();

date_default_timezone_set('Europe/Rome');
$current_day = strtolower(date('l'));
$current_time = date('Hi');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/Rooster.css"/>
    <script src="index.js" defer></script>
    <title>Rooster for <?php echo htmlspecialchars($user_klas); ?></title>
</head>
<body>
<header class="navbar">
        <span style="color: white; margin-left: 15px;">
            Ingelogd als: <?php echo htmlspecialchars($_SESSION['username']); ?>
            (Klas: <?php echo htmlspecialchars($user_klas); ?>)
        </span>
    <form action="../auth/php/logout.php"> <input type="submit" value="Logout" class="logout"/>
    </form>

    <div class="Darkmode">
        <input id="Switch" type="button" value="Darkmode" />
    </div>
</header>

<div class="Agenda">

    <div class="Maandag">
        <h4 class="DateMaandag"><?php echo $monday_display; ?><br>Maandag</h4>
        <ul class="TasksMaandag">
            <?php if (empty($rooster_data[$monday_sql_date])): ?>
                <li class="schedule-item">Geen lessen</li>
            <?php else: ?>
                <?php foreach ($rooster_data[$monday_sql_date] as $item): ?>
                    <?php
                        $bt_raw = $item['begin_time'];
                        $et_raw = $item['end_time'];
                        $bt = str_pad($bt_raw, 4, '0', STR_PAD_LEFT);
                        $et = str_pad($et_raw, 4, '0', STR_PAD_LEFT);

                        $is_current = ($week_offset === 0 && $current_day === 'monday' && $current_time >= $bt && $current_time < $et);
                    ?>
                    <li class="schedule-item<?php if ($is_current) echo ' current-lesson'; ?>">
                        <div class="subject"><?php echo htmlspecialchars($item['subject']); ?></div>
                        <div class="teacher"><?php echo htmlspecialchars($item['teacher']); ?></div>
                        <div class="room"><?php echo htmlspecialchars($item['room']); ?></div>
                        <div class="time-range">
                            <span class="begin_time">
                                <?php
                                    echo htmlspecialchars(substr($bt, 0, 2) . ':' . substr($bt, 2));
                                ?>
                            </span>
                            <span class="end_time">
                                <?php
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
                    <?php
                        $bt_raw = $item['begin_time'];
                        $et_raw = $item['end_time'];
                        $bt = str_pad($bt_raw, 4, '0', STR_PAD_LEFT);
                        $et = str_pad($et_raw, 4, '0', STR_PAD_LEFT);

                        $is_current = ($week_offset === 0 && $current_day === 'tuesday' && $current_time >= $bt && $current_time < $et);
                    ?>
                    <li class="schedule-item<?php if ($is_current) echo ' current-lesson'; ?>">
                        <div class="subject"><?php echo htmlspecialchars($item['subject']); ?></div>
                        <div class="teacher"><?php echo htmlspecialchars($item['teacher']); ?></div>
                        <div class="room"><?php echo htmlspecialchars($item['room']); ?></div>
                        <div class="time-range">
                            <span class="begin_time">
                                <?php
                                    echo htmlspecialchars(substr($bt, 0, 2) . ':' . substr($bt, 2));
                                ?>
                            </span>
                            <span class="end_time">
                                <?php
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
                    <?php
                        $bt_raw = $item['begin_time'];
                        $et_raw = $item['end_time'];
                        $bt = str_pad($bt_raw, 4, '0', STR_PAD_LEFT);
                        $et = str_pad($et_raw, 4, '0', STR_PAD_LEFT);

                        $is_current = ($week_offset === 0 && $current_day === 'wednesday' && $current_time >= $bt && $current_time < $et);
                    ?>
                    <li class="schedule-item<?php if ($is_current) echo ' current-lesson'; ?>">
                        <div class="subject"><?php echo htmlspecialchars($item['subject']); ?></div>
                        <div class="teacher"><?php echo htmlspecialchars($item['teacher']); ?></div>
                        <div class="room"><?php echo htmlspecialchars($item['room']); ?></div>
                        <div class="time-range">
                            <span class="begin_time">
                                <?php
                                    echo htmlspecialchars(substr($bt, 0, 2) . ':' . substr($bt, 2));
                                ?>
                            </span>
                            <span class="end_time">
                                <?php
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
                    <?php
                        $bt_raw = $item['begin_time'];
                        $et_raw = $item['end_time'];
                        $bt = str_pad($bt_raw, 4, '0', STR_PAD_LEFT);
                        $et = str_pad($et_raw, 4, '0', STR_PAD_LEFT);

                        $is_current = ($week_offset === 0 && $current_day === 'thursday' && $current_time >= $bt && $current_time < $et);
                    ?>
                    <li class="schedule-item<?php if ($is_current) echo ' current-lesson'; ?>">
                        <div class="subject"><?php echo htmlspecialchars($item['subject']); ?></div>
                        <div class="teacher"><?php echo htmlspecialchars($item['teacher']); ?></div>
                        <div class="room"><?php echo htmlspecialchars($item['room']); ?></div>
                        <div class="time-range">
                            <span class="begin_time">
                                <?php
                                    echo htmlspecialchars(substr($bt, 0, 2) . ':' . substr($bt, 2));
                                ?>
                            </span>
                            <span class="end_time">
                                <?php
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
                    <?php
                        $bt_raw = $item['begin_time'];
                        $et_raw = $item['end_time'];
                        $bt = str_pad($bt_raw, 4, '0', STR_PAD_LEFT);
                        $et = str_pad($et_raw, 4, '0', STR_PAD_LEFT);

                        $is_current = ($week_offset === 0 && $current_day === 'friday' && $current_time >= $bt && $current_time < $et);
                    ?>
                    <li class="schedule-item<?php if ($is_current) echo ' current-lesson'; ?>">
                        <div class="subject"><?php echo htmlspecialchars($item['subject']); ?></div>
                        <div class="teacher"><?php echo htmlspecialchars($item['teacher']); ?></div>
                        <div class="room"><?php echo htmlspecialchars($item['room']); ?></div>
                        <div class="time-range">
                            <span class="begin_time">
                                <?php
                                    echo htmlspecialchars(substr($bt, 0, 2) . ':' . substr($bt, 2));
                                ?>
                            </span>
                            <span class="end_time">
                                <?php
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

<nav class="week-nav" aria-label="Week navigation">
    <a class="week-nav__btn" href="?week=<?php echo $prev_week; ?>" aria-label="Vorige week">‹</a>
    <div class="week-nav__label">Week van <?php echo htmlspecialchars($monday_display); ?></div>
    <a class="week-nav__btn" href="?week=<?php echo $next_week; ?>" aria-label="Volgende week">›</a>
</nav>

</body>
</html>