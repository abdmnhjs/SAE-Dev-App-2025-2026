<?php
session_start();

require '../includes/init.php';
ensureUserAuthorized("adminweb");

if (isset($_SESSION['username']) && $_SESSION['username'] === "adminweb"){
    echo "<div>
<h1>Bienvenue adminweb</h1>
    <a href='../logout.php' class='sections'>Se déconnecter</a>
    <a href='create-tech-form.php'>Créer un technicien</a> 
    <a href='add-os-form.php'>Ajouter un système d'exploitation</a>
    <a href='add-manufacturer-form.php'>Ajouter un fabriquant</a>

</div>
";
}