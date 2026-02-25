<?php
session_start();

// --- Configuration et Connexion à la Base de Données ---
require '../includes/init.php';
if($_SESSION["role"] !== "tech"){
    header('location: ../index.php');
    exit();
}// Vérifie l'accès et la présence du paramètre 'serial'
$isAuthorized = isset($_SESSION['username']) &&
        $_SESSION['username'] !== 'adminweb' &&
        $_SESSION['username'] !== 'sysadmin' &&
        isset($_GET['serial']);

if ($isAuthorized) {
    $serial = $_GET['serial'];

    // --- 1. Requête Préparée pour l'unité de contrôle spécifique ---
    $queryControlUnit = "SELECT * FROM control_unit WHERE serial = ?";
    $stmt = mysqli_prepare($loginToDb, $queryControlUnit);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $serial);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $controlUnit = mysqli_fetch_assoc($result);

            // --- 2. Récupérer le nom du fabricant actuel ---
            $manufacturerId = intval($controlUnit['id_manufacturer']);
            $manufacturerData = ['name' => 'N/A'];
            if ($manufacturerId) {
                $manufacturerNameQuery = "SELECT name FROM `manufacturer_list` WHERE id = ?";
                $manufacturerNameStmt = mysqli_prepare($loginToDb, $manufacturerNameQuery);
                if ($manufacturerNameStmt) {
                    mysqli_stmt_bind_param($manufacturerNameStmt, "i", $manufacturerId);
                    mysqli_stmt_execute($manufacturerNameStmt);
                    $manufacturerNameResult = mysqli_stmt_get_result($manufacturerNameStmt);
                    $manufacturerData = mysqli_fetch_assoc($manufacturerNameResult) ?: $manufacturerData;
                    mysqli_stmt_close($manufacturerNameStmt);
                }
            }

            // --- 3. Récupérer le système d'exploitation actuel ---
            $osId = intval($controlUnit['id_os']);
            $osData = ['name' => 'N/A'];
            if ($osId) {
                $osNameQuery = "SELECT name FROM `os_list` WHERE id = ?";
                $osNameStmt = mysqli_prepare($loginToDb, $osNameQuery);
                if ($osNameStmt) {
                    mysqli_stmt_bind_param($osNameStmt, "i", $osId);
                    mysqli_stmt_execute($osNameStmt);
                    $osNameResult = mysqli_stmt_get_result($osNameStmt);
                    $osData = mysqli_fetch_assoc($osNameResult) ?: $osData;
                    mysqli_stmt_close($osNameStmt);
                }
            }

            // --- 4. Récupérer tous les fabricants ---
            $allManufacturersQuery = "SELECT id, name FROM `manufacturer_list` ORDER BY name";
            $allManufacturersResult = mysqli_query($loginToDb, $allManufacturersQuery);

            // --- 5. Récupérer tous les systèmes d'exploitation ---
            $allOsQuery = "SELECT id, name FROM `os_list` ORDER BY name";
            $allOsResult = mysqli_query($loginToDb, $allOsQuery);

            mysqli_stmt_close($stmt);
            ?>

            <div>
                <form method="post" action="actions/action-edit-control-unit.php?serial=<?php echo htmlspecialchars($controlUnit['serial']); ?>">
                    <h3>Modification de l'Unité de Contrôle (Série: <?php echo htmlspecialchars($controlUnit['serial']); ?>)</h3>

                    <label for="edit-cu-name">Nom</label>
                    <input type="text" name="name" id="edit-cu-name" value="<?php echo htmlspecialchars($controlUnit['name']); ?>" required>

                    <label for="edit-cu-serial">Numéro de série</label>
                    <input type="text" id="edit-cu-serial" value="<?php echo htmlspecialchars($controlUnit['serial']); ?>" readonly aria-readonly="true">

                    <label for="edit-cu-manufacturer">Fabricant</label>
                    <select name="manufacturer" id="edit-cu-manufacturer" required>
                        <option value="<?php echo htmlspecialchars($controlUnit['id_manufacturer']); ?>" selected>
                            <?php echo htmlspecialchars($manufacturerData['name']); ?>
                        </option>
                        <?php
                        // Afficher tous les autres fabricants
                        if ($allManufacturersResult) {
                            while($row = mysqli_fetch_assoc($allManufacturersResult)){
                                if(intval($row['id']) !== $manufacturerId){
                                    echo "<option value='".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['name'])."</option>";
                                }
                            }
                        }
                        ?>
                    </select>

                    <label for="edit-cu-model">Modèle</label>
                    <input type="text" name="model" id="edit-cu-model" value="<?php echo htmlspecialchars($controlUnit['model']); ?>" required>

                    <label for="edit-cu-type">Type</label>
                    <input type="text" name="type" id="edit-cu-type" value="<?php echo htmlspecialchars($controlUnit['type']); ?>" placeholder="PC, Serveur, Laptop..." required>

                    <label for="edit-cu-cpu">CPU</label>
                    <input type="text" name="cpu" id="edit-cu-cpu" value="<?php echo htmlspecialchars($controlUnit['cpu']); ?>" placeholder="Intel Core i7-10700" required>

                    <label for="edit-cu-ramMb">RAM (MB)</label>
                    <input type="number" name="ramMb" id="edit-cu-ramMb" value="<?php echo htmlspecialchars($controlUnit['ram_mb']); ?>" placeholder="16384" required>

                    <label for="edit-cu-diskGb">Disque (GB)</label>
                    <input type="number" name="diskGb" id="edit-cu-diskGb" value="<?php echo htmlspecialchars($controlUnit['disk_gb']); ?>" placeholder="512" required>

                    <label for="edit-cu-os">Système d'exploitation</label>
                    <select name="os" id="edit-cu-os" required>
                        <option value="<?php echo htmlspecialchars($controlUnit['id_os']); ?>" selected>
                            <?php echo htmlspecialchars($osData['name']); ?>
                        </option>
                        <?php
                        // Afficher tous les autres OS
                        if ($allOsResult) {
                            while($row = mysqli_fetch_assoc($allOsResult)){
                                if(intval($row['id']) !== $osId){
                                    echo "<option value='".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['name'])."</option>";
                                }
                            }
                        }
                        ?>
                    </select>

                    <label for="edit-cu-location">Localisation</label>
                    <input type="text" name="location" id="edit-cu-location" value="<?php echo htmlspecialchars($controlUnit['location']); ?>" required>

                    <label for="edit-cu-building">Bâtiment</label>
                    <input type="text" name="building" id="edit-cu-building" value="<?php echo htmlspecialchars($controlUnit['building']); ?>" required>

                    <label for="edit-cu-room">Salle</label>
                    <input type="text" name="room" id="edit-cu-room" value="<?php echo htmlspecialchars($controlUnit['room']); ?>" required>

                    <label for="edit-cu-macaddr">Adresse MAC</label>
                    <input type="text" name="macaddr" id="edit-cu-macaddr" value="<?php echo htmlspecialchars($controlUnit['macaddr']); ?>" placeholder="00:1A:2B:3C:4D:5E" required>

                    <label for="edit-cu-purchaseDate">Date d'achat</label>
                    <input type="date" name="purchaseDate" id="edit-cu-purchaseDate" value="<?php echo htmlspecialchars($controlUnit['purchase_date']); ?>" required>

                    <label for="edit-cu-warrantyEnd">Fin de garantie</label>
                    <input type="date" name="warrantyEnd" id="edit-cu-warrantyEnd" value="<?php echo htmlspecialchars($controlUnit['warranty_end']); ?>" required>

                    <label for="edit-cu-domain">Domaine (optionnel)</label>
                    <input type="text" name="domain" id="edit-cu-domain" value="<?php echo htmlspecialchars($controlUnit['domain']); ?>" placeholder="CORP.LOCAL">

                    <button type="submit">Modifier l'unité de contrôle</button>
                </form>
            </div>

            <?php
        } else {
            echo "<p>Unité de contrôle non trouvée.</p>";
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