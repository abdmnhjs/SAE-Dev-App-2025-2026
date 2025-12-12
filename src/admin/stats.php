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

// --- 2. Calculs statistiques globaux ---

// A. Médiane (Durée)
$queryDuration = "SELECT duration_seconds FROM logs";
$resultDuration = mysqli_query($loginToDb, $queryDuration);
$durationValues = [];
while ($row = mysqli_fetch_assoc($resultDuration)) {
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

// D. Taux de machines hors garantie
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

// E. Taille moyenne des écrans
$queryScreenSize = "SELECT AVG(size_inch) as avg_size FROM screen WHERE is_active = 1";
$resultScreenSize = mysqli_query($loginToDb, $queryScreenSize);
$rowScreenSize = mysqli_fetch_assoc($resultScreenSize);
$avgScreenSize = ($rowScreenSize && $rowScreenSize['avg_size']) ? $rowScreenSize['avg_size'] : 0;

// F. Mode (Résolution)
$queryRes = "SELECT resolution FROM screen WHERE is_active = 1";
$resultRes = mysqli_query($loginToDb, $queryRes);
$resolutions = [];
while ($row = mysqli_fetch_assoc($resultRes)) {
    $resolutions[] = $row['resolution'];
}
$mostCommonResolution = "Aucune donnée";
if (count($resolutions) > 0) {
    $valuesCount = array_count_values($resolutions);
    arsort($valuesCount);
    $mostCommonResolution = array_key_first($valuesCount);
}

// --- 3. PRÉPARATION DES DONNÉES POUR CHART.JS ---

// G. Répartition par OS (pour le Graphique 1)
// CORRECTION ICI : COUNT(c.name) au lieu de COUNT(c.id)
$queryChartOs = "SELECT l.name, COUNT(c.name) as count 
                 FROM control_unit c 
                 JOIN os_list l ON c.id_os = l.id 
                 GROUP BY l.name";
$resultChartOs = mysqli_query($loginToDb, $queryChartOs);

$osLabels = [];
$osData = [];
if ($resultChartOs) {
    while($row = mysqli_fetch_assoc($resultChartOs)){
        $osLabels[] = $row['name'];
        $osData[] = $row['count'];
    }
}

// H. Répartition par Fabricant d'écrans (pour le Graphique 2)
$queryChartMan = "SELECT m.name, COUNT(s.serial) as count 
                  FROM screen s 
                  JOIN manufacturer_list m ON s.id_manufacturer = m.id 
                  WHERE s.is_active = 1
                  GROUP BY m.name";
$resultChartMan = mysqli_query($loginToDb, $queryChartMan);

$manLabels = [];
$manData = [];
if ($resultChartMan) {
    while($row = mysqli_fetch_assoc($resultChartMan)){
        $manLabels[] = $row['name'];
        $manData[] = $row['count'];
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset='UTF-8'>
    <title>Tech Panel</title>
    <link rel="stylesheet" href="../css/tech/tech-panel.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 40px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }

        .charts-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-around;
            margin-bottom: 30px;
        }
        .chart-box {
            width: 45%;
            min-width: 300px;
            background: #fff;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h4 { text-align: center; color: #333; }
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

    <h2>Tableau de bord statistique</h2>

    <div class="charts-container">
        <div class="chart-box">
            <h4>Répartition des OS (Unités Centrales)</h4>
            <canvas id="osChart"></canvas>
        </div>
        <div class="chart-box">
            <h4>Parts de marché Écrans (Fabricants)</h4>
            <canvas id="manufacturerChart"></canvas>
        </div>
    </div>

    <hr>

    <h3>Indicateurs Mathématiques</h3>
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

    <hr>

</div>

<script>
    const osLabels = <?php echo json_encode($osLabels); ?>;
    const osData = <?php echo json_encode($osData); ?>;

    const manLabels = <?php echo json_encode($manLabels); ?>;
    const manData = <?php echo json_encode($manData); ?>;

    const chartColors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
        '#e74c3c', '#3498db', '#f1c40f', '#2ecc71', '#9b59b6', '#e67e22'
    ];

    const ctxOs = document.getElementById('osChart').getContext('2d');
    if(osLabels.length > 0) {
        new Chart(ctxOs, {
            type: 'doughnut',
            data: {
                labels: osLabels,
                datasets: [{
                    label: 'Nombre d\'unités',
                    data: osData,
                    backgroundColor: chartColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    const ctxMan = document.getElementById('manufacturerChart').getContext('2d');
    if(manLabels.length > 0) {
        new Chart(ctxMan, {
            type: 'pie',
            data: {
                labels: manLabels,
                datasets: [{
                    label: 'Nombre d\'écrans',
                    data: manData,
                    backgroundColor: chartColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
</script>

</body>
</html>