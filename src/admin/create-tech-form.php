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
    <title>Admin - Créer un technicien</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/adminweb.css">
</head>
<body class="with-sidebar">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-with-sidebar">
<?php
if (isset($_SESSION['username']) && $_SESSION['username'] === "adminweb"){
    echo "<div>
<form method='post' action='actions/action-create-tech.php'>
<label for='username'>Nom</label>
<input type='text' name='username' id='username'>
<label for='password'>Mot de passe</label>
<input type='password' name='password' id='password'>
<button type='submit' name='submit'>Créer le technicien</button>
</form>
</div>";
}
?>
</main>
</body>
</html>
