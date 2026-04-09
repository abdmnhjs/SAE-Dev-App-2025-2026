<?php
require __DIR__ . '/../includes/LogEntry.php';
class LogsSAE
{
    private string $logDir;
    private string $successLogDir;
    private string $failsLogDir;

    public function __construct()
    {
        $this->logDir        = __DIR__ . '/../../json-logs';
        $this->successLogDir = __DIR__ . '/../../json-logs/success-logs';
        $this->failsLogDir   = __DIR__ . '/../../json-logs/fails-logs';

    }

    public function createUserLog(string $action = "Unknown"): LogEntry
    {
        $username = "Unknown";
        if (session_status() === PHP_SESSION_ACTIVE) {
            $username = $_SESSION['username'];
        }
        return new LogEntry($username, $action);
    }

    public function successLogin(int $attempts = 0): void
    {
        $user = $this->createUserLog("success_login");

        $logEntry = [
            "username"          => $user->getUsername(),
            "ip"                => $user->getIp(),
            "date"              => $user->getDate(),
            "action"            => $user->getAction(),
            "attempts_before"   => $attempts,
        ];

        $this->writeLog($this->successLogDir, $logEntry);
    }

    public function failedLogin(string $reason = "Identifiants invalides", string $username = null): void
    {
        $user = $this->createUserLog("failed_login");

        $logEntry = [
            "username"  => $username,
            "ip"        => $user->getIp(),
            "date"      => $user->getDate(),
            "action"    => $user->getAction(),
            "reason"    => $reason,
        ];

        $this->writeLog($this->failsLogDir, $logEntry);
    }

    /**
     * Ecris en json
     */
    private function writeLog(string $dir, array $logEntry): void
    {
        // Un fichier par jour
        $filename = $dir . '/' . date('Y-m-d') . '.json';

        $data = [];
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            $data    = json_decode($content, true) ?? [];
        }

        $data[] = $logEntry;
        echo json_encode($logEntry, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    public function loadLogs(string $dir): array {
    $entries = [];

    $files = glob($dir . '/*.json');
    if ($files === false) return $entries;

    foreach ($files as $file) {
        $content = file_get_contents($file);
        if ($content === false) continue;

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) continue;

        $entries = array_merge($entries, $decoded);
    }

    // Tri par date croissante
    usort($entries, fn($a, $b) => strtotime($a['date']) <=> strtotime($b['date']));

    return $entries;
}
}