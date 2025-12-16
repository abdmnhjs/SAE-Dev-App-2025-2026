<?php
session_start();

require '../../includes/init.php';

// 1. Vérification sécurité
if($_SESSION["role"] !== "adminweb"){
    header('location: ../index.php');
    exit();
}
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../admin_panel-logs.php?error=not_a_post_request");
    exit();
}

// 2. Récupération du nom (correspond bien à votre formulaire HTML name='manufacturer_name')
$manufacturerName = isset($_POST['manufacturer_name']) ? trim($_POST['manufacturer_name']) : '';

// 3. Si c'est vide, on rejette (pas de log nécessaire ici, ou alors un log d'erreur)
if (empty($manufacturerName)) {
    header("Location: ../admin_panel-logs.php?error=empty_name");
    exit();
}

// 4. Insertion du fabricant
$query = "INSERT INTO manufacturer_list (name) VALUES (?)";
$stmt = mysqli_prepare($loginToDb, $query);

if($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $manufacturerName);

    // Si l'exécution réussit :
    if (mysqli_stmt_execute($stmt)) {

        // ==========================================
        // C'EST ICI QU'IL FAUT METTRE LE CODE DE LOG
        // ==========================================

        $usernameAdmin = $_SESSION['username'];
        $logDescription = $usernameAdmin . " a ajouté le fabricant nommé " . $manufacturerName;
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $queryLog = "INSERT INTO logs (username, description, ip_address) VALUES (?, ?, ?)";
        $stmtLog = mysqli_prepare($loginToDb, $queryLog);

        if ($stmtLog) {
            // Attention : j'ai corrigé $username en $usernameAdmin ici aussi
            mysqli_stmt_bind_param($stmtLog, "sss", $usernameAdmin, $logDescription, $ip_address);
            mysqli_stmt_execute($stmtLog);
            mysqli_stmt_close($stmtLog);
        }

        // FIN DU LOG, on redirige vers le succès
        header("Location: ../admin_panel-logs.php?success=manufacturer_added");
        exit();

    } else {
        // En cas d'erreur SQL (ex: le fabricant existe déjà)
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