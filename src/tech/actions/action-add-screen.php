<?php
session_start();
require '../../includes/init.php';
if($_SESSION["role"] !== "tech"){
    header('location: ../index.php');
    exit();
}// Vérifier que l'utilisateur est admin
if (!isset($_SESSION['username']) ||
    !in_array($_SESSION['username'], ["adminweb", "sysadmin", "tech", "tech1"])) {
    header("Location: ../tech-panel.php?error=unauthorized");
    exit();
}



$serial = $_POST['serial'];
$manufacturer = $_POST['manufacturer'];
$model = $_POST['model'];
$sizeInch = $_POST['sizeInch'];
$resolution = $_POST['resolution'];
$connector = $_POST['connector'];
$attachedTo = $_POST['attachedTo'];

$query = "INSERT INTO screen (serial, id_manufacturer, model, 
                          size_inch, resolution, connector, 
                          attached_to)
                          
                          VALUES (?, ?, ?,
                                  ?, ?, ?,
                                  ?)";

$stmt = mysqli_prepare($loginToDb, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "sssdsss",
        $serial, $manufacturer, $model,
        $sizeInch, $resolution, $connector,
        $attachedTo
    );

    if (mysqli_stmt_execute($stmt)) {
        // --- GESTION DES LOGS (Centralisée) ---
        // On prépare la description et l'IP

        $usernameTech = $_SESSION['username'];
        $logDescription = $usernameTech . " a ajouté l'écran au numéro de série " . $serial;
        $ip_address = $_SERVER['REMOTE_ADDR'];

        // On utilise une requête préparée pour les logs aussi (plus propre/sûr)
        $queryLog = "INSERT INTO logs (username, description, ip_address) VALUES (?, ?, ?)";
        $stmtLog = mysqli_prepare($loginToDb, $queryLog);

        if ($stmtLog) {
            mysqli_stmt_bind_param($stmtLog, "sss", $username, $logDescription, $ip_address);
            mysqli_stmt_execute($stmtLog);
            mysqli_stmt_close($stmtLog);
        }
        header("Location: ../tech-panel.php?section=screens");
    } else {
        header("Location: ../tech-panel.php?error=screen_insert_failed");
    }

    mysqli_stmt_close($stmt);
} else {
    die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
}

mysqli_close($loginToDb);
?>