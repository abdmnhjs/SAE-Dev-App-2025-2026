<?php
require __DIR__ . '/../includes/LogEntry.php';

class LogsSAE
{
    private string $logDir;
    private string $successLogDir;
    private string $failsLogDir;

    public function __construct()
    {
        $this->logDir = __DIR__ . '/../../json-logs';
        $this->successLogDir = __DIR__ . '/../../json-logs/success-logs';
        $this->failsLogDir = __DIR__ . '/../../json-logs/fails-logs';

    }

    public function successLogin(int $attempts = 0): void
    {
        $user = $this->createUserLog("success_login");

        $logEntry = [
            "username" => $user->getUsername(),
            "ip" => $user->getIp(),
            "date" => $user->getDate(),
            "action" => $user->getAction(),
            "attempts_before" => $attempts,
        ];

        $this->writeLog($this->successLogDir, $logEntry);
    }

    public function createUserLog(string $action = "Unknown"): LogEntry
    {
        $username = "Unknown";
        if (session_status() === PHP_SESSION_ACTIVE) {
            $username = $_SESSION['username'];
        }
        return new LogEntry($username, $action);
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
            $data = json_decode($content, true) ?? [];
        }

        $data[] = $logEntry;
        echo json_encode($logEntry, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function failedLogin(string $reason = "Identifiants invalides", string $username = null): void
    {
        $user = $this->createUserLog("failed_login");

        $logEntry = [
            "username" => $username,
            "ip" => $user->getIp(),
            "date" => $user->getDate(),
            "action" => $user->getAction(),
            "reason" => $reason,
        ];

        $this->writeLog($this->failsLogDir, $logEntry);
    }

    public function loadLogs(string $dir): array
    {
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

    function supprimerFichierSecurise(string $fichier_recu, string $dossier_autorise): array
    {
        // 1. Résoudre le chemin RÉEL (élimine les ../ et symlinks)
        $chemin_reel = realpath($fichier_recu);
        $dossier_reel = realpath($dossier_autorise);

        // 2. Le fichier existe-t-il vraiment ?
        if ($chemin_reel === false) {
            return ['ok' => false, 'erreur' => 'Fichier introuvable.'];
        }

        // 3. Est-il bien DANS le dossier autorisé ? (pas ailleurs sur le serveur)
        if (!str_starts_with($chemin_reel, $dossier_reel . DIRECTORY_SEPARATOR)) {
            return ['ok' => false, 'erreur' => 'Accès refusé : hors du dossier autorisé.'];
        }

        // 4. Est-ce bien un fichier (pas un dossier) ?
        if (!is_file($chemin_reel)) {
            return ['ok' => false, 'erreur' => 'La cible n\'est pas un fichier.'];
        }

        // 5. Seulement là on supprime
        if (!unlink($chemin_reel)) {
            return ['ok' => false, 'erreur' => 'Échec de la suppression (permissions ?).'];
        }

        return ['ok' => true, 'message' => 'Fichier supprimé : ' . basename($chemin_reel)];
    }
public function allLogsFrom(string $dossier): array {
    $fichiers = scandir($dossier);
    if ($fichiers === false) return [];

    $result = [];
    foreach ($fichiers as $fichier) {
        // Ignorer . et .. et les dossiers
        if ($fichier === '.' || $fichier === '..') continue;
        $chemin_complet = $dossier . DIRECTORY_SEPARATOR . $fichier;
        if (!is_file($chemin_complet)) continue;

        $result[] = [
            'log_name'     => $fichier,
            'chemin_complet' => $chemin_complet,
        ];
    }
    return $result;
}
}