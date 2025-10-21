<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/Rooster.css"/>
    <title>Rooster</title>
</head>
<body>
<?php
    $monday = date("n/j/y", strtotime('monday this week'));
    $tuesday = date("n/j/y", strtotime('tuesday this week'));
    $wednesday = date("n/j/y", strtotime('wednesday this week'));
    $thursday = date("n/j/y", strtotime('thursday this week'));
    $friday = date("n/j/y", strtotime('friday this week'));
?>
    <header class="navbar">
        <form action="../auth/login.html">
            <input type="submit" value="Logout" class="logout"/>
        </form>
    </header>
    <div class="Agenda">
        <div class="Maandag">
            <h4 class="DateMaandag"><?php echo $monday; ?><br>Maandag</h4>
            <ul class="TasksMaandag">
                <li class="schedule-item">
                    <div class="subject">Styling en positionering</div>
                    <div class="teacher">DAKL</div>
                    <div class="room">2.04</div>
                    <div class="time">12:30</div>
                </li>
                <li class="schedule-item">
                    <div class="subject">Nederlands</div>
                    <div class="teacher">DKSJ</div>
                    <div class="room">2.20</div>
                    <div class="time">12:30</div>
                </li>
            </ul>
        </div>
        <div class="Dinsdag">
            <h4 class="DateDinsdag"><?php echo $tuesday; ?><br>Dinsdag</h4>
            <ul class="TasksDinsdag">
                <li class="schedule-item">
                    <div class="subject">Styling en positionering</div>
                    <div class="teacher">DAKL</div>
                    <div class="room">2.04</div>
                    <div class="time">12:30</div>
                </li>
                <li class="schedule-item">
                    <div class="subject">Nederlands</div>
                    <div class="teacher">DKSJ</div>
                    <div class="room">2.20</div>
                    <div class="time">12:30</div>
                </li>
            </ul>
        </div>
        <div class="Woensdag">
            <h4 class="DateWoensdag"><?php echo $wednesday; ?><br>Woensdag</h4>
            <ul class="TasksWoensdag">
                <li class="schedule-item">
                    <div class="subject">Styling en positionering</div>
                    <div class="teacher">DAKL</div>
                    <div class="room">2.04</div>
                    <div class="time">12:30</div>
                </li>
                <li class="schedule-item">
                    <div class="subject">Nederlands</div>
                    <div class="teacher">DKSJ</div>
                    <div class="room">2.20</div>
                    <div class="time">12:30</div>
                </li>
                <li class="schedule-item">
                    <div class="subject">Styling en positionering</div>
                    <div class="teacher">DAKL</div>
                    <div class="room">2.04</div>
                    <div class="time">12:30</div>
                </li>
            </ul>
        </div>
        <div class="Donderdag">
            <h4 class="DateDonderdag"><?php echo $thursday; ?><br>Donderdag</h4>
            <ul class="TasksDonderdag">
                <li class="schedule-item">
                    <div class="subject">Styling en positionering</div>
                    <div class="teacher">DAKL</div>
                    <div class="room">2.04</div>
                    <div class="time">12:30</div>
                </li>
                <li class="schedule-item">
                    <div class="subject">Nederlands</div>
                    <div class="teacher">DKSJ</div>
                    <div class="room">2.20</div>
                    <div class="time">12:30</div>
                </li>
            </ul>
        </div>
        <div class="Vrijdag">
            <h4 class="DateVrijdag"><?php echo $friday; ?><br>Vrijdag</h4>
            <ul class="TasksVrijdag">
                <li class="schedule-item">
                    <div class="subject">Nederlands</div>
                    <div class="teacher">DKSJ</div>
                    <div class="room">2.20</div>
                    <div class="time">12:30</div>
                </li>
                <li class="schedule-item">
                    <div class="subject">Styling en positionering</div>
                    <div class="teacher">DAKL</div>
                    <div class="room">2.04</div>
                    <div class="time">12:30</div>
                </li>
            </ul>
        </div>
    </div>
</body>
</html>