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
    $queryControlUnit = "SELECT * FROM control_unit";
    $controlUnits = mysqli_query($loginToDb, $queryControlUnit);
    $queryScreen = "SELECT * FROM screen";
    $screens = mysqli_query($loginToDb, $queryScreen);
    if (isset($_SESSION['username']) && $_SESSION['username'] !== "adminweb" && $_SESSION['username'] !== "sysadmin") {
        echo "
<html>
<head>
    <meta charset='UTF-8'>
    <title>Tech Panel</title>
    <style>
    /* Container de la sidebar */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    height: 100vh;
    width: 250px;
    background-color: #ffffff;
    padding: 20px 0;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
}

body {
background-color: #121212
}

body a, h1 {
color: #FFFFFF;
}



.sidebar a {
color: black;
}

/* Container des sections */
.sidebar-sections {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 0 15px;
}

/* Style des liens de section */
.sidebar-section {
    display: block;
    padding: 15px 20px;
    color: #ecf0f1;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 16px;
}

/* Effet au survol */
.sidebar-section:hover {
    color: #fff;
    background-color: #121212;
    transform: translateX(5px);
}

/* Section active (optionnel) */
.sidebar-section.active {
    background-color: #3498db;
    font-weight: bold;
}

/* Ajustement du contenu principal pour faire de la place à la sidebar */
body {
    margin-left: 250px;
}
</style>
 </head>
    
<div>
<div class='sidebar'>
    <div class='sidebar-sections'>
        <a class='sidebar-section'>Moniteurs</a>
        <a class='sidebar-section'>Unités de contrôle</a>
    </div>
</div>


<h1>Ici c le tech panel</h1>
    <a href='../logout.php' class='sections'>Se déconnecter</a>
    <a href='add-screen-form.php'>Ajouter un écran</a> 
    <a href='add-control-unit-form.php'>Ajouter une unité de controle</a> 

</div>
</html>

";
    }
}

