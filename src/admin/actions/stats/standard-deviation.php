<?php
session_start();
require '../../../includes/init.php';
require '../../../probas-stats/stats.php';


$query = "SELECT ram_mb FROM control_unit";
$result = mysqli_query($loginToDb, $query);

$ramValues = [];
while ($row = mysqli_fetch_assoc($result)) {
    $ramValues[] = (float)$row['ram_mb'];
}

$standardDeviationResult = standardDeviation($ramValues);

if ($standardDeviationResult !== false && $standardDeviationResult !== null) {
    header("Location: ../../stats.php?standard-deviation=" . $standardDeviationResult);
    exit();
}