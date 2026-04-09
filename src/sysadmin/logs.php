<?php
session_start();

require '../includes/init.php';
require 'LogsSAE.php';
if ($_SESSION["role"] !== "sysadmin") {
    header('Location: ../index.php');
    exit();
}

define('LOGS_PER_PAGE', 25);

$logsJson = new LogsSAE();
$logs_location = __DIR__ . '/../../json-logs';
$logs_location_success = __DIR__ . '/../../json-logs/success-logs';
$logs_location_fails = __DIR__ . '/../../json-logs/fails-logs';
$successLogs = $logsJson->loadLogs($logs_location_success);
$failedLogs = $logsJson->loadLogs($logs_location_fails);
// Fusion et tri anti-chronologique (plus récent en premier)
$allLogs = array_merge($successLogs, $failedLogs);
usort($allLogs, fn($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));

// ---------- Normalisation des champs ----------
// Les JSON utilisent "username", "ip", "date", "action", "reason"
// On les mappe vers les noms utilisés dans le template pour ne pas tout réécrire
$allLogs = array_map(function ($entry) {
    return [
        'log_time'         => $entry['date']     ?? '',
        'username'         => $entry['username'] ?? '',
        'ip_address'       => $entry['ip']       ?? '',
        'description'      => $entry['action']   ?? '',
        'reason'           => $entry['reason']   ?? '',
        'duration_seconds' => $entry['duration'] ?? null,
    ];
}, $allLogs);

// Filtre : on retire les entrées sans action (équivalent du WHERE description IS NOT NULL)
$allLogs = array_filter($allLogs, fn($l) => $l['description'] !== '');
$allLogs = array_values($allLogs);

// ---------- Valeurs distinctes pour les menus (avant filtrage) ----------

$distinctUsers   = array_unique(array_column($allLogs, 'username'));
$distinctActions = array_unique(array_column($allLogs, 'description'));
sort($distinctUsers);
sort($distinctActions);

// ---------- Filtres GET ----------

$filter_user   = isset($_GET['filter_user'])   ? trim($_GET['filter_user'])   : '';
$filter_action = isset($_GET['filter_action']) ? trim($_GET['filter_action']) : '';
$search        = isset($_GET['search'])        ? trim($_GET['search'])        : '';

if ($filter_user !== '') {
    $allLogs = array_values(array_filter($allLogs, fn($l) => $l['username'] === $filter_user));
}
if ($filter_action !== '') {
    $allLogs = array_values(array_filter($allLogs, fn($l) => $l['description'] === $filter_action));
}
if ($search !== '') {
    $needle = mb_strtolower($search);
    $allLogs = array_values(array_filter($allLogs, function ($l) use ($needle) {
        return str_contains(mb_strtolower($l['username']),    $needle)
            || str_contains(mb_strtolower($l['ip_address']),  $needle)
            || str_contains(mb_strtolower($l['description']), $needle)
            || str_contains(mb_strtolower($l['reason']),      $needle);
    }));
}

// ---------- Pagination ----------

$page       = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$total      = count($allLogs);
$totalPages = $total > 0 ? (int) ceil($total / LOGS_PER_PAGE) : 1;
$page       = min($page, $totalPages);
$offset     = ($page - 1) * LOGS_PER_PAGE;

$logsPage = array_slice($allLogs, $offset, LOGS_PER_PAGE);

// ---------- Helpers ----------

function formatDuration($seconds) {
    if ($seconds === null || $seconds === '' || (int) $seconds === 0) {
        return '—';
    }
    $s = (int) $seconds;
    if ($s < 60) return $s . ' s';
    return floor($s / 60) . ' min ' . ($s % 60) . ' s';
}

$sidebarBase = '../';
$sidebarSysadminPrefix = '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Journaux d'activité — Sysadmin</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/adminweb.css">
    <link rel="stylesheet" href="../css/sysadmin.css">
</head>
<body class="with-sidebar">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-with-sidebar sysadmin-logs-main">
    <h1>Journaux d'activité</h1>

    <div class="filters-panel">
        <form method="get" action="logs.php" class="filters-form">
            <input type="text"
                   id="search" name="search"
                   placeholder="Rechercher (utilisateur, IP, action, raison…)"
                   value="<?php echo htmlspecialchars($search); ?>">
            <label for="filter_user">Utilisateur
                <select id="filter_user" name="filter_user">
                    <option value="">— Tous</option>
                    <?php foreach ($distinctUsers as $u) : ?>
                        <option value="<?php echo htmlspecialchars($u); ?>"<?php echo $filter_user === $u ? ' selected' : ''; ?>><?php echo htmlspecialchars($u); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label for="filter_action">Action
                <select id="filter_action" name="filter_action">
                    <option value="">— Toutes</option>
                    <?php foreach ($distinctActions as $a) : ?>
                        <option value="<?php echo htmlspecialchars($a); ?>"<?php echo $filter_action === $a ? ' selected' : ''; ?>><?php echo htmlspecialchars($a); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit">Filtrer</button>
        </form>
        <a href="logs.php" class="filters-reset">Réinitialiser</a>
    </div>

    <?php if (count($logsPage) === 0) : ?>
        <p class="logs-empty">Aucune entrée de journal pour les critères sélectionnés.</p>
    <?php else : ?>
        <div class="logs-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date / heure</th>
                        <th>Utilisateur</th>
                        <th>Adresse IP</th>
                        <th>Action</th>
                        <th>Détail</th>
                        <th>Durée</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($logsPage as $row) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['log_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['ip_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['reason']); ?></td>
                        <td class="<?php echo empty($row['duration_seconds']) ? 'log-duration-empty' : ''; ?>"><?php echo htmlspecialchars(formatDuration($row['duration_seconds'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1) : ?>
            <nav class="pagination" aria-label="Pagination des journaux">
                <span class="pagination-info">
                    Page <?php echo $page; ?> / <?php echo $totalPages; ?>
                    — <?php echo $total; ?> entrée<?php echo $total !== 1 ? 's' : ''; ?> au total
                </span>
                <div class="pagination-links">
                    <?php
                    $queryParams = [];
                    if ($filter_user   !== '') $queryParams['filter_user']   = $filter_user;
                    if ($filter_action !== '') $queryParams['filter_action'] = $filter_action;
                    if ($search        !== '') $queryParams['search']        = $search;
                    $queryString = http_build_query($queryParams);
                    $baseUrl = 'logs.php' . ($queryString !== '' ? '?' . $queryString : '');
                    $sep = ($queryString !== '') ? '&' : '?';
                    ?>
                    <?php if ($page <= 1) : ?>
                        <span class="disabled" aria-disabled="true">Précédent</span>
                    <?php else : ?>
                        <a href="<?php echo $baseUrl . $sep . 'page=' . ($page - 1); ?>">Précédent</a>
                    <?php endif; ?>
                    <span class="current"><?php echo $page; ?></span>
                    <?php if ($page >= $totalPages) : ?>
                        <span class="disabled" aria-disabled="true">Suivant</span>
                    <?php else : ?>
                        <a href="<?php echo $baseUrl . $sep . 'page=' . ($page + 1); ?>">Suivant</a>
                    <?php endif; ?>
                </div>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</main>
</body>
</html>