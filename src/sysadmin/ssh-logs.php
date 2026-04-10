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
exec('journalctl -u ssh -n 100 --no-pager 2>&1 | grep -E "Accepted|Failed|session opened"', $output, $return_var);

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

    <section class="logs-table-wrap">
        <h2>Derniers événements SSH</h2>
        <table>
            <tr>
                Logs
            </tr>
            <?php foreach ($output as $line): ?>
            <tr>
                <td><?php echo htmlspecialchars($line); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
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
    </section>
</main>
</body>
</html>