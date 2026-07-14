<?php

if (!function_exists('timeAgo')) {
    function timeAgo($datetime): string
    {
        if (!$datetime) return '';
        $time = strtotime($datetime);
        $diff = time() - $time;
        if ($diff < 60) return 'Baru saja';
        if ($diff < 3600) return round($diff / 60) . 'm lalu';
        if ($diff < 86400) return round($diff / 3600) . 'j lalu';
        return date('d/m/Y', $time);
    }
}
