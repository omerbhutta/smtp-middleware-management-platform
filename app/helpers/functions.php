<?php
function asset($path)
{
    return $path;
}

function route($route)
{
    return ltrim($route, '/');
}

function old($key, $default = '')
{
    return $_POST[$key] ?? $default;
}

function escape($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function truncate($str, $length = 50)
{
    if (mb_strlen($str) <= $length) return $str;
    return mb_substr($str, 0, $length) . '...';
}

function timeAgo($datetime)
{
    if (!$datetime) return 'Never';
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    return date('M j, Y', $timestamp);
}

function flash($key = null, $value = null)
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return;
    }
    if ($key !== null) {
        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }
    $flashes = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $flashes;
}

function flashHas($key)
{
    return isset($_SESSION['_flash'][$key]);
}

function formatNumber($num)
{
    if ($num >= 1000000) return round($num / 1000000, 1) . 'M';
    if ($num >= 1000) return round($num / 1000, 1) . 'K';
    return $num;
}

function generateApiKey()
{
    return bin2hex(random_bytes(32));
}

function generateSecretKey()
{
    return bin2hex(random_bytes(32));
}
