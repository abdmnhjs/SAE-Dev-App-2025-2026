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
    if(isset($_SESSION['username']) &&
        $_SESSION['username'] !== 'adminweb' && $_SESSION['username'] !== 'sysadmin'
        && isset($_GET['serial'])){

        $serial = mysqli_real_escape_string($loginToDb, $_GET['serial']);
        $queryScreen = "SELECT * FROM screen WHERE serial = '$serial'";
        $result = mysqli_query($loginToDb, $queryScreen);

        if($result && mysqli_num_rows($result) > 0){
            $screen = mysqli_fetch_assoc($result);

            echo "<div>
<form method='post' action='actions/action-edit-screen.php'>
    <label>Numéro de série</label>
    <input type='text' name='serial' value='" . htmlspecialchars($screen['serial']) . "' readonly required>
    
    <label>Fabricant</label>
    <input type='text' name='manufacturer' value='" . htmlspecialchars($screen['manufacturer']) . "' required>
    
    <label>Modèle</label>
    <input type='text' name='model' value='" . htmlspecialchars($screen['model']) . "' required>
    
    <label>Taille (pouces)</label>
    <input type='number' step='0.1' name='sizeInch' value='" . htmlspecialchars($screen['size_inch']) . "' required>
    
    <label>Résolution</label>
    <input type='text' name='resolution' value='" . htmlspecialchars($screen['resolution']) . "' placeholder='1920x1080' required>
    
    <label>Connecteur</label>
    <input type='text' name='connector' value='" . htmlspecialchars($screen['connector']) . "' placeholder='HDMI, DisplayPort, VGA...' required>
    
    <label>Attaché à (Serial)</label>
    <input type='text' name='attachedTo' value='" . htmlspecialchars($screen['attached_to']) . "'>
    
    <button type='submit'>Modifier les informations du moniteur</button>
</form>
</div>";
        } else {
            echo "<p>Écran non trouvé.</p>";
        }
    } else {
        echo "<p>Accès non autorisé ou paramètre manquant.</p>";
    }
}
?>