<?php
session_start();

if(isset($_SESSION['username']) && $_SESSION['username'] !== 'adminweb' && $_SESSION['username'] !== 'sysadmin'){
    echo "<div>
<form method='post' action='actions/action-add-screen.php'>
    <label>Numéro de série</label>
    <input type='text' name='serial' required>
    
    <label>Fabricant</label>
    <input type='text' name='manufacturer' required>
    
    <label>Modèle</label>
    <input type='text' name='model' required>
    
    <label>Taille (pouces)</label>
    <input type='number' step='0.1' name='sizeInch' required>
    
    <label>Résolution</label>
    <input type='text' name='resolution' placeholder='1920x1080' required>
    
    <label>Connecteur</label>
    <input type='text' name='connector' placeholder='HDMI, DisplayPort, VGA...' required>
    
    <label>Attaché à (Serial)</label>
    <input type='text' name='attachedTo'>
    
    <button type='submit'>Ajouter l'écran</button>
</form>
</div>";
}
?>