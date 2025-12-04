<?php
session_start();

$host = 'localhost';
$user = 'root';
$db_password = ""; //penser a le changer si vous faites des tests en locaux, le mdp du rpi12 est : !sae2025!
$db = "infra";
$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if(!$loginToDb){
    die("Erreur de connexion à la db: " . mysqli_connect_error());
}

$select = mysqli_select_db($loginToDb, $db);
if (!$select) {
    die("Erreur");
} else {
    // Vérifie l'accès et la présence du paramètre 'serial'
    if(isset($_SESSION['username']) &&
        $_SESSION['username'] !== 'adminweb' && $_SESSION['username'] !== 'sysadmin'
        && isset($_GET['serial'])){

        $serial = mysqli_real_escape_string($loginToDb, $_GET['serial']);
        $queryControlUnit = "SELECT * FROM control_unit WHERE serial = '$serial'";
        $result = mysqli_query($loginToDb, $queryControlUnit);

        if($result && mysqli_num_rows($result) > 0){
            $controlUnit = mysqli_fetch_assoc($result);

            // Récupérer le fabricant actuel
            $manufacturerNameQuery = "SELECT name FROM `manufacturer_list` WHERE id = ". intval($controlUnit['id_manufacturer']);
            $manufacturerNameResult = mysqli_query($loginToDb, $manufacturerNameQuery);
            $manufacturerData = mysqli_fetch_assoc($manufacturerNameResult);

            // Récupérer tous les fabricants
            $allManufacturersQuery = "SELECT id, name FROM `manufacturer_list`";
            $allManufacturersResult = mysqli_query($loginToDb, $allManufacturersQuery);

            // Récupérer le système d'exploitation actuel (si la table existe)
            $osData = null;
            if(isset($controlUnit['id_os']) && !empty($controlUnit['id_os'])){
                $osNameQuery = "SELECT name FROM `os_list` WHERE id = ". intval($controlUnit['id_os']);
                $osNameResult = mysqli_query($loginToDb, $osNameQuery);
                if($osNameResult){
                    $osData = mysqli_fetch_assoc($osNameResult);
                }
            }

            // Récupérer tous les systèmes d'exploitation
            $allOsQuery = "SELECT id, name FROM `os_list`";
            $allOsResult = mysqli_query($loginToDb, $allOsQuery);
            ?>

            <div>
                <form method='post' action='actions/action-edit-control-unit.php?serial=<?php echo htmlspecialchars($serial); ?>'>
                    <h3>Modification de l'Unité de Contrôle (Série: <?php echo htmlspecialchars($controlUnit['serial']); ?>)</h3>

                    <label>Nom</label>
                    <input type='text' name='name' value='<?php echo htmlspecialchars($controlUnit['name']); ?>' required>

                    <label>Numéro de série</label>
                    <input type='hidden' name='serial' value='<?php echo htmlspecialchars($controlUnit['serial']); ?>'>
                    <input type='text' value='<?php echo htmlspecialchars($controlUnit['serial']); ?>' readonly required>

                    <label>Fabricant</label>
                    <select name='manufacturer' required>
                        <option value='<?php echo htmlspecialchars($controlUnit['id_manufacturer']); ?>'>
                            <?php echo htmlspecialchars($manufacturerData['name'] ?? 'N/A'); ?>
                        </option>
                        <?php
                        while($row = mysqli_fetch_assoc($allManufacturersResult)){
                            if($row['id'] != $controlUnit['id_manufacturer']){
                                echo "<option value='".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['name'])."</option>";
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
                    <?php if($allOsResult && mysqli_num_rows($allOsResult) > 0): ?>
                        <select name='os' required>
                            <option value='<?php echo htmlspecialchars($controlUnit['id_os'] ?? ''); ?>'>
                                <?php echo htmlspecialchars($osData['name'] ?? $controlUnit['os'] ?? 'N/A'); ?>
                            </option>
                            <?php
                            while($row = mysqli_fetch_assoc($allOsResult)){
                                if($row['id'] != ($controlUnit['id_os'] ?? null)){
                                    echo "<option value='".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['name'])."</option>";
                                }
                            }
                            ?>
                        </select>
                    <?php else: ?>
                        <input type='text' name='os' value='<?php echo htmlspecialchars($controlUnit['os']); ?>' placeholder='Windows 11 Pro' required>
                    <?php endif; ?>

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
        echo "<p>Accès non autorisé ou paramètre manquant.</p>";
    }
}
?>