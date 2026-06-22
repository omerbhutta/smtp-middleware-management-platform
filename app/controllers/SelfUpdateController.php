<?php
class SelfUpdateController
{
    private $gitPath;

    public function __construct()
    {
        $this->gitPath = $this->findGit();
    }

    private function findGit()
    {
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
                    return $full;
                }
            }
        }

        // Fallback — let proc_open try PATH (works on Linux)
        if (PHP_OS_FAMILY !== 'Windows') {
            return 'git';
        }

        return null;
    }

    private function runGit(array $args)
    {
        if (!$this->gitPath) {
            return [
                'stdout' => '',
                'stderr' => 'Git executable not found. Install Git and ensure it is in the system PATH, then restart the web server.',
                'exitCode' => -1,
            ];
        }

        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $cmd = array_merge([$this->gitPath], $args);
        $process = @proc_open($cmd, $descriptorspec, $pipes, BASE_PATH);

        if (!is_resource($process)) {
            return [
                'stdout' => '',
                'stderr' => 'Failed to execute git command. Git not found at: ' . ($this->gitPath ?? 'not found'),
                'exitCode' => -1,
            ];
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        return [
            'stdout' => trim($stdout),
            'stderr' => trim($stderr),
            'exitCode' => $exitCode,
        ];
    }

    public function index()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        $repoPath = BASE_PATH;
        $currentBranch = 'Unknown';
        $currentCommit = 'Unknown';
        $gitStatus = '';

        try {
            $result = $this->runGit(['rev-parse', '--abbrev-ref', 'HEAD']);
            if ($result['exitCode'] === 0) {
                $currentBranch = $result['stdout'] ?: 'Unknown';
            }

            $result = $this->runGit(['log', '-1', '--format=%h | %ci | %s']);
            if ($result['exitCode'] === 0) {
                $currentCommit = $result['stdout'] ?: 'Unknown';
            }

            $result = $this->runGit(['status', '--short']);
            if ($result['exitCode'] === 0) {
                $gitStatus = $result['stdout'];
            }
        } catch (Exception $e) {
        }

        $logModel = new DeployLog();
        $logs = $logModel->getRecent(20);

        $title = 'Self Update';
        $active_menu = 'self_update';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'self_update/index.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function pull()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        header('Content-Type: application/json');

        try {
            $data = $this->runWorker('pull');
        } catch (Exception $e) {
            $data = null;
        }

        if (!$data || !isset($data['success'])) {
            $data = $this->runDirect('self_update');
        }

        $logModel = new DeployLog();
        $logModel->create([
            'action' => 'self_update',
            'branch' => $data['new_commit'] ?? '',
            'previous_commit' => '',
            'status' => $data['success'] ? 'success' : 'failed',
            'user_id' => $_SESSION['user_id'] ?? 0,
        ]);

        echo json_encode($data);
        exit;
    }

    public function fullUpdate()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        header('Content-Type: application/json');

        try {
            $data = $this->runWorker('full');
        } catch (Exception $e) {
            $data = null;
        }

        if (!$data || !isset($data['success'])) {
            $data = $this->runDirect('self_full_update');
        }

        $logModel = new DeployLog();
        $logModel->create([
            'action' => 'self_full_update',
            'branch' => $data['new_commit'] ?? '',
            'previous_commit' => '',
            'status' => $data['success'] ? 'success' : 'failed',
            'user_id' => $_SESSION['user_id'] ?? 0,
        ]);

        echo json_encode($data);
        exit;
    }

    private function runWorker(string $action): ?array
    {
        $phpBin = PHP_BINARY;
        if (!$phpBin || !is_executable($phpBin)) {
            $phpBin = 'php';
        }
        $worker = BASE_PATH . 'update_worker.php';
        if (!file_exists($worker)) {
            return null;
        }
        $uid = (int)($_SESSION['user_id'] ?? 0);

        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $cmd = [$phpBin, $worker, $action, (string)$uid];
        $process = @proc_open($cmd, $descriptorspec, $pipes, BASE_PATH);

        if (!is_resource($process)) {
            return null;
        }
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]); fclose($pipes[2]);
        proc_close($process);

        if (!$stdout) {
            return null;
        }
        $data = json_decode($stdout, true);
        return is_array($data) ? $data : null;
    }

    private function runDirect(string $action): array
    {
        $currentBranch = 'main';
        $r = $this->runGit(['rev-parse', '--abbrev-ref', 'HEAD']);
        if ($r['exitCode'] === 0) {
            $currentBranch = $r['stdout'] ?: 'main';
        }

        $output = '';

        $fetch = $this->runGit(['fetch', 'origin']);
        $output .= "> git fetch origin\n" . $fetch['stdout'] . "\n" . $fetch['stderr'] . "\n";
        $fetchOk = $fetch['exitCode'] === 0;

        $reset = $this->runGit(['reset', '--hard', "origin/{$currentBranch}"]);
        $output .= "> git reset --hard origin/{$currentBranch}\n" . $reset['stdout'] . "\n" . $reset['stderr'] . "\n";
        $resetOk = $reset['exitCode'] === 0;

        $clean = $this->runGit(['clean', '-fd']);
        $output .= "> git clean -fd\n" . $clean['stdout'] . "\n" . $clean['stderr'] . "\n";
        $cleanOk = $clean['exitCode'] === 0;

        $r = $this->runGit(['log', '-1', '--format=%h | %ci']);
        $newCommit = $r['exitCode'] === 0 ? $r['stdout'] : 'Unknown';

        $success = $fetchOk && $resetOk && $cleanOk;

        return [
            'success' => $success,
            'output' => $output,
            'new_commit' => $newCommit,
            'error' => $success ? null : 'One or more steps failed. Check output above.',
        ];
    }
}