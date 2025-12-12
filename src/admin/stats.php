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

// A. Médiane (Durée)
$queryDuration = "SELECT duration_seconds FROM logs";
$resultDuration = mysqli_query($loginToDb, $queryDuration);
$durationValues = [];
while ($row = mysqli_fetch_assoc($resultDuration)) {
    // Conversion en minutes
    $durationValues[] = (int)$row['duration_seconds'] / 60;
}
$medialResult = (count($durationValues) > 0) ? medial($durationValues) : 0;

// B. Écart-type (RAM)
$queryRam = "SELECT ram_mb FROM control_unit";
$resultRam = mysqli_query($loginToDb, $queryRam);
$ramValues = [];
while ($row = mysqli_fetch_assoc($resultRam)) {
    $ramValues[] = (float)$row['ram_mb'];
}
$standardDeviationResult = (count($ramValues) > 0) ? standardDeviation($ramValues) : 0;

// C. Variance (Disque)
$queryDisk = "SELECT disk_gb FROM control_unit";
$resultDisk = mysqli_query($loginToDb, $queryDisk);
$diskValues = [];
while ($row = mysqli_fetch_assoc($resultDisk)) {
    $diskValues[] = (float)$row['disk_gb'];
}
$varianceResult = (count($diskValues) > 0) ? variance($diskValues) : 0;

// --- NOUVEAUX CALCULS AJOUTÉS ---

// D. Taux de machines hors garantie (Vétusté)
// On compte le total et celles dont la date de garantie est dépassée, uniquement sur le matériel actif
$queryWarranty = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN warranty_end < CURDATE() THEN 1 ELSE 0 END) as expired
                  FROM control_unit 
                  WHERE is_active = 1";
$resultWarranty = mysqli_query($loginToDb, $queryWarranty);
$rowWarranty = mysqli_fetch_assoc($resultWarranty);

$percentExpired = 0;
if ($rowWarranty && $rowWarranty['total'] > 0) {
    $percentExpired = ($rowWarranty['expired'] / $rowWarranty['total']) * 100;
}

// E. Taille moyenne des écrans (Moniteurs actifs)
$queryScreenSize = "SELECT AVG(size_inch) as avg_size FROM screen WHERE is_active = 1";
$resultScreenSize = mysqli_query($loginToDb, $queryScreenSize);
$rowScreenSize = mysqli_fetch_assoc($resultScreenSize);
$avgScreenSize = ($rowScreenSize && $rowScreenSize['avg_size']) ? $rowScreenSize['avg_size'] : 0;

// F. Résolution la plus fréquente (Mode statistique)
$queryRes = "SELECT resolution FROM screen WHERE is_active = 1";
$resultRes = mysqli_query($loginToDb, $queryRes);
$resolutions = [];
while ($row = mysqli_fetch_assoc($resultRes)) {
    $resolutions[] = $row['resolution'];
}

$mostCommonResolution = "Aucune donnée";
if (count($resolutions) > 0) {
    // Compte les occurrences de chaque résolution
    $valuesCount = array_count_values($resolutions);
    // Trie pour avoir la plus fréquente en premier
    arsort($valuesCount);
    // Récupère la clé (la résolution) du premier élément
    $mostCommonResolution = array_key_first($valuesCount);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset='UTF-8'>
    <title>Tech Panel</title>
    <link rel="stylesheet" href="../css/tech/tech-panel.css">
    <style>
        /* Petit ajout CSS pour le tableau si nécessaire */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
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

<div class="main-content">

    <form method="post" action="actions/stats/percent.php">
        <label for="os_id">Part des unités de contrôle possédant ce système d'exploitation : </label>
        <select name="os_id" id="os_id" required>
            <?php
            if ($allOsResult) {
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
                // Remise à zéro du pointeur de résultat pour réutiliser la requête si besoin
                mysqli_data_seek($allManufacturerResult, 0);
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

    <h3>Tableau de bord statistique</h3>

    <table>
        <tr>
            <th>Indicateur</th>
            <th>Résultat</th>
        </tr>

        <tr>
            <td>Médiane du temps de connexion sur la plateforme</td>
            <td><?php echo round($medialResult, 2); ?> min</td>
        </tr>
        <tr>
            <td>Écart-type de la RAM des unités de contrôle</td>
            <td><?php echo round($standardDeviationResult, 2); ?> Mb</td>
        </tr>
        <tr>
            <td>Variance de la taille de stockage entre les unités de contrôle</td>
            <td><?php echo round($varianceResult, 2); ?> Go</td>
        </tr>



        <tr>
            <td>Part du parc informatique hors garantie</td>
            <td style="<?php echo ($percentExpired > 50) ? 'color:red; font-weight:bold;' : 'color:green; font-weight:bold;'; ?>">
                <?php echo round($percentExpired, 1); ?> %
            </td>
        </tr>
        <tr>
            <td>Taille moyenne des écrans</td>
            <td><?php echo round($avgScreenSize, 1); ?> pouces</td>
        </tr>
        <tr>
            <td>Résolution d'écran la plus répandue</td>
            <td><?php echo htmlspecialchars($mostCommonResolution); ?></td>
        </tr>
    </table>

</div>
</body>
</html>