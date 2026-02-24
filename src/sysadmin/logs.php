<?php
session_start();

require '../includes/init.php';
if($_SESSION["role"] !== "sysadmin"){
    header('location: ../index.php');
    exit();
}

$select = mysqli_select_db($loginToDb, $db);

$queryLogs = "SELECT * FROM logs WHERE description IS NOT NULL";
$logs = mysqli_query($loginToDb, $queryLogs);

$sidebarBase = '../';
$sidebarSysadminPrefix = '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset='UTF-8'>
    <title>Sysadmin - Logs</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="with-sidebar">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-with-sidebar">
    <table>
        <tr>
            <th>Nom d'utilisateur</th>
            <th>Adresse IP</th>
            <th>Description</th>
            <th>Durée d'activité sur la plateforme</th>
            <th>Date de création du log</th>
        </tr>
    <?php
    while ($row = mysqli_fetch_assoc($logs)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ip_address']) . "</td>";
        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
        echo "<td>" . htmlspecialchars($row['duration_seconds']) . "</td>";
        echo "<td>" . htmlspecialchars($row['log_time']) . "</td>";
        echo "</tr>";
    }
    ?>
    </table>
</main>
</body>
</html>