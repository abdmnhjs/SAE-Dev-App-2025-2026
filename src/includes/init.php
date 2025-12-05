<?php
// INITIALISATION GENERALE

//connexion à la bdd.
require __DIR__ . '/connexion_bdd.php';

//message d'erreur propre
function stopWithError($message) {
    echo "<p style='color:red;font-weight:bold;'>$message</p>";
    echo "<a href='../index.php'> retourner a l'accueil </a>";
    exit;
}


function getUserRank($username) {
    switch ($username) {
        case 'sysadmin':
            return 4;
        case 'adminweb':
            return 3;
        case 'tech1': // penser a faire une colonne roles pour les rangs pour pouvoir distingué les tech plus tard
            return 2;
        case 'tech':
            return 2;
        default:
            // tous les autres sont "tech"
            return 1;
    }
}

//méthode de vérification de rang
function ensureUserAuthorized($requiredRankName) {
    if (!isset($_SESSION['username'])) {
        stopWithError("Accès refusé : utilisateur non connecté.");
    }

    $user = $_SESSION['username'];

    $userRank     = getUserRank($user);
    $requiredRank = getUserRank($requiredRankName);

    if ($userRank < $requiredRank) {
        stopWithError("Accès refusé : privilèges insuffisants.");
    }
}

function checkStmt($stmt, $db) {
    if (!$stmt) {
        stopWithError("Erreur SQL : " . mysqli_error($db));
    }
}

