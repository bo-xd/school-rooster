<?php
date_default_timezone_set(date_default_timezone_get());

function offsetweek($date, $offset) {
    if (is_numeric($date)) {
        $ts = (int)$date;
    } else {
        $dt = DateTime::createFromFormat('d/m/Y', $date) ?: new DateTime($date);
        $ts = $dt ? $dt->getTimestamp() : false;
    }
    if ($ts === false) return false;

    $offset_string = ($offset >= 0 ? '+' : '') . (int)$offset . ' week' . (abs($offset) != 1 ? 's' : '');
    return date('d/m/Y', strtotime($offset_string, $ts));
}

function returnDate($format = 'd/m/Y') {
    return date($format);
}

function sqlDateToDisplay(string $sqlDate, string $format = 'd/m/Y'): string {
    if (empty($sqlDate)) return '';

    $dt = DateTime::createFromFormat('Y-m-d', $sqlDate);
    if ($dt && $dt->format('Y-m-d') === $sqlDate) {
        return $dt->format($format);
    }

    $dt = DateTime::createFromFormat('d/m/Y', $sqlDate);
    if ($dt && $dt->format('d/m/Y') === $sqlDate) {
        return $dt->format($format);
    }

    try {
        $dt = new DateTime($sqlDate);
        return $dt->format($format);
    } catch (Exception $e) {
        return $sqlDate;
    }
}

function displayDateToSql(string $displayDate, string $format = 'Y-m-d'): string {
    if (empty($displayDate)) return '';

    $dt = DateTime::createFromFormat('d/m/Y', $displayDate);
    if ($dt && $dt->format('d/m/Y') === $displayDate) {
        return $dt->format($format);
    }

    try {
        $dt = new DateTime($displayDate);
        return $dt->format($format);
    } catch (Exception $e) {
        return $displayDate;
    }
}

function getWeekDates(int $weekOffset = 0): array {
    $base_monday = strtotime('monday this week');
    $offset_string = ($weekOffset >= 0 ? '+' : '') . $weekOffset . ' weeks';
    $monday_ts = strtotime($offset_string, $base_monday);

    return [
        'monday_ts' => $monday_ts,
        'tuesday_ts' => strtotime('+1 day', $monday_ts),
        'wednesday_ts' => strtotime('+2 days', $monday_ts),
        'thursday_ts' => strtotime('+3 days', $monday_ts),
        'friday_ts' => strtotime('+4 days', $monday_ts),
    ];
}

function isToday(string $sqlDate): bool {
    return $sqlDate === date('Y-m-d');
}

function isCurrentWeek(int $weekOffset): bool {
    return $weekOffset === 0;
}

function getCurrentDayName(): string {
    return strtolower(date('l'));
}

function getCurrentTimeHHmm(): string {
    return date('Hi');
}

