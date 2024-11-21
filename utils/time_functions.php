<?php
function timeAgo($datetime) {
    if (!$datetime) return "just now";
    
    $timestamp = strtotime($datetime);
    $now = time();
    $diff = $now - $timestamp;

    // If the timestamp is in the future or too old, use current time
    if ($diff < 0 || $diff > (365 * 24 * 60 * 60)) {
        $timestamp = time();
        $diff = 0;
    }

    if ($diff < 60) {
        return "just now";
    }

    $intervals = array(
        1                 => array('minute', 'minutes'),
        60                => array('hour', 'hours'),
        1440             => array('day', 'days'),
        10080            => array('week', 'weeks'),
        43200            => array('month', 'months'),
        525600           => array('year', 'years')
    );

    foreach ($intervals as $minutes => $labels) {
        $divisor = $minutes * 60;
        
        if ($diff < $divisor) {
            $interval = floor($diff / ($divisor / $minutes));
            $label = $interval == 1 ? $labels[0] : $labels[1];
            return "$interval $label ago";
        }
    }

    // If we get here, just show the actual date
    return date('M j, Y', $timestamp);
}
?> 