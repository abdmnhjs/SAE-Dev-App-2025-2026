<?php
session_start();

require "includes/connexion_bdd.php";
require "includes/password_crypto.php";

if (!isset($_SESSION['session_start_time'])) {
    $_SESSION['session_start_time'] = time();
}

$username = $_POST["username"];
$password = $_POST["password"];

// Récupérer l'utilisateur par nom puis vérifier le mdp (chiffré ChaCha20 ou legacy clair)
$queryUser = "SELECT name, mdp, role FROM users WHERE name = ?";
$stmt = mysqli_prepare($loginToDb, $queryUser);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (!password_verify_plain($password, $row["mdp"])) {
            mysqli_stmt_close($stmt);
            mysqli_close($loginToDb);
            header("Location: connexion.php?error=1");
            exit();
        }
        $role = $row["role"];

        // Initialisation de la session
        $_SESSION["role"] = $role;
        $_SESSION["username"] = $username;

        // Fermeture du statement de login (mais PAS de la connexion BDD globale)
        mysqli_stmt_close($stmt);

        // --- GESTION DES LOGS (Centralisée) ---
        // On prépare la description et l'IP
        $logDescription = $username . " s'est connecté en tant que " . $role;
        $ip_address = $_SERVER['REMOTE_ADDR'];

        // On utilise une requête préparée pour les logs aussi (plus propre/sûr)
        $queryLog = "INSERT INTO logs (username, description, ip_address) VALUES (?, ?, ?)";
        $stmtLog = mysqli_prepare($loginToDb, $queryLog);

        if ($stmtLog) {
            mysqli_stmt_bind_param($stmtLog, "sss", $username, $logDescription, $ip_address);
            mysqli_stmt_execute($stmtLog);
            mysqli_stmt_close($stmtLog);
        }

        // --- FERMETURE DE LA BDD ---
        // C'est ICI qu'on ferme la connexion, une fois que tout est fini
        mysqli_close($loginToDb);

        // --- REDIRECTION ---
        if ($role === "adminweb") {
            header('Location: admin/create-tech-form.php');
        } else if ($role === "tech") {
            header('Location: tech/tech-panel.php?section=screens');
        } else if ($role === "sysadmin") {
            header('Location: sysadmin/logs.php');
        } else {
            // Rôle inconnu
            header("Location: connexion.php?error=role_inconnu");
        }
        exit();

    }
    // Utilisateur non trouvé
    mysqli_stmt_close($stmt);
    mysqli_close($loginToDb);
    header("Location: connexion.php?error=1");
    exit();
} else {
    die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
}
?>