<?php
session_start();

require '../includes/init.php';
if($_SESSION["role"] !== "adminweb"){
    header('location: ../index.php');
    exit();
}
$sidebarBase = '../';
$sidebarAdminPrefix = '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset='UTF-8'>
    <title>Admin - Ajouter un OS</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/adminweb.css">
</head>
<body class="with-sidebar">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-with-sidebar">
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
</main>
</body>
</html>