<?php
session_start();

require '../../includes/init.php';
if($_SESSION["role"] !== "tech"){
    header('location: ../index.php');
    exit();
}// Récupération et nettoyage de l'ID série de l'unité de contrôle depuis l'URL
$serial = isset($_GET['serial']) ? trim($_GET['serial']) : null;



// --- Validation et Nettoyage des données POST ---
// Utilisation de filter_input pour une validation et un nettoyage plus sûrs pour les entiers
$manufacturerId = filter_input(INPUT_POST, 'manufacturer', FILTER_VALIDATE_INT);
$ramMb = filter_input(INPUT_POST, 'ramMb', FILTER_VALIDATE_INT);
$diskGb = filter_input(INPUT_POST, 'diskGb', FILTER_VALIDATE_INT);
$osId = filter_input(INPUT_POST, 'os', FILTER_VALIDATE_INT);

// Nettoyage des chaînes de caractères
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$model = isset($_POST['model']) ? trim($_POST['model']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '';
$cpu = isset($_POST['cpu']) ? trim($_POST['cpu']) : '';
$domain = isset($_POST['domain']) ? trim($_POST['domain']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$building = isset($_POST['building']) ? trim($_POST['building']) : '';
$room = isset($_POST['room']) ? trim($_POST['room']) : '';
$macaddr = isset($_POST['macaddr']) ? trim($_POST['macaddr']) : '';
$purchaseDate = isset($_POST['purchaseDate']) ? trim($_POST['purchaseDate']) : '';
$warrantyEnd = isset($_POST['warrantyEnd']) ? trim($_POST['warrantyEnd']) : '';

// Vérification minimale que les données critiques sont valides
if ($manufacturerId === false || $ramMb === false || $diskGb === false || $osId === false ||
    empty($name) || empty($model) || empty($type) || empty($cpu) ||
    empty($location) || empty($building) || empty($room) || empty($macaddr) ||
    empty($purchaseDate) || empty($warrantyEnd)) {

    header("Location: ../tech-panel.php?error=missing_or_invalid_fields");
    mysqli_close($loginToDb);
    exit();
}

// --- Requête Préparée pour la Mise à Jour ---
$query = "UPDATE control_unit SET 
        name = ?, id_manufacturer = ?, 
        model = ?, type = ?, cpu = ?, ram_mb = ?, 
        disk_gb = ?, id_os = ?, domain = ?, 
        location = ?, building = ?, room = ?, 
        macaddr = ?, purchase_date = ?, warranty_end = ?
      WHERE serial = ?";

$stmt = mysqli_prepare($loginToDb, $query);

if ($stmt) {
    // Types de paramètres : s(string), i(integer)
    // L'ordre des types et des variables DOIT correspondre à l'ordre des '?'
    mysqli_stmt_bind_param($stmt, "sisssiiissssssss",
        $name,           // s
        $manufacturerId, // i
        $model,          // s
        $type,           // s
        $cpu,            // s
        $ramMb,          // i
        $diskGb,         // i
        $osId,           // i
        $domain,         // s
        $location,       // s
        $building,       // s
        $room,           // s
        $macaddr,        // s
        $purchaseDate,   // s
        $warrantyEnd,    // s
        $serial          // s (dans la clause WHERE)
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../tech-panel.php?section=control-units&success=unit_updated");
        exit();
    } else {
        // En cas d'échec de l'exécution (ex: violation de contrainte)
        header("Location: ../tech-panel.php?error=unit_update_failed: " . urlencode(mysqli_stmt_error($stmt)));
        exit();
    }

    mysqli_stmt_close($stmt);
} else {
    // En cas d'échec de la préparation (ex: erreur de syntaxe SQL)
    die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
}

mysqli_close($loginToDb);
?>