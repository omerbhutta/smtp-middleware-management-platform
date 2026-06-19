<?php
// Standalone update worker — runs via CLI php.exe so it doesn't hold web-server file locks
$_ENV = [];
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $_ENV[trim($parts[0])] = trim(trim($parts[1]), '"\'');
        }
    }
}

$dbHost = $_ENV['DB_HOST'] ?? 'localhost';
$dbPort = $_ENV['DB_PORT'] ?? '3306';
$dbName = $_ENV['DB_DATABASE'] ?? '';
$dbUser = $_ENV['DB_USERNAME'] ?? '';
$dbPass = $_ENV['DB_PASSWORD'] ?? '';

$action = $argv[1] ?? 'pull';
$userId = (int)($argv[2] ?? 0);

function runGit(array $args) {
    $descriptorspec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    // Find git full path
    $git = 'git';
    $candidates = ['git', 'git.exe'];
    $paths = explode(PATH_SEPARATOR, getenv('PATH') ?: '');
    $paths[] = 'C:\Program Files\Git\bin';
    $paths[] = 'C:\Program Files\Git\cmd';
    $paths[] = 'C:\Program Files (x86)\Git\bin';
    $paths[] = 'C:\Program Files (x86)\Git\cmd';
    foreach ($paths as $p) {
        $p = trim($p);
        if ($p === '') continue;
        foreach ($candidates as $bin) {
            $full = $p . DIRECTORY_SEPARATOR . $bin;
            if (is_file($full) && is_executable($full)) {
                $git = $full;
                break 2;
            }
        }
    }
    $cmd = array_merge([$git], $args);
    $process = @proc_open($cmd, $descriptorspec, $pipes, __DIR__);
    if (!is_resource($process)) {
        return ['stdout' => '', 'stderr' => 'Failed to execute git.', 'exitCode' => -1];
    }
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]); fclose($pipes[2]);
    $exitCode = proc_close($process);
    return ['stdout' => trim($stdout), 'stderr' => trim($stderr), 'exitCode' => $exitCode];
}

function getBranch() {
    $r = runGit(['rev-parse', '--abbrev-ref', 'HEAD']);
    return $r['exitCode'] === 0 ? $r['stdout'] : 'main';
}

function getCommit() {
    $r = runGit(['log', '-1', '--format=%h | %ci']);
    return $r['exitCode'] === 0 ? $r['stdout'] : 'Unknown';
}

// Connect to DB to log the result
$pdo = null;
try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    // Can't log to DB, but we can still run git
}

$currentBranch = getBranch();
$oldCommit = getCommit();
$output = '';
$success = false;

if ($action === 'full') {
    // Step 1: Fetch
    $fetch = runGit(['fetch', 'origin']);
    $output .= "========== Step 1: git fetch ==========\n" . $fetch['stdout'] . "\n" . $fetch['stderr'] . "\n\n";
    $fetchOk = $fetch['exitCode'] === 0;

    // Step 2: Reset hard to remote (discards any local changes on prod)
    $reset = runGit(['reset', '--hard', "origin/{$currentBranch}"]);
    $output .= "========== Step 2: git reset --hard origin/{$currentBranch} ==========\n" . $reset['stdout'] . "\n" . $reset['stderr'] . "\n\n";
    $resetOk = $reset['exitCode'] === 0;

    $success = $fetchOk && $resetOk;
} else {
    // Simple pull
    $fetch = runGit(['fetch', 'origin']);
    $output .= "> git fetch origin\n" . $fetch['stdout'] . "\n" . $fetch['stderr'] . "\n";
    $reset = runGit(['reset', '--hard', "origin/{$currentBranch}"]);
    $output .= "> git reset --hard origin/{$currentBranch}\n" . $reset['stdout'] . "\n" . $reset['stderr'] . "\n";
    $success = $fetch['exitCode'] === 0 && $reset['exitCode'] === 0;
}

$newCommit = getCommit();

// Log to DB
if ($pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO deploy_logs (user_id, action, branch, previous_commit, output, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $userId,
            $action === 'full' ? 'self_full_update' : 'self_update',
            $currentBranch,
            $oldCommit,
            $output,
            $success ? 'success' : 'failed',
        ]);
    } catch (Exception $e) {}
}

echo json_encode([
    'success' => $success,
    'output' => $output,
    'new_commit' => $newCommit,
    'error' => $success ? null : 'One or more steps failed. Check output above.',
]);
