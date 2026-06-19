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

        $logModel = new DeployLog();
        $logId = $logModel->create([
            'action' => 'self_update',
            'branch' => '',
            'previous_commit' => '',
            'status' => 'running',
            'user_id' => $_SESSION['user_id'] ?? 0,
        ]);
        $logModel->update($logId, ['status' => 'running']);

        $phpBin = PHP_BINARY ?: 'php';
        $worker = escapeshellarg(BASE_PATH . 'update_worker.php');
        $action = 'pull';
        $uid = (int)($_SESSION['user_id'] ?? 0);
        $cmd = "{$phpBin} {$worker} {$action} {$uid}";
        $result = shell_exec($cmd);

        if (!$result) {
            $logModel->update($logId, ['status' => 'failed', 'output' => 'Worker process returned no output.']);
            echo json_encode(['success' => false, 'error' => 'Update worker failed to start.', 'output' => '']);
            exit;
        }

        $data = json_decode($result, true);
        if (!$data) {
            $logModel->update($logId, ['status' => 'failed', 'output' => $result]);
            echo json_encode(['success' => false, 'error' => 'Invalid response from worker.', 'output' => $result]);
            exit;
        }

        $logModel->update($logId, [
            'status' => $data['success'] ? 'success' : 'failed',
            'output' => $data['output'] ?? '',
            'branch' => $data['new_commit'] ?? '',
        ]);

        echo json_encode($data);
        exit;
    }

    public function fullUpdate()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        header('Content-Type: application/json');

        $logModel = new DeployLog();
        $logId = $logModel->create([
            'action' => 'self_full_update',
            'branch' => '',
            'previous_commit' => '',
            'status' => 'running',
            'user_id' => $_SESSION['user_id'] ?? 0,
        ]);
        $logModel->update($logId, ['status' => 'running']);

        $phpBin = PHP_BINARY ?: 'php';
        $worker = escapeshellarg(BASE_PATH . 'update_worker.php');
        $action = 'full';
        $uid = (int)($_SESSION['user_id'] ?? 0);
        $cmd = "{$phpBin} {$worker} {$action} {$uid}";
        $result = shell_exec($cmd);

        if (!$result) {
            $logModel->update($logId, ['status' => 'failed', 'output' => 'Worker process returned no output.']);
            echo json_encode(['success' => false, 'error' => 'Update worker failed to start.', 'output' => '']);
            exit;
        }

        $data = json_decode($result, true);
        if (!$data) {
            $logModel->update($logId, ['status' => 'failed', 'output' => $result]);
            echo json_encode(['success' => false, 'error' => 'Invalid response from worker.', 'output' => $result]);
            exit;
        }

        $logModel->update($logId, [
            'status' => $data['success'] ? 'success' : 'failed',
            'output' => $data['output'] ?? '',
            'branch' => $data['new_commit'] ?? '',
        ]);

        echo json_encode($data);
        exit;
    }
}
}