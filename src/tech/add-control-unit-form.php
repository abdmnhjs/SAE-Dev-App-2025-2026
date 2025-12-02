<?php
session_start();

if(isset($_SESSION['username']) && $_SESSION['username'] !== 'adminweb' && $_SESSION['username'] !== 'sysadmin'){
    echo "<div>
<form method='post' action='actions/action-add-control-unit.php'>
    <label>Nom</label>
    <input type='text' name='name' required>
    
    <label>Numéro de série</label>
    <input type='text' name='serial' required>
    
    <label>Fabricant</label>
    <input type='text' name='manufacturer' required>
    
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
    <input type='text' name='os' placeholder='Windows 11 Pro' required>
    
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
</div>";
}
?>