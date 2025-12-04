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

            echo "<div>
<form method='post' action='actions/action-edit-control-unit.php?serial=$serial'>
    <h3>Modification de l'Unité de Contrôle (Série: ".htmlspecialchars($controlUnit['serial']).")</h3>
    
    <label>Nom</label>
    <input type='text' name='name' value='".htmlspecialchars($controlUnit['name'])."' required>
    
    <label>Numéro de série</label>
    <input type='hidden' name='serial' value='".htmlspecialchars($controlUnit['serial'])."' required>
    <input type='text' value='".htmlspecialchars($controlUnit['serial'])."' required>
    
    <label>Fabricant</label>
    <input type='text' name='manufacturer' value='".htmlspecialchars($controlUnit['manufacturer'])."' required>
    
    <label>Modèle</label>
    <input type='text' name='model' value='".htmlspecialchars($controlUnit['model'])."' required>
    
    <label>Type</label>
    <input type='text' name='type' value='".htmlspecialchars($controlUnit['type'])."' placeholder='PC, Serveur, Laptop...' required>
    
    <label>CPU</label>
    <input type='text' name='cpu' value='".htmlspecialchars($controlUnit['cpu'])."' placeholder='Intel Core i7-10700' required>
    
    <label>RAM (MB)</label>
    <input type='number' name='ramMb' value='".htmlspecialchars($controlUnit['ram_mb'])."' placeholder='16384' required>
    
    <label>Disque (GB)</label>
    <input type='number' name='diskGb' value='".htmlspecialchars($controlUnit['disk_gb'])."' placeholder='512' required>
    
    <label>Système d'exploitation</label>
    <input type='text' name='os' value='".htmlspecialchars($controlUnit['os'])."' placeholder='Windows 11 Pro' required>
    
    <label>Domaine</label>
    <input type='text' name='domain' value='".htmlspecialchars($controlUnit['domain'])."' placeholder='CORP.LOCAL'>
    
    <label>Localisation</label>
    <input type='text' name='location' value='".htmlspecialchars($controlUnit['location'])."' required>
    
    <label>Bâtiment</label>
    <input type='text' name='building' value='".htmlspecialchars($controlUnit['building'])."' required>
    
    <label>Salle</label>
    <input type='text' name='room' value='".htmlspecialchars($controlUnit['room'])."' required>
    
    <label>Adresse MAC</label>
    <input type='text' name='macaddr' value='".htmlspecialchars($controlUnit['macaddr'])."' placeholder='00:1A:2B:3C:4D:5E' required>
    
    <label>Date d'achat</label>
    <input type='date' name='purchaseDate' value='".htmlspecialchars($controlUnit['purchase_date'])."' required>
    
    <label>Fin de garantie</label>
    <input type='date' name='warrantyEnd' value='".htmlspecialchars($controlUnit['warranty_end'])."' required>
    
    <button type='submit'>Modifier l'unité de contrôle</button>
</form>
</div>";
        } else {
            echo "<p>Unité de contrôle non trouvée.</p>";
        }
    } else {
        echo "<p>Accès non autorisé ou paramètre manquant.</p>";
    }
}
?>