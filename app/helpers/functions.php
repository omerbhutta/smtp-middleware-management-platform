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

function notifyAllUsers($subject, $body)
{
    try {
        $db = Database::getInstance();
        $users = $db->fetchAll("SELECT email, full_name, username FROM users WHERE status = 'active'");
        foreach ($users as $user) {
            SmtpMailer::sendPortalEmail(
                $user['email'],
                $subject,
                $body
            );
        }
    } catch (Exception $e) {
        // silently fail — email delivery is best-effort
    }
}

function portalUrl()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . BASE_URL;
}

function activityEmailTemplate($title, $message)
{
    global $app_name;
    return '<div style="font-family:Arial,Helvetica,sans-serif;max-width:560px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">'
        . '<div style="background:linear-gradient(135deg,#3b82f6,#06b6d4);padding:32px;text-align:center;">'
        . '<h1 style="color:#fff;margin:0;font-size:22px;">' . escape($app_name ?? 'SMMP') . '</h1>'
        . '<p style="color:rgba(255,255,255,.85);margin:8px 0 0;font-size:14px;">' . escape($title) . '</p>'
        . '</div>'
        . '<div style="padding:32px;">'
        . '<p style="color:#374151;font-size:15px;line-height:1.6;">' . nl2br(escape($message)) . '</p>'
        . '<a href="' . portalUrl() . 'dashboard" style="display:inline-block;padding:12px 32px;background:#3b82f6;color:#fff;text-decoration:none;border-radius:8px;font-size:15px;font-weight:600;">Go to Dashboard</a>'
        . '<p style="color:#9ca3af;font-size:12px;margin-top:24px;border-top:1px solid #e5e7eb;padding-top:16px;">This is an automated notification from ' . escape($app_name ?? 'SMMP') . '.</p>'
        . '</div>'
        . '</div>';
}
