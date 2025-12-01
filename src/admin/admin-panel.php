<?php
session_start();

$host = 'localhost';
$user = 'root';
$db_password = "!sae2025!"; //penser a le changer si vous faites des tests en locaux, le mdp du rpi12 est : !sae2025!
$db = "infra";
$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if (isset($_SESSION['username']) && $_SESSION['username'] === "adminweb"){
    echo "<div>
<h1>Ici c l'admin panel</h1>
    <a href='../logout.php' class='sections'>Se déconnecter</a>
    <a href='create-tech-form.php'>Créer un technicien</a> 

</div>
";
}