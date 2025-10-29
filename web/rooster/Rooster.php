<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '../../auth/php/middleware.php');
require_auth();
require_once(__DIR__ . '/../utils/authUtil.php');
require_once(__DIR__ . '/../utils/DateUtil.php');
$csrf = generate_csrf_token();

$user_klas = $_SESSION['klas'];
$week_offset = isset($_GET['week']) ? intval($_GET['week']) : 0;

$week_dates = getWeekDates($week_offset);

$days = [
    'monday' => [
        'ts' => $week_dates['monday_ts'],
        'sql_date' => date('Y-m-d', $week_dates['monday_ts']),
        'display' => date('j/n/y', $week_dates['monday_ts']),
        'display_full' => date('d/m/Y', $week_dates['monday_ts']),
        'name' => 'Maandag'
    ],
    'tuesday' => [
        'ts' => $week_dates['tuesday_ts'],
        'sql_date' => date('Y-m-d', $week_dates['tuesday_ts']),
        'display' => date('j/n/y', $week_dates['tuesday_ts']),
        'display_full' => date('d/m/Y', $week_dates['tuesday_ts']),
        'name' => 'Dinsdag'
    ],
    'wednesday' => [
        'ts' => $week_dates['wednesday_ts'],
        'sql_date' => date('Y-m-d', $week_dates['wednesday_ts']),
        'display' => date('j/n/y', $week_dates['wednesday_ts']),
        'display_full' => date('d/m/Y', $week_dates['wednesday_ts']),
        'name' => 'Woensdag'
    ],
    'thursday' => [
        'ts' => $week_dates['thursday_ts'],
        'sql_date' => date('Y-m-d', $week_dates['thursday_ts']),
        'display' => date('j/n/y', $week_dates['thursday_ts']),
        'display_full' => date('d/m/Y', $week_dates['thursday_ts']),
        'name' => 'Donderdag'
    ],
    'friday' => [
        'ts' => $week_dates['friday_ts'],
        'sql_date' => date('Y-m-d', $week_dates['friday_ts']),
        'display' => date('j/n/y', $week_dates['friday_ts']),
        'display_full' => date('d/m/Y', $week_dates['friday_ts']),
        'name' => 'Vrijdag'
    ]
];

$week_number = date('W', $week_dates['monday_ts']);
$prev_week = $week_offset - 1;
$next_week = $week_offset + 1;

$current_day = getCurrentDayName();
$current_time = getCurrentTimeHHmm();
$is_current_week = isCurrentWeek($week_offset);

require_once(__DIR__ . '/../../server/server.php');

$rooster_data = [];
$sql = "SELECT schedule_date, subject, teacher, room, begin_time, end_time
    FROM schedule
    WHERE klas = ? AND schedule_date BETWEEN ? AND ?
    ORDER BY schedule_date, begin_time, end_time";

/** @var mysqli $conn */
$conn = isset($conn) ? $conn : get_db_connection();
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $user_klas, $days['monday']['sql_date'], $days['friday']['sql_date']);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $rooster_data[$row['schedule_date']][] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooster - <?php echo htmlspecialchars($user_klas); ?></title>
    <link rel="stylesheet" href="css/Rooster.css">
</head>
<body>

<div class="Agenda">
    <?php foreach ($days as $day_key => $day): ?>
        <?php
        $is_today = ($is_current_week && $current_day === $day_key);
        $day_class = ucfirst($day['name']);
        ?>
        <div class="<?php echo $day_class; ?><?php if ($is_today) echo ' today'; ?>">
            <h4 class="Date<?php echo $day_class; ?>"><?php echo htmlspecialchars($day['display']); ?><br><?php echo htmlspecialchars($day['name']); ?></h4>
            <ul class="Tasks<?php echo $day_class; ?>">
                <?php if (empty($rooster_data[$day['sql_date']])): ?>
                    <li class="schedule-item">Geen lessen</li>
                <?php else: ?>
                    <?php foreach ($rooster_data[$day['sql_date']] as $item): ?>
                        <?php
                        $bt_raw = $item['begin_time'];
                        $et_raw = $item['end_time'];
                        $bt = str_pad($bt_raw, 4, '0', STR_PAD_LEFT);
                        $et = str_pad($et_raw, 4, '0', STR_PAD_LEFT);

                        $is_current = ($is_today && $current_time >= $bt && $current_time < $et);
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
    <?php endforeach; ?>
</div>

<nav class="week-nav" aria-label="Week navigation">
    <a class="week-nav__btn" href="?week=<?php echo $prev_week; ?>" aria-label="Vorige week">‹</a>
    <div class="week-nav__label">Week <?php echo htmlspecialchars($week_number); ?></div>
    <a class="week-nav__btn" href="?week=<?php echo $next_week; ?>" aria-label="Volgende week">›</a>
    <a class="week-huidigeweek" href="?week=0">ga naar huidige week</a>
</nav>

</body>
</html>