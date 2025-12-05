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

    // Récupérer tous les systèmes d'exploitation
    $allOsQuery = "SELECT id, name FROM `os_list` ORDER BY name";
    $allOsResult = mysqli_query($loginToDb, $allOsQuery);

    ?>
    <div>
        <form method='post' action='actions/action-add-control-unit.php'>
            <h2>Ajouter une nouvelle unité de contrôle</h2>

            <label>Nom</label>
            <input type='text' name='name' required>

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

            <label>Type</label>
            <input type='text' name='type' placeholder='PC, Serveur, Laptop...' required>

            <label>CPU</label>
            <input type='text' name='cpu' placeholder='Intel Core i7-10700' required>

            <label>RAM (MB)</label>
            <input type='number' name='ramMb' placeholder='16384' required>

            <label>Disque (GB)</label>
            <input type='number' name='diskGb' placeholder='512' required>

            <label>Système d'exploitation</label>
            <select name='os_id' required>
                <option value=''>-- Sélectionner un OS --</option>
                <?php
                if ($allOsResult) {
                    while($row = mysqli_fetch_assoc($allOsResult)){
                        echo "<option value='".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['name'])."</option>";
                    }
                }
                ?>
            </select>

            <label>Domaine</label>
            <input type='text' name='domain' placeholder='CORP.LOCAL'>

            <label>Localisation</label>
            <input type='text' name='location' required>

            <label>Bâtiment</label>
            <input type='text' name='building' required>

            <label>Salle</label>
            <input type='text' name='room' required>

            <label>Adresse MAC</label>
            <input type='text' name='macaddr' placeholder='00:1A:2B:3C:4D:5E' required>

            <label>Date d'achat</label>
            <input type='date' name='purchaseDate' required>

            <label>Fin de garantie</label>
            <input type='date' name='warrantyEnd' required>

            <button type='submit'>Ajouter l'unité de contrôle</button>
        </form>
    </div>
    <?php
} else {
    echo "<p>Accès non autorisé.</p>";
}

mysqli_close($loginToDb);
?>