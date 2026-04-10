<?php
session_start();

require '../includes/init.php';
require 'LogsSAE.php';
if ($_SESSION["role"] !== "sysadmin") {
    header('Location: ../index.php');
    exit();
}


$output = [];
$return_var = 0;
$sidebarBase = '../';
$sidebarSysadminPrefix = '';
$from = max(1, (int)($_GET['from'] ?? 1));
$to   = max($from + 1, (int)($_GET['to'] ?? 100));
$limit = 10000 * max(1, $to / 10);
// Fetch a large batch — don't limit to $to, let grep filter first
exec("journalctl -u ssh -n $limit --no-pager 2>&1 | grep -E 'Accepted|Failed|session opened'", $raw, $return_var);

// Reverse so line 1 = newest
$raw = array_reverse($raw);

// Now slice correctly
$output = array_slice($raw, $from - 1, $to - $from + 1);
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
        <form method="GET" class="filters-form">
            <label>De la ligne : <input style="color: black;" type="number" name="from" min="1" value="<?php echo $from; ?>"></label>
            <label>À la ligne : <input style="color: black;" type="number" name="to" min="2" value="<?php echo $to; ?>"></label>
            <button type="submit">Afficher</button>
        </form>
    </div>
    <h2>Derniers événements SSH</h2>
    <div class="logs-table-wrap">
        <table>
            <thead>
            <tr>
                <th>Logs</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($output as $line): ?>
                <tr>
                    <td><?php echo htmlspecialchars($line); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <p><strong>Code retour:</strong> <?php echo $return_var; ?></p>

</main>
</body>
</html>