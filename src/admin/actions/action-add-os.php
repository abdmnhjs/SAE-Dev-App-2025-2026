<?php
session_start();

require '../../includes/init.php';

// 1. Vérification du rôle
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "adminweb") {
    header('location: ../index.php');
    exit();
}

// 2. Vérification de la méthode POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../admin_panel-logs.php?error=not_a_post_request");
    exit();
}

// 3. Récupération de la donnée
// CORRECTION : On utilise 'os_name' comme défini dans votre formulaire HTML
$osName = isset($_POST['os_name']) ? trim($_POST['os_name']) : '';

// 4. Vérifier si le champ est vide
if (empty($osName)) {
    header("Location: ../admin_panel-logs.php?error=empty_name");
    exit();
}

// 5. Préparation de l'insertion (CORRECTION : Table os_list)
$query = "INSERT INTO os_list (name) VALUES (?)";
$stmt = mysqli_prepare($loginToDb, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $osName);

    // Exécution de l'insertion
    if (mysqli_stmt_execute($stmt)) {

        // --- SUCCÈS : GESTION DES LOGS ---
        $usernameAdmin = $_SESSION['username'];
        // On change la description pour dire qu'on a ajouté un OS
        $logDescription = $usernameAdmin . " a ajouté le système d'exploitation : " . $osName;
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $queryLog = "INSERT INTO logs (username, description, ip_address) VALUES (?, ?, ?)";
        $stmtLog = mysqli_prepare($loginToDb, $queryLog);

        if ($stmtLog) {
            mysqli_stmt_bind_param($stmtLog, "sss", $usernameAdmin, $logDescription, $ip_address);
            mysqli_stmt_execute($stmtLog);
            mysqli_stmt_close($stmtLog);
        }

        // Redirection succès
        header("Location: ../admin_panel-logs.php?success=os_added");
        exit();

    } else {
        // En cas d'échec (ex: l'OS existe déjà)
        $error_message = urlencode(mysqli_stmt_error($stmt) ?: "fail");
        header("Location: ../admin_panel-logs.php?error=db_fail:$error_message");
        exit();
    }

    mysqli_stmt_close($stmt);
} else {
    die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
}

mysqli_close($loginToDb);
?>