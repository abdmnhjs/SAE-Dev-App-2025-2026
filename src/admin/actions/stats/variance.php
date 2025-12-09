<?php
session_start();
require '../../../includes/init.php';
require '../../../probas-stats/stats.php';


$query = "SELECT disk_gb FROM control_unit";
$result = mysqli_query($loginToDb, $query);

$diskValues = [];
while ($row = mysqli_fetch_assoc($result)) {
    $diskValues[] = (float)$row['disk_gb'];
}

$varianceResult = variance($diskValues);

if ($varianceResult !== false && $varianceResult !== null) {
    header("Location: ../../stats.php?variance=" . $varianceResult);
    exit();
}