<?php
session_start();
require 'includes/init.php';

if (isset($_SESSION['username']) && isset($_SESSION['session_start_time'])) {
    // 1. On sécurise les variables de base
    $username = mysqli_real_escape_string($loginToDb, $_SESSION['username']);
    $ip_address = mysqli_real_escape_string($loginToDb, $_SERVER['REMOTE_ADDR']);
    $duration = time() - $_SESSION['session_start_time'];

    // 2. On prépare la phrase ET on l'échappe pour gérer l'apostrophe de "s'est"
    $raw_desc = $_SESSION['username'] . " s'est déconnecté";
    $action = mysqli_real_escape_string($loginToDb, $raw_desc);

    // 3. Correction de la requête (virgules ajoutées + nom de colonne corrigé)
    $insert = "INSERT INTO logs (username, description, ip_address, duration_seconds) 
               VALUES ('$username', '$action', '$ip_address', $duration)";

    mysqli_query($loginToDb, $insert);
}

session_destroy();
header("Location: index.php");
exit(); // Toujours ajouter exit() après une redirection
?>