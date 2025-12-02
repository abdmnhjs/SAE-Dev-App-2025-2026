<?php
session_start();

// Vérifier que l'utilisateur est admin
if (!isset($_SESSION['username']) || $_SESSION['username'] === "adminweb" || $_SESSION['username'] === "sysadmin") {
    header("Location: ../tech-panel.php?error=unauthorized");
    exit();
}

$host = 'localhost';
$user = 'root';
$db_password = "!sae2025!"; // penser à le changer si vous faites des tests en locaux
$db = "infra";

$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if (!$loginToDb) {
    die("Erreur de connexion à la db: " . mysqli_connect_error());
}