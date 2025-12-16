<?php
session_start();

// --- Configuration et Connexion à la Base de Données ---
require '../../includes/init.php';
if($_SESSION["role"] !== "tech"){
    header('location: ../index.php');
    exit();
}
// Récupération de l'ID série de l'écran depuis l'URL
$serial = isset($_GET['serial']) ? trim($_GET['serial']) : null;

// Vérifier l'autorisation et le paramètre 'serial'
$isAuthorized = isset($_SESSION['username']) &&
    $_SESSION['username'] !== "adminweb" &&
    $_SESSION['username'] !== "sysadmin" &&
    !empty($serial);

if (!$isAuthorized) {
    header("Location: ../tech-panel.php?error=unauthorized_or_missing_serial");
    mysqli_close($loginToDb);
    exit();
}

// --- Validation et Nettoyage des données POST ---
// Utilisation de filter_input pour une validation et un nettoyage plus sûrs
$manufacturerId = filter_input(INPUT_POST, 'manufacturer', FILTER_VALIDATE_INT);
$sizeInch = filter_input(INPUT_POST, 'sizeInch', FILTER_VALIDATE_FLOAT);

$model = isset($_POST['model']) ? trim($_POST['model']) : '';
$resolution = isset($_POST['resolution']) ? trim($_POST['resolution']) : '';
$connector = isset($_POST['connector']) ? trim($_POST['connector']) : '';
$attachedTo = isset($_POST['attachedTo']) ? trim($_POST['attachedTo']) : '';

// Validation que tous les champs requis sont présents et valides
if ($manufacturerId === false || $manufacturerId === null || $sizeInch === false || $sizeInch === null ||
    empty($model) || empty($resolution) || empty($connector) || $attachedTo === null) {

    header("Location: ../tech-panel.php?error=missing_or_invalid_fields");
    mysqli_close($loginToDb);
    exit();
}

// --- Requête Préparée pour la Mise à Jour ---
// Utilisation de requêtes préparées pour la sécurité anti-injection
$query = "UPDATE screen SET 
        id_manufacturer = ?, 
        model = ?, 
        size_inch = ?, 
        resolution = ?, 
        connector = ?, 
        attached_to = ?
      WHERE serial = ?";

$stmt = mysqli_prepare($loginToDb, $query);

if ($stmt) {
    // i = integer, s = string, d = double/decimal
    // L'ordre des types et des variables DOIT correspondre à l'ordre des '?' dans la requête
    mysqli_stmt_bind_param($stmt, "isdssss",
        $manufacturerId,
        $model,
        $sizeInch,
        $resolution,
        $connector,
        $attachedTo,
        $serial
    );

    if (mysqli_stmt_execute($stmt)) {
        // --- GESTION DES LOGS (Centralisée) ---
        // On prépare la description et l'IP

        $usernameTech = $_SESSION['username'];
        $logDescription = $usernameTech . " a modifié l'écran au numéro de série " . $serial;
        $ip_address = $_SERVER['REMOTE_ADDR'];

        // On utilise une requête préparée pour les logs aussi (plus propre/sûr)
        $queryLog = "INSERT INTO logs (username, description, ip_address) VALUES (?, ?, ?)";
        $stmtLog = mysqli_prepare($loginToDb, $queryLog);

        if ($stmtLog) {
            mysqli_stmt_bind_param($stmtLog, "sss", $username, $logDescription, $ip_address);
            mysqli_stmt_execute($stmtLog);
            mysqli_stmt_close($stmtLog);
        }
        header("Location: ../tech-panel.php?section=screens&success=screen_updated");
        exit();
    } else {
        // En cas d'échec de l'exécution (ex: clé étrangère non trouvée)
        header("Location: ../tech-panel.php?error=screen_update_failed: " . mysqli_stmt_error($stmt));
        exit();
    }

    mysqli_stmt_close($stmt);
} else {
    // En cas d'échec de la préparation (ex: erreur de syntaxe SQL)
    die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
}

mysqli_close($loginToDb);
?>