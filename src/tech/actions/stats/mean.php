<?php
session_start();
require '../../includes/init.php';

$type = $_POST['type'] ?? null;

if ($type === "os") {
    $osId = intval($_POST['os_id']);
    $query = "SELECT AVG(rating) AS mean FROM computers WHERE os_id = $osId";
}
elseif ($type === "manufacturer") {
    $manuId = intval($_POST['manufacturer_id']);
    $query = "SELECT AVG(size) AS mean FROM monitors WHERE manufacturer_id = $manuId";
}
else {
    $_SESSION['mean_result'] = "Erreur : type invalide";
    header("Location: ../../stats_mean.php");
    exit;
}

$result = mysqli_query($loginToDb, $query);
$row = mysqli_fetch_assoc($result);

$mean = $row['mean'] ?? null;

if ($mean === null) {
    $_SESSION['mean_result'] = "Aucune donnée trouvée pour cette sélection.";
} else {
    $_SESSION['mean_result'] = "Moyenne : " . round($mean, 2);
}

header("Location: ../../stats_mean.php");
exit;
