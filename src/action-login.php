<?php
session_start();

require "includes/connexion_bdd.php";

// 1. Initialisation du timer pour les logs (C'est très bien)
if (!isset($_SESSION['session_start_time'])) {
    $_SESSION['session_start_time'] = time();
}

$username = $_POST["username"];
$password = $_POST["password"];

$queryRole = "SELECT role FROM users WHERE name = ? AND mdp = ?";
$stmt = mysqli_prepare($loginToDb, $queryRole);

if($stmt){
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // --- CORRECTION ICI ---
    // On vérifie s'il y a au moins 1 résultat
    if(mysqli_num_rows($result) > 0){

        // On récupère les données sous forme de tableau associatif
        $row = mysqli_fetch_assoc($result);

        $role = $row["role"];

        // On stocke le rôle ET le nom d'utilisateur (crucial pour tes logs plus tard !)
        $_SESSION["role"] = $role;
        $_SESSION["username"] = $username; // AJOUTÉ : sinon tu ne sauras pas qui loguer

        mysqli_stmt_close($stmt);
        mysqli_close($loginToDb);

        // Redirection selon le rôle
        if($role === "adminweb"){
            header('Location: admin/create-tech-form.php');
        } else if ($role === "tech") {
            header('Location: tech/tech-panel.php?section=screens');
        } else {
            // Cas de sécurité : si le rôle n'est ni admin ni tech
            header("Location: connexion.php?error=role_inconnu");
        }
        exit();

    } else {
        // Mauvais login ou mot de passe
        mysqli_stmt_close($stmt);
        mysqli_close($loginToDb);
        header("Location: connexion.php?error=1");
        exit();
    }
} else {
    die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
}
?>