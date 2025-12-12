<?php
session_start();

require '../includes/init.php';
ensureUserAuthorized("tech");

// --- Récupérer tous les systèmes d'exploitation ---
$allOsQuery = "SELECT id, name FROM `os_list` ORDER BY name";
$allOsResult = mysqli_query($loginToDb, $allOsQuery);

$allManufacturerQuery = "SELECT id, name FROM `manufacturer_list` ORDER BY name";
$allManufacturerResult = mysqli_query($loginToDb, $allManufacturerQuery);

// --- Calculs directs ---
$variance = null;
$standardDeviation = null;
$medial = null;

if (isset($_POST['calculate_variance'])) {
    $storageQuery = "SELECT storage FROM control_units";
    $storageResult = mysqli_query($loginToDb, $storageQuery);

    $storageValues = [];
    while ($row = mysqli_fetch_assoc($storageResult)) {
        $storageValues[] = $row['storage'];
    }

    if (count($storageValues) > 0) {
        $mean = array_sum($storageValues) / count($storageValues);
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $storageValues)) / count($storageValues);
    }
}

if (isset($_POST['calculate_standard_deviation'])) {
    $ramQuery = "SELECT ram FROM control_units";
    $ramResult = mysqli_query($loginToDb, $ramQuery);

    $ramValues = [];
    while ($row = mysqli_fetch_assoc($ramResult)) {
        $ramValues[] = $row['ram'];
    }

    if (count($ramValues) > 0) {
        $mean = array_sum($ramValues) / count($ramValues);
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $ramValues)) / count($ramValues);
        $standardDeviation = sqrt($variance);
    }
}

if (isset($_POST['calculate_medial'])) {
    $connectionQuery = "SELECT duration_seconds FROM logs";
    $connectionResult = mysqli_query($loginToDb, $connectionQuery);

    $connectionTimes = [];
    while ($row = mysqli_fetch_assoc($connectionResult)) {
        $connectionTimes[] = $row['connection_time'];
    }

    if (count($connectionTimes) > 0) {
        sort($connectionTimes);
        $count = count($connectionTimes);
        $middle = floor($count / 2);

        if ($count % 2 == 0) {
            $medial = ($connectionTimes[$middle - 1] + $connectionTimes[$middle]) / 2;
        } else {
            $medial = $connectionTimes[$middle];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Tech Panel</title>
    <link rel="stylesheet" href="../css/tech/tech-panel.css">
</head>
<body>
<div class='sidebar'>
    <div class='sidebar-sections'>
        <a class='sidebar-section' href='../logout.php' class='sections'>Se déconnecter</a>
        <a class='sidebar-section' href='create-tech-form.php'>Créer un technicien</a>
        <a class='sidebar-section' href='add-os-form.php'>Ajouter un système d'exploitation</a>
        <a class='sidebar-section' href='add-manufacturer-form.php'>Ajouter un fabriquant</a>
        <a class="sidebar-section" href="stats.php">Statistiques</a>
        <a class="sidebar-section" href="probas.php">Probabilités</a>
        <a class="sidebar-section" href="admin_panel-logs.php">Logs</a>
    </div>
</div>

<div>
    <form method="post" action="actions/stats/percent.php">
        <label for="os">Part des unités de contrôle possédant ce système d'exploitation : </label>
        <select name="os_id" required>
            <?php
            if ($allOsResult) {
                while($row = mysqli_fetch_assoc($allOsResult)) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "' >"
                            . htmlspecialchars($row['name']) . "</option>";
                }
            }
            ?>
        </select>
        <button type="submit">Calculer le pourcentage</button>
    </form>

    <form method="post" action="actions/stats/percent.php">
        <label for="manufacturer_id">Part des moniteurs possédant ce fabricant : </label>
        <select name="manufacturer_id" required>
            <?php
            if ($allManufacturerResult) {
                while($row = mysqli_fetch_assoc($allManufacturerResult)) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "' >"
                            . htmlspecialchars($row['name']) . "</option>";
                }
            }
            ?>
        </select>
        <input type="hidden" name="type" value="manufacturer">
        <button type="submit">Calculer le pourcentage</button>
    </form>

    <form method="post">
        <label>Variance de la taille de stockage entre les unités de contrôle</label>
        <button type="submit" name="calculate_variance">Calculer la variance</button>
    </form>

    <form method="post">
        <label>Ecart type de la ram des unités de contrôle</label>
        <button type="submit" name="calculate_standard_deviation">Calculer l'écart type</button>
    </form>

    <form method="post">
        <label>Médiane du temps de connexion sur la plateforme</label>
        <button type="submit" name="calculate_medial">Calculer la médiane</button>
    </form>

    <hr> <?php
    if ($variance !== null) {
        echo "<p>La variance de la taille de stockage entre les unités de contrôle vaut <span style='font-weight: bold'>".$variance."</span></p>";
    } elseif (isset($_GET["variance"])){
        $varianceResult = htmlspecialchars($_GET["variance"]);
        echo "<p>La variance de la taille de stockage entre les unités de contrôle vaut <span style='font-weight: bold'>".$varianceResult."</span></p>";
    }
    ?>

    <?php
    if(isset($_GET["percent-os"], $_GET['os-name'])){
        $percentResult = htmlspecialchars($_GET["percent-os"]);
        $osName = htmlspecialchars($_GET["os-name"]);

        echo "<p><span style='font-weight: bold'>".$percentResult."%</span> des unités de contrôle sont sous ".$osName."</p>";
    }
    ?>

    <?php
    if(isset($_GET["percent-manufacturer"], $_GET['manufacturer-name'])){
        $percentResult = htmlspecialchars($_GET["percent-manufacturer"]);
        $manufacturerName = htmlspecialchars($_GET["manufacturer-name"]);

        echo "<p><span style='font-weight: bold'>".$percentResult."%</span> des moniteurs sont fabriqués par ".$manufacturerName."</p>";
    }
    ?>

    <?php
    if ($standardDeviation !== null) {
        echo "<p>L'écart-type de la ram des unités de contrôle vaut <span style='font-weight: bold'>".$standardDeviation."</span></p>";
    } elseif (isset($_GET["standard-deviation"])){
        $standardDeviationResult = htmlspecialchars($_GET["standard-deviation"]);
        echo "<p>L'écart-type de la ram des unités de contrôle vaut <span style='font-weight: bold'>".$standardDeviationResult."</span></p>";
    }
    ?>

    <?php
    if ($medial !== null) {
        echo "<p>La médiane du temps de connexion sur la plateforme (en minutes) vaut <span style='font-weight: bold'>".$medial."min</span></p>";
    } elseif (isset($_GET["medial"])){
        $medialResult = htmlspecialchars($_GET["medial"]);
        echo "<p>La médiane du temps de connexion sur la plateforme (en minutes) vaut <span style='font-weight: bold'>".$medialResult."min</span></p>";
    }
    ?>
</div>
</html>