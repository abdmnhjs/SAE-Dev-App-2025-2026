<?php
session_start();

require '../includes/init.php';
ensureUserAuthorized("tech");
// Vérification de la permission et du paramètre 'serial'
$isAuthorized = isset($_SESSION['username']) &&
        $_SESSION['username'] !== 'adminweb' &&
        $_SESSION['username'] !== 'sysadmin' &&
        isset($_GET['serial']);

if ($isAuthorized) {
    $serial = $_GET['serial']; // Récupération brute pour la préparation

    // --- 1. Requête Préparée pour l'écran spécifique ---
    $queryScreen = "SELECT serial, id_manufacturer, model, size_inch, resolution, connector, attached_to FROM screen WHERE serial = ?";
    $stmt = mysqli_prepare($loginToDb, $queryScreen);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $serial);
        mysqli_stmt_bind_param($stmt, "s", $serial);
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
            $allControlUnitsQuery = "SELECT serial FROM `control_unit`";
            $allControlUnitsResult = mysqli_query($loginToDb, $allControlUnitsQuery);

            // --- 4. Récupérer tous les fabricants ---
            $allManufacturersQuery = "SELECT id, name FROM `manufacturer_list` ORDER BY name";
            $allManufacturersResult = mysqli_query($loginToDb, $allManufacturersQuery);

            mysqli_stmt_close($stmt);
            ?>

            <div>
                <form method='post' action='actions/action-edit-screen.php?serial=<?php echo htmlspecialchars($screen['serial']); ?>'>
                    <h2>Modification de l'Écran: <?php echo htmlspecialchars($screen['serial']); ?></h2>

                    <label>Numéro de série</label>
                    <input type='text' name='serial' value='<?php echo htmlspecialchars($screen['serial']); ?>' readonly required>

                    <label>Fabricant</label>
                    <select name='manufacturer' required>
                        <option value='<?php echo htmlspecialchars($screen['id_manufacturer']); ?>' selected>
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

                    <label>Modèle</label>
                    <input type='text' name='model' value='<?php echo htmlspecialchars($screen['model']); ?>' required>

                    <label>Taille (pouces)</label>
                    <input type='number' step='0.1' name='sizeInch' value='<?php echo htmlspecialchars($screen['size_inch']); ?>' required>

                    <label>Résolution</label>
                    <input type='text' name='resolution' value='<?php echo htmlspecialchars($screen['resolution']); ?>' placeholder='1920x1080' required>

                    <label>Connecteur</label>
                    <input type='text' name='connector' value='<?php echo htmlspecialchars($screen['connector']); ?>' placeholder='HDMI, DisplayPort, VGA...' required>

                    <label>Attaché à</label>
                    <select name='attachedTo' required>
                        <option value='<?php echo htmlspecialchars($screen['attached_to']); ?>' selected>
                            <?php echo htmlspecialchars($screen['attached_to'] ?? 'AUCUNE'); ?>
                        </option>
                        <option value=''>AUCUNE</option> <?php
                        // Liste des unités de contrôle
                        if ($allControlUnitsResult) {
                            while($row = mysqli_fetch_assoc($allControlUnitsResult)){
                                if ($row['serial'] !== $screen['attached_to']) {
                                    echo "<option value='".htmlspecialchars($row['serial'])."'>".htmlspecialchars($row['serial'])."</option>";
                                }
                            }
                        }
                        ?>
                    </select>
                    <button type='submit'>Modifier les informations du moniteur</button>
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