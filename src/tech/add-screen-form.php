<?php
session_start();

$host = 'localhost';
$user = 'root';
$db_password = ""; // À changer pour les tests en local
$db = "infra";

// Connexion à la base de données
$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if (!$loginToDb) {
    die("Erreur de connexion à la db: " . mysqli_connect_error());
}

// Vérifie l'accès
if(isset($_SESSION['username']) && $_SESSION['username'] !== 'adminweb' && $_SESSION['username'] !== 'sysadmin'){

    // Récupérer tous les fabricants
    $allManufacturersQuery = "SELECT id, name FROM `manufacturer_list` ORDER BY name";
    $allManufacturersResult = mysqli_query($loginToDb, $allManufacturersQuery);

    // Récupérer TOUTES les unités de contrôle disponibles
    $allControlUnitsQuery = "SELECT serial FROM `control_unit` ORDER BY serial";
    $allControlUnitsResult = mysqli_query($loginToDb, $allControlUnitsQuery);

    ?>
    <div>
        <form method='post' action='actions/action-add-screen.php'>
            <h2>Ajouter un nouvel écran</h2>

            <label>Numéro de série</label>
            <input type='text' name='serial' required>

            <label>Fabricant</label>
            <select name='manufacturer_id' required>
                <option value=''>-- Sélectionner un fabricant --</option>
                <?php
                if ($allManufacturersResult) {
                    while($row = mysqli_fetch_assoc($allManufacturersResult)){
                        echo "<option value='".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['name'])."</option>";
                    }
                }
                ?>
            </select>

            <label>Modèle</label>
            <input type='text' name='model' required>

            <label>Taille (pouces)</label>
            <input type='number' step='0.1' name='sizeInch' required>

            <label>Résolution</label>
            <input type='text' name='resolution' placeholder='1920x1080' required>

            <label>Connecteur</label>
            <input type='text' name='connector' placeholder='HDMI, DisplayPort, VGA...' required>

            <label>Attaché à (Optionnel)</label>
            <select name='attachedTo'>
                <option value='' selected>-- Non attaché --</option>
                <?php
                if ($allControlUnitsResult) {
                    while($row = mysqli_fetch_assoc($allControlUnitsResult)){
                        echo "<option value='".htmlspecialchars($row['serial'])."'>".htmlspecialchars($row['serial'])."</option>";
                    }
                }
                ?>
            </select>

            <button type='submit'>Ajouter l'écran</button>
        </form>
    </div>
    <?php
} else {
    echo "<p>Accès non autorisé.</p>";
}

mysqli_close($loginToDb);
?>