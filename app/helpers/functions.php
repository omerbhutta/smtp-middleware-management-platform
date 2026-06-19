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
                'SMMP | ' . $subject,
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

/**
 * Parse email log recipients + error_message into an array with 'email' and 'status' (sent|forbidden|suppressed)
 */
function parseRecipientsWithStatus($recipientsStr, $errorMessage)
{
    $delivered = [];
    if ($recipientsStr) {
        foreach (array_map('trim', explode(',', $recipientsStr)) as $r) {
            $delivered[strtolower($r)] = $r;
        }
    }

    $skipped = [];
    if ($errorMessage && preg_match('/Skipped:\s*(.+?)(?:$|\|)/i', $errorMessage, $m)) {
        foreach (explode(',', $m[1]) as $part) {
            $part = trim($part);
            if (preg_match('/^(.+?)\s*\((.+?)\)$/', $part, $pm)) {
                $email = trim($pm[1]);
                $reason = trim($pm[2]);
                $skipped[strtolower($email)] = ['email' => $email, 'reason' => $reason];
            }
        }
    }

    $result = [];
    foreach ($delivered as $lower => $email) {
        if (isset($skipped[$lower])) {
            $result[] = ['email' => $email, 'status' => $skipped[$lower]['reason']];
        } else {
            $result[] = ['email' => $email, 'status' => 'sent'];
        }
    }
    foreach ($skipped as $lower => $info) {
        if (!isset($delivered[$lower])) {
            $result[] = ['email' => $info['email'], 'status' => $info['reason']];
        }
    }
    return $result;
}

function renderRecipientsHtml($recipientsStr, $errorMessage)
{
    $items = parseRecipientsWithStatus($recipientsStr, $errorMessage);
    $html = '';
    foreach ($items as $item) {
        $isSkipped = $item['status'] !== 'sent';
        $color = $isSkipped ? 'var(--red)' : 'var(--text-primary)';
        $badge = $isSkipped ? ' <span class="badge-smm badge-smm-danger" style="font-size:0.6rem;">' . escape($item['status']) . '</span>' : '';
        $style = $isSkipped ? 'text-decoration:line-through;' : '';
        $html .= '<div style="color:' . $color . ';' . $style . 'font-size:0.82rem;white-space:nowrap;">'
               . '<i class="fas fa-' . ($isSkipped ? 'times-circle' : 'check-circle') . '" style="margin-right:4px;font-size:0.65rem;"></i>'
               . escape($item['email']) . $badge . '</div>';
    }
    return $html;
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
        . '<hr style="border:none;border-top:1px solid #e5e7eb;margin:16px 0;">'
        . '<p style="color:#9ca3af;font-size:12px;">This is an automated notification from SMTP Management Platform (SMMP).</p>'
        . '</div>'
        . '</div>';
}

function saveEnvSetting($key, $value)
{
    $envFile = BASE_PATH . '.env';
    if (!file_exists($envFile)) {
        return false;
    }
    $content = file_get_contents($envFile);
    $lines = explode("\n", $content);
    $found = false;
    foreach ($lines as $i => $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) continue;
        $parts = explode('=', $trimmed, 2);
        if (trim($parts[0]) === $key) {
            $lines[$i] = $key . '=' . $value;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $lines[] = $key . '=' . $value;
    }
    file_put_contents($envFile, implode("\n", $lines));
    $_ENV[$key] = $value;
    return true;
}

function sortUrl($column, $sort, $order)
{
    $params = $_GET;
    $params['sort'] = $column;
    $params['order'] = ($sort === $column && $order === 'asc') ? 'desc' : 'asc';
    return '?' . http_build_query($params);
}

function sortIcon($column, $sort, $order)
{
    if ($sort !== $column) {
        return '<i class="fas fa-sort" style="opacity:0.3;font-size:0.7rem;margin-left:4px;"></i>';
    }
    $icon = $order === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
    return '<i class="fas ' . $icon . '" style="font-size:0.7rem;margin-left:4px;"></i>';
}

function buildSortSql($sort, $order, $allowedColumns, $defaultSort = 'created_at', $defaultOrder = 'DESC')
{
    $sort = in_array($sort, $allowedColumns) ? $sort : $defaultSort;
    $order = strtoupper($order) === 'ASC' ? 'ASC' : ($order === 'DESC' ? 'DESC' : $defaultOrder);
    return " ORDER BY {$sort} {$order}";
}
