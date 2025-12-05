<?php
session_start();

$filename = '../includes/connexion_bdd.php';

if (file_exists($filename)) {
    require $filename;
} else {
    echo "<b>Erreur :</b> le fichier $filename est introuvable.";
}

if (isset($_SESSION['username']) && $_SESSION['username'] === "adminweb"){
    echo "<div>
<h1>Ici c l'admin panel</h1>
    <a href='../logout.php' class='sections'>Se déconnecter</a>
    <a href='create-tech-form.php'>Créer un technicien</a> 
    <a href='add-os-form.php'>Ajouter un système d'exploitation</a>
    <a href='add-manufacturer-form.php'>Ajouter un fabriquant</a>

</div>
";
}