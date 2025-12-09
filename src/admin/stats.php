<?php

session_start();
require '../includes/init.php';
ensureUserAuthorized("adminweb");

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
    <form method="post" action="actions/stats/mean.php">
        <label for="os">Moyenne des ordinateurs possédant ce système d'exploitation : </label>
        <select name="os_id" id="os" required>
            <?php
            // Afficher tous les manufactureurs
            if ($allOsResult) {
                while($row = mysqli_fetch_assoc($allOsResult)) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "' >"
                        . htmlspecialchars($row['name']) . "</option>";
                }
            }
            ?>
        </select>
        <input type="hidden" name="type" value="os">
        <button type="submit" name="os">Calculer la moyenne</button>
    </form>

    <form method="post" action="actions/stats/mean.php">
        <label for="manufacturer">Moyenne des moniteurs possédant ce fabricant : </label>
        <select name="manufacturer_id" id="manufacturer" required>
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

    <form method="post" action="actions/stats/mean.php">
        <label>Variance de la taille de stockage entre les unités de contrôle</label>
        <button type="submit">Calculer la variance</button>
    </form>

    <form method="post" action="actions/stats/mean.php">
        <label>Ecart type de la date d'achat des unités de contrôle</label>
        <button type="submit">Calculer l'écart type</button>
    </form>

    <form method="post" action="actions/stats/mean.php">
        <label>Médiane du temps de connexion sur la plateforme</label>
        <button type="submit">Calculer la médiane</button>
    </form>
    <?php

    print_r($_SESSION['mean_result'] ?? '');

    ?>
</div>
</html>