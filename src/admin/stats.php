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

        <a class='sidebar-section' href='tech-panel.php?section=screens'>Moniteurs</a>
        <a class='sidebar-section' href='tech-panel.php?section=control-units'>Unités de contrôle</a>
            <a class='sidebar-section' href='add-screen-form.php'>Ajouter un écran</a>
    <a class='sidebar-section' href='add-control-unit-form.php'>Ajouter une unité de controle</a>
        <a class="sidebar-section" href="stats.php">Effectuer des calculs de statistiques</a>
        <a class="sidebar-section" href="probas.php">Effectuer des calculs de probabilités</a>

    </div>
</div>

<div>
    <form method="post" action="actions/stats/mean.php">
        <label for="os">Moyenne des ordinateurs possédant ce système d'exploitation : </label>
        <select name="os" id="os" required>
            <?php
            // Afficher tous les OS
            if ($allOsResult) {
                while($row = mysqli_fetch_assoc($allOsResult)) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "' >"
                        . htmlspecialchars($row['name']) . "</option>";
                }
            }
            ?>
        </select>
        <button type="submit">Calculer la moyenne</button>
    </form>

    <form method="post" action="actions/stats/mean.php">
        <label for="os">Moyenne des moniteurs possédant ce fabricant : </label>
        <select name="os" id="os" required>
            <?php
            // Afficher tous les OS
            if ($allManufacturerResult) {
                while($row = mysqli_fetch_assoc($allManufacturerResult)) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "' >"
                        . htmlspecialchars($row['name']) . "</option>";
                }
            }
            ?>
        </select>
        <input type="hidden" name="type" value="manufacturer">
        <button type="submit">Calculer la moyenne</button>
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

    <?php
    if(isset($_GET["variance"])){
        $varianceResult = $_GET["variance"];

        echo "<p>La variance de la taille de stockage entre les unités de contrôle vaut <span style='font-weight: bold'>".$varianceResult."</span></p>";
    }
    ?>
    <?php

    print_r($_SESSION['mean_result']);

    ?>

    <?php
    if(isset($_GET["standard-deviation"])){
        $standardDeviationResult = $_GET["standard-deviation"];
        echo "<p>L'écart-type de la ram des unités de contrôle vaut <span style='font-weight: bold'>".$standardDeviationResult."</span></p>";

    }
    ?>

    <?php
    if(isset($_GET["medial"])){
        $medialResult = $_GET["medial"];
        echo "<p>La médiane du temps de connexion sur la plateforme (en minutes) vaut <span style='font-weight: bold'>".$medialResult."min</span></p>";

    }
    ?>
</div>
</html>