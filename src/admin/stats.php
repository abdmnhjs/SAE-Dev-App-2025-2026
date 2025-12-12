<?php
session_start();

require '../includes/init.php';
require '../probas-stats/stats.php';
ensureUserAuthorized("tech");

// --- 1. Récupération des données pour les listes déroulantes ---
$allOsQuery = "SELECT id, name FROM `os_list` ORDER BY name";
$allOsResult = mysqli_query($loginToDb, $allOsQuery);

$allManufacturerQuery = "SELECT id, name FROM `manufacturer_list` ORDER BY name";
$allManufacturerResult = mysqli_query($loginToDb, $allManufacturerQuery);

// --- 2. Calculs statistiques globaux (exécutés au chargement) ---

// Médiane (Durée)
$queryDuration = "SELECT duration_seconds FROM logs";
$resultDuration = mysqli_query($loginToDb, $queryDuration);
$durationValues = [];
while ($row = mysqli_fetch_assoc($resultDuration)) {
    // Conversion en minutes
    $durationValues[] = (int)$row['duration_seconds'] / 60;
}
// Vérification pour éviter erreur si vide
$medialResult = (count($durationValues) > 0) ? medial($durationValues) : 0;

// Écart-type (RAM)
$queryRam = "SELECT ram_mb FROM control_unit";
$resultRam = mysqli_query($loginToDb, $queryRam);
$ramValues = [];
while ($row = mysqli_fetch_assoc($resultRam)) {
    $ramValues[] = (float)$row['ram_mb'];
}
$standardDeviationResult = (count($ramValues) > 0) ? standardDeviation($ramValues) : 0;

// Variance (Disque)
$queryDisk = "SELECT disk_gb FROM control_unit";
$resultDisk = mysqli_query($loginToDb, $queryDisk);
$diskValues = [];
while ($row = mysqli_fetch_assoc($resultDisk)) {
    $diskValues[] = (float)$row['disk_gb'];
}
$varianceResult = (count($diskValues) > 0) ? variance($diskValues) : 0;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset='UTF-8'>
    <title>Tech Panel</title>
    <link rel="stylesheet" href="../css/tech/tech-panel.css">
</head>
<body>

<div class='sidebar'>
    <div class='sidebar-sections'>
        <a class='sidebar-section' href='../logout.php'>Se déconnecter</a>
        <a class='sidebar-section' href='create-tech-form.php'>Créer un technicien</a>
        <a class='sidebar-section' href='add-os-form.php'>Ajouter un système d'exploitation</a>
        <a class='sidebar-section' href='add-manufacturer-form.php'>Ajouter un fabricant</a>
        <a class="sidebar-section" href="stats.php">Statistiques</a>
        <a class="sidebar-section" href="probas.php">Probabilités</a>
        <a class="sidebar-section" href="admin_panel-logs.php">Logs</a>
    </div>
</div>

<div class="main-content"> <form method="post" action="actions/stats/percent.php">
        <label for="os_id">Part des unités de contrôle possédant ce système d'exploitation : </label>
        <select name="os_id" id="os_id" required>
            <?php
            if ($allOsResult) {
                // Rembobiner le pointeur si besoin, ou ré-exécuter si utilisé plusieurs fois,
                // ici c'est la première utilisation donc ok.
                while($row = mysqli_fetch_assoc($allOsResult)) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "'>"
                            . htmlspecialchars($row['name']) . "</option>";
                }
            }
            ?>
        </select>
        <button type="submit">Calculer le pourcentage</button>
    </form>

    <?php if(isset($_GET["percent-os"], $_GET['os-name'])){
        $percentResult = htmlspecialchars($_GET["percent-os"]);
        $osName = htmlspecialchars($_GET["os-name"]);
        echo "<p><span style='font-weight: bold'>".$percentResult."%</span> des unités de contrôle sont sous ".$osName."</p>";
    } ?>

    <form method="post" action="actions/stats/percent.php">
        <label for="manufacturer_id">Part des moniteurs possédant ce fabricant : </label>
        <select name="manufacturer_id" id="manufacturer_id" required>
            <?php
            if ($allManufacturerResult) {
                while($row = mysqli_fetch_assoc($allManufacturerResult)) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "'>"
                            . htmlspecialchars($row['name']) . "</option>";
                }
            }
            ?>
        </select>
        <input type="hidden" name="type" value="manufacturer">
        <button type="submit">Calculer le pourcentage</button>
    </form>

    <?php  if(isset($_GET["percent-manufacturer"], $_GET['manufacturer-name'])){
        $percentResult = htmlspecialchars($_GET["percent-manufacturer"]);
        $manufacturerName = htmlspecialchars($_GET["manufacturer-name"]);
        echo "<p><span style='font-weight: bold'>".$percentResult."%</span> des moniteurs sont fabriqués par ".$manufacturerName."</p>";
    } ?>

    <hr>

    <table>
        <tr>
            <th>Médiane du temps de connexion sur la plateforme</th>
            <td><?php echo round($medialResult, 2); ?> min</td>
        </tr>
        <tr>
            <th>Écart-type de la RAM des unités de contrôle</th>
            <td><?php echo round($standardDeviationResult, 2); ?> Mb</td>
        </tr>
        <tr>
            <th>Variance de la taille de stockage entre les unités de contrôle</th>
            <td><?php echo round($varianceResult, 2); ?> Go</td>
        </tr>
    </table>

</div>
</body>
</html>