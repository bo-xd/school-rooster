<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '../../utils/DateUtil.php');
require_once(__DIR__ . '../../auth/php/middleware.php');
require_once(__DIR__ . '/../utils/authUtil.php');

require_auth();
$csrf = generate_csrf_token();

date_default_timezone_set(date_default_timezone_get());
$week_offset = isset($_GET['week']) ? (int)$_GET['week'] : 0;

$base_monday = strtotime('monday this week');
$offset_monday = strtotime(sprintf('%+d week', $week_offset), $base_monday);

$monday = date('d/m/Y', $offset_monday);
$tuesday = date('d/m/Y', strtotime('+1 day', $offset_monday));
$wednesday = date('d/m/Y', strtotime('+2 days', $offset_monday));
$thursday = date('d/m/Y', strtotime('+3 days', $offset_monday));
$friday = date('d/m/Y', strtotime('+4 days', $offset_monday));
$current_day = date('d/m/Y');
$current_time = date('H:i');
$monday_offset = $offset_monday;

$user_klas = $_SESSION['klas'];

require_once(__DIR__ . '/../../server/server.php');

$rooster_data = [];

$start_sql = displayDateToSql($monday);
$end_sql = displayDateToSql($friday);

$sql = "SELECT schedule_date, subject, teacher, room, begin_time, end_time
    FROM schedule
    WHERE klas = ? AND schedule_date BETWEEN ? AND ?
    ORDER BY schedule_date, begin_time, end_time";

/** @var mysqli $conn */
$conn = isset($conn) ? $conn : get_db_connection();
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $user_klas, $start_sql, $end_sql);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $display_date = sqlDateToDisplay($row['schedule_date']);
    $row['schedule_date_display'] = $display_date;
    $rooster_data[$display_date][] = $row;
}
$stmt->close();

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/Rooster.css"/>
    <script src="js/darkmode.js" defer></script>
    <title>Rooster for <?php echo htmlspecialchars($user_klas); ?></title>
</head>
<body>
<header class="navbar">
        <span style="color: white; margin-left: 15px;">
            Ingelogd als: <?php echo htmlspecialchars($_SESSION['username']); ?>
            (Klas: <?php echo htmlspecialchars($user_klas); ?>)
        </span>
    <form action="../auth/php/logout.php" method="post"> <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>" /> <input type="submit" value="Logout" class="logout"/>
    </form>

    <div class="Darkmode">
        <input id="Switch" type="button" value="Darkmode" />
    </div>
</header>

<div class="Agenda" >

    <div class="Maandag<?php if ($current_day === $monday) echo ' today'; ?>">
        <h4 class="DateMaandag"><?php echo $monday; ?><br>Maandag</h4>
        <ul class="TasksMaandag">
            <?php if (empty($rooster_data[$monday])): ?>
                <li class="schedule-item">Geen lessen</li>
            <?php else: ?>
                <?php foreach ($rooster_data[$monday] as $item): ?>
                    <?php
                        $bt_raw = $item['begin_time'];
                        $et_raw = $item['end_time'];
                        $bt = str_pad($bt_raw, 4, '0', STR_PAD_LEFT);
                        $et = str_pad($et_raw, 4, '0', STR_PAD_LEFT);

                        $is_current = ($current_day === $monday && $current_time >= $bt && $current_time < $et);
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

    <div class="Dinsdag<?php if ($current_day === $tuesday) echo ' today'; ?>">
        <h4 class="DateDinsdag"><?php echo $tuesday; ?><br>Dinsdag</h4>
        <ul class="TasksDinsdag">
            <?php if (empty($rooster_data[$tuesday])): ?>
                <li class="schedule-item">Geen lessen</li>
            <?php else: ?>
                <?php foreach ($rooster_data[$tuesday] as $item): ?>
                    <?php
                        $bt_raw = $item['begin_time'];
                        $et_raw = $item['end_time'];
                        $bt = str_pad($bt_raw, 4, '0', STR_PAD_LEFT);
                        $et = str_pad($et_raw, 4, '0', STR_PAD_LEFT);

                        $is_current = ($current_day === $tuesday && $current_time >= $bt && $current_time < $et);
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

    <div class="Woensdag<?php if ($current_day === $wednesday) echo ' today'; ?>">
        <h4 class="DateWoensdag"><?php echo $wednesday; ?><br>Woensdag</h4>
        <ul class="TasksWoensdag">
            <?php if (empty($rooster_data[$wednesday])): ?>
                <li class="schedule-item">Geen lessen</li>
            <?php else: ?>
                <?php foreach ($rooster_data[$wednesday] as $item): ?>
                    <?php
                        $bt_raw = $item['begin_time'];
                        $et_raw = $item['end_time'];
                        $bt = str_pad($bt_raw, 4, '0', STR_PAD_LEFT);
                        $et = str_pad($et_raw, 4, '0', STR_PAD_LEFT);

                        $is_current = ($current_day === $wednesday && $current_time >= $bt && $current_time < $et);
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

    <div class="Donderdag<?php if ($current_day === $thursday) echo ' today'; ?>">
        <h4 class="DateDonderdag"><?php echo $thursday; ?><br>Donderdag</h4>
        <ul class="TasksDonderdag">
            <?php if (empty($rooster_data[$thursday])): ?>
                <li class="schedule-item">Geen lessen</li>
            <?php else: ?>
                <?php foreach ($rooster_data[$thursday] as $item): ?>
                    <?php
                        $bt_raw = $item['begin_time'];
                        $et_raw = $item['end_time'];
                        $bt = str_pad($bt_raw, 4, '0', STR_PAD_LEFT);
                        $et = str_pad($et_raw, 4, '0', STR_PAD_LEFT);

                        $is_current = ($current_day === $thursday && $current_time >= $bt && $current_time < $et);
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

    <div class="Vrijdag<?php if ($current_day === $friday) echo ' today'; ?>">
        <h4 class="DateVrijdag"><?php echo $friday; ?><br>Vrijdag</h4>
        <ul class="TasksVrijdag">
            <?php if (empty($rooster_data[$friday])): ?>
                <li class="schedule-item">Geen lessen</li>
            <?php else: ?>
                <?php foreach ($rooster_data[$friday] as $item): ?>
                    <?php
                        $bt_raw = $item['begin_time'];
                        $et_raw = $item['end_time'];
                        $bt = str_pad($bt_raw, 4, '0', STR_PAD_LEFT);
                        $et = str_pad($et_raw, 4, '0', STR_PAD_LEFT);

                        $is_current = ($current_day === $friday && $current_time >= $bt && $current_time < $et);
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
    <a class="week-nav__btn" href="?week=<?php echo $week_offset - 1; ?>" aria-label="Vorige week">‹</a>
    <div class="week-nav__label">Week <?php echo date('W', $monday_offset); ?></div>
    <a class="week-nav__btn" href="?week=<?php echo $week_offset + 1; ?>" aria-label="Volgende week">›</a>
    <a class="week-huidigeweek" href="?week=0">ga naar huidige week</a>
</nav>

</body>
</html>

