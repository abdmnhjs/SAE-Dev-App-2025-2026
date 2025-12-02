<?php
session_start();

$host = 'localhost';
$user = 'root';
$db_password = ""; //penser a le changer si vous faites des tests en locaux, le mdp du rpi12 est : !sae2025!
$db = "infra";
$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if (isset($_SESSION['username']) && $_SESSION['username'] !== "adminweb" && $_SESSION['username'] !== "sysadmin") {
    echo "<div>
<h1>Ici c le tech panel</h1>
    <a href='../logout.php' class='sections'>Se déconnecter</a>
    <a href='add-screen-form.php'>Ajouter un écran</a> 
    <a href='add-control-unit-form.php'>Ajouter une unité de controle</a> 

</div>
";
}