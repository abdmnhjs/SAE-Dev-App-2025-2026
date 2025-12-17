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
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Tech Panel</title>
</head>
<div class='sidebar'>
    <div class='sidebar-sections'>
        <a class='sidebar-section' href='../logout.php' class='sections'>Se déconnecter</a>
    </div>
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
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['ip_address'] . "</td>";
        echo "<td>" . $row['description'] . "</td>";
        echo "<td>" . $row['duration_seconds'] . "</td>";
        echo "<td>" . $row['log_time'] . "</td>";
        echo "</tr>";
    }?>
    </table>

</div>
</html>