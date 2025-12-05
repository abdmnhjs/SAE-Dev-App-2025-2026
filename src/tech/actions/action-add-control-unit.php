<?php
session_start();

// Vérifier que l'utilisateur est autorisé (Technicien, pas Admin)
if (!isset($_SESSION['username']) || $_SESSION['username'] === "adminweb" || $_SESSION['username'] === "sysadmin") {
    header("Location: ../tech-panel.php?error=unauthorized");
    exit();
}

$host = 'localhost';
$user = 'root';
$db_password = ""; // À changer pour les tests en local
$db = "infra";

$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if (!$loginToDb) {
    die("Erreur de connexion à la db: " . mysqli_connect_error());
}

// --- Validation et Nettoyage des données POST ---

// Validation et conversion des types
$manufacturerId = filter_input(INPUT_POST, 'manufacturer_id', FILTER_VALIDATE_INT);
$ramMb = filter_input(INPUT_POST, 'ramMb', FILTER_VALIDATE_INT);
$diskGb = filter_input(INPUT_POST, 'diskGb', FILTER_VALIDATE_INT);
$osId = filter_input(INPUT_POST, 'os_id', FILTER_VALIDATE_INT);

// Nettoyage des chaînes
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$serial = isset($_POST['serial']) ? trim($_POST['serial']) : '';
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

// Vérification que les champs obligatoires sont présents et valides
if (empty($serial) || empty($name) || $manufacturerId === false || $manufacturerId === null ||
    empty($model) || empty($type) || empty($cpu) || $ramMb === false || $ramMb === null ||
    $diskGb === false || $diskGb === null || $osId === false || $osId === null ||
    empty($location) || empty($building) || empty($room) || empty($macaddr) ||
    empty($purchaseDate) || empty($warrantyEnd)) {

    header("Location: ../tech-panel.php?error=missing_or_invalid_fields");
    mysqli_close($loginToDb);
    exit();
}

// --- Requête Préparée pour l'Insertion ---

// Correction: Remplacement de 'manufacturer' et 'os' par 'id_manufacturer' et 'id_os'
$query = "INSERT INTO control_unit (name, serial, id_manufacturer, 
                          model, type, cpu, ram_mb, 
                          disk_gb, id_os, domain, 
                          location, building, room, 
                          macaddr, purchase_date, warranty_end)
                          
                          VALUES (?, ?, ?,
                                  ?, ?, ?, ?,
                                  ?, ?, ?, 
                                  ?, ?, ?,
                                  ?, ?, ?)";

$stmt = mysqli_prepare($loginToDb, $query);

if ($stmt) {
    // Types : s(string), i(integer)
    // L'ancienne chaîne : "ssisssiiissssss" (15 types)
    // La chaîne correcte : "ssisssiiisssssss" (16 types pour 16 variables)
    mysqli_stmt_bind_param($stmt, "ssisssiiisssssss",
        $name,           // s
        $serial,         // s
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
        $warrantyEnd     // s
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../tech-panel.php?section=control-units&success=unit_added");
        exit();
    } else {
        // Ajout d'une information d'erreur plus précise (pour le debug)
        $error = urlencode(mysqli_stmt_error($stmt));
        header("Location: ../tech-panel.php?error=unit_insert_failed:$error");
        exit();
    }

    mysqli_stmt_close($stmt);
} else {
    // En cas d'échec de la préparation (ex: erreur de syntaxe SQL)
    die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
}

mysqli_close($loginToDb);
?>