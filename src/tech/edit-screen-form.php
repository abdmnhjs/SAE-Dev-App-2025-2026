<?php
session_start();

require '../includes/init.php';
if($_SESSION["role"] !== "tech"){
    header('location: ../index.php');
    exit();
}// Vérification de la permission et du paramètre 'serial'
$isAuthorized = isset($_SESSION['username']) &&
        $_SESSION['username'] !== 'adminweb' &&
        $_SESSION['username'] !== 'sysadmin' &&
        isset($_GET['serial']);

if ($isAuthorized) {
    $serial = $_GET['serial']; // Récupération brute pour la préparation

    // --- 1. Requête Préparée pour l'écran spécifique ---
    // NOTE: Il y a une double liaison de paramètre ici, qui est redondante mais inoffensive.
    $queryScreen = "SELECT serial, id_manufacturer, model, size_inch, resolution, connector, attached_to FROM screen WHERE serial = ?";
    $stmt = mysqli_prepare($loginToDb, $queryScreen);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $serial);
        // mysqli_stmt_bind_param($stmt, "s", $serial); // Cette ligne était redondante
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $screen = mysqli_fetch_assoc($result);

            // --- 2. Récupérer le nom du fabricant actuel ---
            $manufacturerNameQuery = "SELECT name FROM `manufacturer_list` WHERE id = ?";
            $manufacturerNameStmt = mysqli_prepare($loginToDb, $manufacturerNameQuery);
            $manufacturerData = ['name' => 'N/A'];
            if ($manufacturerNameStmt) {
                mysqli_stmt_bind_param($manufacturerNameStmt, "i", $screen['id_manufacturer']);
                mysqli_stmt_execute($manufacturerNameStmt);
                $manufacturerNameResult = mysqli_stmt_get_result($manufacturerNameStmt);
                $manufacturerData = mysqli_fetch_assoc($manufacturerNameResult) ?: ['name' => 'N/A'];
                mysqli_stmt_close($manufacturerNameStmt);
            }

            // --- 3. Récupérer TOUTES les unités de contrôle disponibles ---
            // CORRECTION: Sélectionner 'name' (clé) et 'serial' (pour info/tri si besoin)
            $allControlUnitsQuery = "SELECT name, serial FROM `central_unit` ORDER BY name";
            $allControlUnitsResult = mysqli_query($loginToDb, $allControlUnitsQuery);

            // --- 4. Récupérer tous les fabricants ---
            $allManufacturersQuery = "SELECT id, name FROM `manufacturer_list` ORDER BY name";
            $allManufacturersResult = mysqli_query($loginToDb, $allManufacturersQuery);

            mysqli_stmt_close($stmt);
            ?>

            <div>
                <form method="post" action="actions/action-edit-screen.php?serial=<?php echo htmlspecialchars($screen['serial']); ?>">
                    <h2>Modification de l'Écran: <?php echo htmlspecialchars($screen['serial']); ?></h2>

                    <label for="edit-screen-serial">Numéro de série</label>
                    <input type="text" name="serial" id="edit-screen-serial" value="<?php echo htmlspecialchars($screen['serial']); ?>" readonly aria-readonly="true">

                    <label for="edit-screen-manufacturer">Fabricant</label>
                    <select name="manufacturer" id="edit-screen-manufacturer" required>
                        <option value="<?php echo htmlspecialchars($screen['id_manufacturer']); ?>" selected>
                            <?php echo htmlspecialchars($manufacturerData['name']); ?>
                        </option>

                        <?php
                        // Liste des fabricants
                        if ($allManufacturersResult) {
                            while($row = mysqli_fetch_assoc($allManufacturersResult)){
                                // Éviter de lister deux fois le fabricant actuel s'il n'est pas "N/A"
                                if (intval($row['id']) !== intval($screen['id_manufacturer'])) {
                                    echo "<option value='".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['name'])."</option>";
                                }
                            }
                        }
                        ?>
                    </select>

                    <label for="edit-screen-model">Modèle</label>
                    <input type="text" name="model" id="edit-screen-model" value="<?php echo htmlspecialchars($screen['model']); ?>" required>

                    <label for="edit-screen-sizeInch">Taille (pouces)</label>
                    <input type="number" step="0.1" name="sizeInch" id="edit-screen-sizeInch" value="<?php echo htmlspecialchars($screen['size_inch']); ?>" required>

                    <label for="edit-screen-resolution">Résolution</label>
                    <input type="text" name="resolution" id="edit-screen-resolution" value="<?php echo htmlspecialchars($screen['resolution']); ?>" placeholder="1920x1080" required>

                    <label for="edit-screen-connector">Connecteur</label>
                    <input type="text" name="connector" id="edit-screen-connector" value="<?php echo htmlspecialchars($screen['connector']); ?>" placeholder="HDMI, DisplayPort, VGA..." required>

                    <label for="edit-screen-attachedTo">Attaché à (optionnel)</label>
                    <select name="attachedTo" id="edit-screen-attachedTo">
                        <option value="<?php echo htmlspecialchars($screen['attached_to']); ?>" selected>
                            <?php echo htmlspecialchars($screen['attached_to'] ?? 'AUCUNE'); ?>
                        </option>
                        <option value="">AUCUNE</option> <?php
                        // Liste des unités de contrôle
                        if ($allControlUnitsResult) {
                            while($row = mysqli_fetch_assoc($allControlUnitsResult)){
                                if ($row['name'] !== $screen['attached_to']) {
                                    echo "<option value='".htmlspecialchars($row['name'])."'>".htmlspecialchars($row['name'])."</option>";
                                }
                            }
                        }
                        ?>
                    </select>
                    <button type="submit">Modifier les informations du moniteur</button>
                </form>


            </div>

            <?php
        } else {
            // L'écran n'existe pas ou la requête a échoué
            echo "<p>Écran non trouvé.</p>";
            if ($stmt) mysqli_stmt_close($stmt); // Fermer le statement s'il a été préparé
        }
    } else {
        echo "<p>Erreur lors de la préparation de la requête: " . mysqli_error($loginToDb) . "</p>";
    }
} else {
    echo "<p>Accès non autorisé ou paramètre 'serial' manquant.</p>";
}

// Fermeture de la connexion
mysqli_close($loginToDb);
?>