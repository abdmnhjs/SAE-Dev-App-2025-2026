<?php
session_start();
require '../../../includes/init.php';
require '../../../probas-stats/stats.php';


$query = "SELECT duration_seconds FROM logs";
$result = mysqli_query($loginToDb, $query);

$durationValues = [];
while ($row = mysqli_fetch_assoc($result)) {
    $durationValues[] = (int)$row['duration_seconds'] / 60;
}

$medialResult = medial($durationValues);

if ($medialResult !== false && $medialResult !== null) {
    header("Location: ../../stats.php?medial=" . $medialResult);
    exit();
}