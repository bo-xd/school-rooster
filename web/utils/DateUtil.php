<?php
date_default_timezone_set(date_default_timezone_get());
$monday = strtotime('monday this week');
$tuesday = strtotime('tuesday this week');
$wednesday = strtotime('wednesday this week');
$thursday = strtotime('thursday this week');
$friday = strtotime('friday this week');

function offsetweek($date, $offset) {
    if (is_numeric($date)) {
        $ts = (int)$date;
    } else {
        $dt = DateTime::createFromFormat('d/m/Y', $date) ?: new DateTime($date);
        $ts = $dt ? $dt->getTimestamp() : false;
    }
    if ($ts === false) return false;
    $rel = sprintf('%+d week', (int)$offset);
    return date('d/m/Y', strtotime($rel, $ts));
}

function returnDate() {
    return date('d/m/Y');
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
