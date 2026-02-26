<?php
session_start();

require '../includes/init.php';
if ($_SESSION["role"] !== "sysadmin") {
    header('Location: ../index.php');
    exit();
}

mysqli_select_db($loginToDb, $db);

define('LOGS_PER_PAGE', 25);

$filter_user = isset($_GET['filter_user']) ? trim($_GET['filter_user']) : '';
$filter_ip   = isset($_GET['filter_ip']) ? trim($_GET['filter_ip']) : '';
$page        = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset      = ($page - 1) * LOGS_PER_PAGE;

// Construction de la requête avec filtres
$where = ["description IS NOT NULL"];
$params = [];
$types = '';

if ($filter_user !== '') {
    $where[] = "username = ?";
    $params[] = $filter_user;
    $types .= 's';
}
if ($filter_ip !== '') {
    $where[] = "ip_address = ?";
    $params[] = $filter_ip;
    $types .= 's';
}

$whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

// Total pour la pagination
$countSql = "SELECT COUNT(*) AS total FROM logs" . $whereSql;
if (count($params) > 0) {
    $stmtCount = mysqli_prepare($loginToDb, $countSql);
    mysqli_stmt_bind_param($stmtCount, $types, ...$params);
    mysqli_stmt_execute($stmtCount);
    $total = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCount))['total'];
    mysqli_stmt_close($stmtCount);
} else {
    $resCount = mysqli_query($loginToDb, $countSql);
    $total = (int) mysqli_fetch_assoc($resCount)['total'];
}

$totalPages = $total > 0 ? (int) ceil($total / LOGS_PER_PAGE) : 1;
$page = min(max(1, $page), $totalPages);
$offset = ($page - 1) * LOGS_PER_PAGE;

// Liste des logs avec pagination
$sql = "SELECT * FROM logs" . $whereSql . " ORDER BY log_time DESC LIMIT " . (int) LOGS_PER_PAGE . " OFFSET " . (int) $offset;
if (count($params) > 0) {
    $stmt = mysqli_prepare($loginToDb, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $logs = mysqli_stmt_get_result($stmt);
} else {
    $logs = mysqli_query($loginToDb, $sql);
}

// Valeurs distinctes pour les filtres
$usersResult = mysqli_query($loginToDb, "SELECT DISTINCT username FROM logs WHERE description IS NOT NULL ORDER BY username");
$ipsResult   = mysqli_query($loginToDb, "SELECT DISTINCT ip_address FROM logs WHERE description IS NOT NULL ORDER BY ip_address");

function formatDuration($seconds) {
    if ($seconds === null || $seconds === '' || (int) $seconds === 0) {
        return '—';
    }
    $s = (int) $seconds;
    if ($s < 60) {
        return $s . ' s';
    }
    $min = floor($s / 60);
    $sec = $s % 60;
    return $min . ' min ' . $sec . ' s';
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
            <label for="filter_user">Utilisateur
                <select id="filter_user" name="filter_user">
                    <option value="">— Tous</option>
                    <?php while ($u = mysqli_fetch_assoc($usersResult)) : ?>
                        <option value="<?php echo htmlspecialchars($u['username']); ?>"<?php echo $filter_user === $u['username'] ? ' selected' : ''; ?>><?php echo htmlspecialchars($u['username']); ?></option>
                    <?php endwhile; ?>
                </select>
            </label>
            <label for="filter_ip">Adresse IP
                <select id="filter_ip" name="filter_ip">
                    <option value="">— Toutes</option>
                    <?php while ($ip = mysqli_fetch_assoc($ipsResult)) : ?>
                        <option value="<?php echo htmlspecialchars($ip['ip_address']); ?>"<?php echo $filter_ip === $ip['ip_address'] ? ' selected' : ''; ?>><?php echo htmlspecialchars($ip['ip_address']); ?></option>
                    <?php endwhile; ?>
                </select>
            </label>
            <button type="submit">Filtrer</button>
        </form>
        <a href="logs.php" class="filters-reset">Réinitialiser</a>
    </div>

    <?php if (mysqli_num_rows($logs) === 0) : ?>
        <p class="logs-empty">Aucune entrée de journal pour les critères sélectionnés.</p>
    <?php else : ?>
        <div class="logs-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date / heure</th>
                        <th>Utilisateur</th>
                        <th>Adresse IP</th>
                        <th>Description</th>
                        <th>Durée</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($logs)) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['log_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['ip_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td class="<?php echo ($row['duration_seconds'] === null || $row['duration_seconds'] === '') ? 'log-duration-empty' : ''; ?>"><?php echo htmlspecialchars(formatDuration($row['duration_seconds'])); ?></td>
                    </tr>
                <?php endwhile; ?>
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
                    if ($filter_user !== '') $queryParams['filter_user'] = $filter_user;
                    if ($filter_ip !== '') $queryParams['filter_ip'] = $filter_ip;
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
