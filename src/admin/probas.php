<?php
session_start();

require '../includes/init.php';
ensureUserAuthorized("adminweb");


$allControlUnitsQuery = "SELECT name FROM `control_unit` ";
$allControlUnitsResult = mysqli_query($loginToDb, $allControlUnitsQuery);


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Tech Panel</title>
    <link rel="stylesheet" href="../css/adminweb/adminweb.css">
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

    <form method="post" action="actions/probas/simple-proba.php">
        <label>Probabilité qu'une unité de contrôle va être dans le rébut</label>
        <select name="control_unit">
            <?php
            if ($allControlUnitsResult) {
                while($row = mysqli_fetch_assoc($allControlUnitsResult)) {
                    echo "<option value='" . htmlspecialchars($row['serial']) . "' >"
                        . htmlspecialchars($row['name']) . "</option>";
                }
            }
            ?>
        </select>
        <button type="submit">Calculer la probabilité</button>
    </form>
</div>
</html>