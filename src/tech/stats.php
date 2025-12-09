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
        <button type="submit">Calculer la moyenne</button>
    </form>

    <form method="post" action="actions/stats/variance.php">
        <label>Variance de la taille de stockage entre les unités de contrôle</label>
        <button type="submit">Calculer la variance</button>
    </form>

    <form method="post" action="actions/stats/standard-deviation.php">
        <label>Ecart type de la date d'achat des unités de contrôle</label>
        <button type="submit">Calculer l'écart type</button>
    </form>

    <form method="post" action="actions/stats/medial.php">
        <label>Médiane du temps de connexion sur la plateforme</label>
        <button type="submit">Calculer la médiane</button>
    </form>
</div>