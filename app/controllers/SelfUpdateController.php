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
        $repoPath = BASE_PATH;
        $output = '';

        try {
            $result = $this->runGit(['rev-parse', '--abbrev-ref', 'HEAD']);
            if ($result['exitCode'] !== 0) {
                throw new Exception('Failed to get current branch: ' . ($result['stderr'] ?: 'unknown error'));
            }
            $currentBranch = $result['stdout'];

            $result = $this->runGit(['log', '-1', '--format=%h | %ci']);
            $oldCommit = $result['exitCode'] === 0 ? $result['stdout'] : 'Unknown';

            $logModel = new DeployLog();
            $logId = $logModel->create([
                'action' => 'self_update',
                'branch' => $currentBranch,
                'previous_commit' => $oldCommit,
                'status' => 'running',
                'user_id' => $_SESSION['user_id'] ?? 0,
            ]);

            $fetch = $this->runGit(['fetch', 'origin']);
            $output .= "> git fetch origin\n" . $fetch['stdout'] . "\n" . $fetch['stderr'] . "\n";

            $pull = $this->runGit(['pull', 'origin', $currentBranch]);
            $output .= "> git pull origin {$currentBranch}\n" . $pull['stdout'] . "\n" . $pull['stderr'] . "\n";

            $result = $this->runGit(['log', '-1', '--format=%h | %ci']);
            $newCommit = $result['exitCode'] === 0 ? $result['stdout'] : 'Unknown';

            $success = $fetch['exitCode'] === 0 && $pull['exitCode'] === 0;

            $logModel->update($logId, [
                'status' => $success ? 'success' : 'failed',
                'output' => $output,
            ]);

            echo json_encode([
                'success' => $success,
                'output' => $output,
                'new_commit' => $newCommit,
                'error' => !$success ? trim($fetch['stderr'] . "\n" . $pull['stderr']) : null,
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'output' => $output,
            ]);
            exit;
        }
    }

    public function fullUpdate()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        header('Content-Type: application/json');
        $repoPath = BASE_PATH;
        $output = '';
        $success = true;

        try {
            $result = $this->runGit(['rev-parse', '--abbrev-ref', 'HEAD']);
            if ($result['exitCode'] !== 0) {
                throw new Exception('Failed to get current branch: ' . ($result['stderr'] ?: 'unknown error'));
            }
            $currentBranch = $result['stdout'];

            $result = $this->runGit(['log', '-1', '--format=%h | %ci']);
            $oldCommit = $result['exitCode'] === 0 ? $result['stdout'] : 'Unknown';

            $logModel = new DeployLog();
            $logId = $logModel->create([
                'action' => 'self_full_update',
                'branch' => $currentBranch,
                'previous_commit' => $oldCommit,
                'status' => 'running',
                'user_id' => $_SESSION['user_id'] ?? 0,
            ]);

            // Step 1: Fetch
            $fetch = $this->runGit(['fetch', 'origin']);
            $output .= "========== Step 1: git fetch ==========\n" . $fetch['stdout'] . "\n" . $fetch['stderr'] . "\n\n";
            $success = $success && $fetch['exitCode'] === 0;

            // Step 2: Pull
            $pull = $this->runGit(['pull', 'origin', $currentBranch]);
            $output .= "========== Step 2: git pull ==========\n" . $pull['stdout'] . "\n" . $pull['stderr'] . "\n\n";
            $success = $success && $pull['exitCode'] === 0;

            // Step 3: Run auto-migrations (they run on every page load via index.php)

            $result = $this->runGit(['log', '-1', '--format=%h | %ci']);
            $newCommit = $result['exitCode'] === 0 ? $result['stdout'] : 'Unknown';

            $logModel->update($logId, [
                'status' => $success ? 'success' : 'failed',
                'output' => $output,
            ]);

            echo json_encode([
                'success' => $success,
                'output' => $output,
                'new_commit' => $newCommit,
                'error' => !$success ? 'One or more steps failed. Check output above.' : null,
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'output' => $output,
            ]);
            exit;
        }
    }
}