<?php
session_start();

// --- Configuration et Connexion à la Base de Données ---
$host = 'localhost';
$user = 'root';
$db_password = ""; // À changer pour les tests en local
$db = "infra";

$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if (!$loginToDb) {
    die("Erreur de connexion à la db: " . mysqli_connect_error());
}

// Vérifie l'accès et la présence du paramètre 'serial'
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
                <form method='post' action='actions/action-edit-control-unit.php?serial=<?php echo htmlspecialchars($controlUnit['serial']); ?>'>
                    <h3>Modification de l'Unité de Contrôle (Série: <?php echo htmlspecialchars($controlUnit['serial']); ?>)</h3>

                    <label>Nom</label>
                    <input type='text' name='name' value='<?php echo htmlspecialchars($controlUnit['name']); ?>' required>

                    <label>Numéro de série</label>
                    <input type='text' value='<?php echo htmlspecialchars($controlUnit['serial']); ?>' readonly required>

                    <label>Fabricant</label>
                    <select name='manufacturer' required>
                        <option value='<?php echo htmlspecialchars($controlUnit['id_manufacturer']); ?>' selected>
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

                    <label>Modèle</label>
                    <input type='text' name='model' value='<?php echo htmlspecialchars($controlUnit['model']); ?>' required>

                    <label>Type</label>
                    <input type='text' name='type' value='<?php echo htmlspecialchars($controlUnit['type']); ?>' placeholder='PC, Serveur, Laptop...' required>

                    <label>CPU</label>
                    <input type='text' name='cpu' value='<?php echo htmlspecialchars($controlUnit['cpu']); ?>' placeholder='Intel Core i7-10700' required>

                    <label>RAM (MB)</label>
                    <input type='number' name='ramMb' value='<?php echo htmlspecialchars($controlUnit['ram_mb']); ?>' placeholder='16384' required>

                    <label>Disque (GB)</label>
                    <input type='number' name='diskGb' value='<?php echo htmlspecialchars($controlUnit['disk_gb']); ?>' placeholder='512' required>

                    <label>Système d'exploitation</label>
                    <select name='os' required>
                        <option value='<?php echo htmlspecialchars($controlUnit['id_os']); ?>' selected>
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

                    <label>Domaine</label>
                    <input type='text' name='domain' value='<?php echo htmlspecialchars($controlUnit['domain']); ?>' placeholder='CORP.LOCAL'>

                    <label>Localisation</label>
                    <input type='text' name='location' value='<?php echo htmlspecialchars($controlUnit['location']); ?>' required>

                    <label>Bâtiment</label>
                    <input type='text' name='building' value='<?php echo htmlspecialchars($controlUnit['building']); ?>' required>

                    <label>Salle</label>
                    <input type='text' name='room' value='<?php echo htmlspecialchars($controlUnit['room']); ?>' required>

                    <label>Adresse MAC</label>
                    <input type='text' name='macaddr' value='<?php echo htmlspecialchars($controlUnit['macaddr']); ?>' placeholder='00:1A:2B:3C:4D:5E' required>

                    <label>Date d'achat</label>
                    <input type='date' name='purchaseDate' value='<?php echo htmlspecialchars($controlUnit['purchase_date']); ?>' required>

                    <label>Fin de garantie</label>
                    <input type='date' name='warrantyEnd' value='<?php echo htmlspecialchars($controlUnit['warranty_end']); ?>' required>

                    <button type='submit'>Modifier l'unité de contrôle</button>
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