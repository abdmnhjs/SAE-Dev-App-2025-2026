<?php
session_start();

require '../includes/init.php';
ensureUserAuthorized("tech");

// --- Récupérer tous les systèmes d'exploitation ---
$allOsQuery = "SELECT id, name FROM `os_list` ORDER BY name";
$allOsResult = mysqli_query($loginToDb, $allOsQuery);

$allManufacturerQuery = "SELECT id, name FROM `manufacturer_list` ORDER BY name";
$allManufacturerResult = mysqli_query($loginToDb, $allManufacturerQuery);
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

    <form method="post" action="actions/stats/variance.php">
        <label>Variance de la taille de stockage entre les unités de contrôle</label>
        <button type="submit">Calculer la variance</button>
    </form>

    <form method="post" action="actions/stats/standard-deviation.php">
        <label>Ecart type de la ram des unités de contrôle</label>
        <button type="submit">Calculer l'écart type</button>
    </form>

    <form method="post" action="actions/stats/medial.php">
        <label>Médiane du temps de connexion sur la plateforme</label>
        <button type="submit">Calculer la médiane</button>
    </form>

    <hr> <?php
    if(isset($_GET["variance"])){
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
    if(isset($_GET["standard-deviation"])){
        $standardDeviationResult = htmlspecialchars($_GET["standard-deviation"]);
        echo "<p>L'écart-type de la ram des unités de contrôle vaut <span style='font-weight: bold'>".$standardDeviationResult."</span></p>";
    }
    ?>

    <?php
    if(isset($_GET["medial"])){
        $medialResult = htmlspecialchars($_GET["medial"]);
        echo "<p>La médiane du temps de connexion sur la plateforme (en minutes) vaut <span style='font-weight: bold'>".$medialResult."min</span></p>";
    }
    ?>
</div>
</html>