<?php
session_start();
require '../../includes/init.php';
if($_SESSION["role"] !== "adminweb"){
    header('location: ../index.php');
    exit();
}


// Récupérer et valider les données
$username = isset($_POST["username"]) ? trim($_POST["username"]) : '';
$password = isset($_POST["password"]) ? trim($_POST["password"]) : '';

// Validation des champs
if (empty($username) || empty($password)) {
    mysqli_close($loginToDb);
    header("Location: ../admin_panel-logs.php?error=empty_fields");
    exit();
}

// Vérifier si l'utilisateur existe déjà
$check_query = "SELECT name FROM users WHERE name = ?";
$check_stmt = mysqli_prepare($loginToDb, $check_query);

if ($check_stmt) {
    mysqli_stmt_bind_param($check_stmt, "s", $username);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        mysqli_stmt_close($check_stmt);
        mysqli_close($loginToDb);
        header("Location: ../admin_panel-logs.php?error=user_exists");
        exit();
    }
    mysqli_stmt_close($check_stmt);
}

// Insérer le nouvel utilisateur
$query = "INSERT INTO users (name, mdp) VALUES (?, ?)";
$stmt = mysqli_prepare($loginToDb, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);

    if (mysqli_stmt_execute($stmt)) {
        // --- GESTION DES LOGS (Centralisée) ---
        // On prépare la description et l'IP

        $usernameAdmin = $_SESSION['username'];
        $logDescription = $usernameAdmin . " a ajouté le technicien nommé " . $username;
        $ip_address = $_SERVER['REMOTE_ADDR'];

        // On utilise une requête préparée pour les logs aussi (plus propre/sûr)
        $queryLog = "INSERT INTO logs (username, description, ip_address) VALUES (?, ?, ?)";
        $stmtLog = mysqli_prepare($loginToDb, $queryLog);

        if ($stmtLog) {
            mysqli_stmt_bind_param($stmtLog, "sss", $username, $logDescription, $ip_address);
            mysqli_stmt_execute($stmtLog);
            mysqli_stmt_close($stmtLog);
        }
        mysqli_stmt_close($stmt);
        mysqli_close($loginToDb);
        header("Location: ../create-tech-form.php");
        exit();
    } else {
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($loginToDb);
        error_log("Erreur SQL: " . $error);
        header("Location: ../admin_panel-logs.php?error=sql_error&details=" . urlencode($error));
        exit();
    }
} else {
    $error = mysqli_error($loginToDb);
    mysqli_close($loginToDb);
    error_log("Erreur préparation: " . $error);
    header("Location: ../admin_panel-logs.php?error=prepare_failed&details=" . urlencode($error));
    exit();
}
?>