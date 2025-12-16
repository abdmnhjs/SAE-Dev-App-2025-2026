<?php
session_start();

require '../includes/init.php';
if($_SESSION["role"] !== "adminweb"){
    header('location: ../index.php');
    exit();
}?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Tech Panel</title>
    <link rel="stylesheet" href="../css/adminweb.css">
</head>
<body>
<div class='sidebar'>
    <div class='sidebar-sections'>
        <a class='sidebar-section' href='../logout.php' class='sections'>Se déconnecter</a>
        <a class='sidebar-section' href='create-tech-form.php'>Créer un technicien</a>
        <a class='sidebar-section' href='add-os-form.php'>Ajouter un système d'exploitation</a>
        <a class='sidebar-section' href='add-manufacturer-form.php'>Ajouter un fabriquant</a>
        <a class="sidebar-section" href="../stats.php">Statistiques</a>
        <a class="sidebar-section" href="admin_panel-logs.php">Logs</a>

    </div>
</div>
<?php

if (isset($_SESSION['username']) && $_SESSION['username'] === "adminweb"){

    echo "
<div>
    <form method='post' action='actions/action-add-os.php'>
        <label>Nom</label>
        <input type='text' name='os_name' >
        <button type='submit'>Ajouter le système d'exploitation</button>
    </form>
</div>
    ";

 } ?>
</html>