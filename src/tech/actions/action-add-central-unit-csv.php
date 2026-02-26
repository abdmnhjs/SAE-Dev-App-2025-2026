<?php
session_start();
// Activation du rapport d'erreurs pour mysqli (transforme les erreurs en Exceptions)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require '../../includes/init.php';

if($_SESSION["role"] !== "tech"){
    header('location: ../index.php');
    exit();
}

if (!isset($_FILES["central-units-csv"])) {
    header("Location: ../tech-panel.php?section=central-units");
    exit;
}

$csvFile = $_FILES["central-units-csv"]["tmp_name"];

if (!file_exists($csvFile)) {
    header("Location: ../tech-panel.php?section=central-units");
    exit;
}

$fp = fopen($csvFile, "r");

if (!$fp) {
    header("Location: ../tech-panel.php?section=central-units");
    exit;
}

// Ignorer la ligne d'en-tête
$header = fgetcsv($fp, 1024, ",");

$successCount = 0;
$errorCount = 0;
$duplicateCount = 0; // Nouveau compteur pour les doublons

// --- PRÉPARATION DES REQUÊTES (Optimisation : on prépare une seule fois, on exécute plusieurs fois) ---

// 1. Préparation INSERT principal
$query = "INSERT INTO central_unit (name, serial, id_manufacturer,
                      model, type, cpu, ram_mb,
                      disk_gb, id_os, domain,
                      location, building, room,
                      macaddr, purchase_date, warranty_end)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($loginToDb, $query);

// 2. Préparation Manufacturer
$queryManufacturer = "SELECT id FROM manufacturer_list WHERE name = ?";
$stmtManufacturer = mysqli_prepare($loginToDb, $queryManufacturer);
$insertManufacturer = "INSERT INTO manufacturer_list (name) VALUES (?)";
$stmtInsertManufacturer = mysqli_prepare($loginToDb, $insertManufacturer);

// 3. Préparation OS
$queryOs = "SELECT id FROM os_list WHERE name = ?";
$stmtOs = mysqli_prepare($loginToDb, $queryOs);
$insertOs = "INSERT INTO os_list (name) VALUES (?)";
$stmtInsertOs = mysqli_prepare($loginToDb, $insertOs);


while (($result = fgetcsv($fp, 1024, ",")) !== false) {
    // Vérifier que la ligne contient au moins 16 colonnes
    if (count($result) < 16) {
        $errorCount++;
        continue;
    }

    // Extraire et nettoyer les données
    $name = trim($result[0]);
    $serial = trim($result[1]);
    $manufacturerName = trim($result[2]);
    $model = trim($result[3]);
    $type = trim($result[4]);
    $cpu = trim($result[5]);
    $ramMb = (int)trim($result[6]);
    $diskGb = (int)trim($result[7]);
    $osName = trim($result[8]);
    $domain = trim($result[9]);
    $location = trim($result[10]);
    $building = trim($result[11]);
    $room = trim($result[12]);
    $macaddr = trim($result[13]);
    $purchaseDate = trim($result[14]);
    $warrantyEnd = trim($result[15]);

    // Ignorer les lignes sans numéro de série
    if (empty($serial)) {
        continue;
    }

    // --- GESTION FABRICANT ---
    $idManufacturer = NULL;
    mysqli_stmt_bind_param($stmtManufacturer, "s", $manufacturerName);
    mysqli_stmt_execute($stmtManufacturer);
    $resMan = mysqli_stmt_get_result($stmtManufacturer);

    if ($row = mysqli_fetch_assoc($resMan)) {
        $idManufacturer = $row['id'];
    } else {
        // Création nouveau fabricant
        mysqli_stmt_bind_param($stmtInsertManufacturer, "s", $manufacturerName);
        if (mysqli_stmt_execute($stmtInsertManufacturer)) {
            $idManufacturer = mysqli_insert_id($loginToDb);
        }
    }

    // --- GESTION OS ---
    $idOs = NULL;
    mysqli_stmt_bind_param($stmtOs, "s", $osName);
    mysqli_stmt_execute($stmtOs);
    $resOs = mysqli_stmt_get_result($stmtOs);

    if ($row = mysqli_fetch_assoc($resOs)) {
        $idOs = $row['id'];
    } else {
        // Création nouvel OS
        mysqli_stmt_bind_param($stmtInsertOs, "s", $osName);
        if (mysqli_stmt_execute($stmtInsertOs)) {
            $idOs = mysqli_insert_id($loginToDb);
        }
    }

    // --- INSERTION UNITÉ CENTRALE ---
    // Bind des paramètres
    mysqli_stmt_bind_param($stmt, "ssisssiississsss",
        $name, $serial, $idManufacturer,
        $model, $type, $cpu, $ramMb,
        $diskGb, $idOs, $domain,
        $location, $building, $room,
        $macaddr, $purchaseDate, $warrantyEnd
    );

    try {
        mysqli_stmt_execute($stmt);
        $successCount++;
    } catch (mysqli_sql_exception $e) {
        // Code 1062 = Duplicate entry (Doublon)
        if ($e->getCode() == 1062) {
            $duplicateCount++;
        } else {
            // Autre erreur SQL
            $errorCount++;
            error_log("Erreur Import CSV - Serial: $serial - " . $e->getMessage());
        }
    }
}

// Fermeture des ressources
fclose($fp);
mysqli_stmt_close($stmt);
mysqli_stmt_close($stmtManufacturer);
mysqli_stmt_close($stmtInsertManufacturer);
mysqli_stmt_close($stmtOs);
mysqli_stmt_close($stmtInsertOs);


// --- LOGS (Une seule fois à la fin) ---
if ($successCount > 0) {
    $usernameTech = $_SESSION['username'];
    $logDescription = "$usernameTech a importé un CSV d'unités centrales. Succès: $successCount, Doublons: $duplicateCount, Erreurs: $errorCount";
    $ip_address = $_SERVER['REMOTE_ADDR'];

    $queryLog = "INSERT INTO logs (username, description, ip_address) VALUES (?, ?, ?)";
    $stmtLog = mysqli_prepare($loginToDb, $queryLog);
    if ($stmtLog) {
        mysqli_stmt_bind_param($stmtLog, "sss", $usernameTech, $logDescription, $ip_address);
        mysqli_stmt_execute($stmtLog);
        mysqli_stmt_close($stmtLog);
    }
}

mysqli_close($loginToDb);

// --- REDIRECTION FINALE ---
$redirectUrl = "../tech-panel.php?section=central-units&success=import_done&added=$successCount&dupes=$duplicateCount&errors=$errorCount";
header("Location: " . $redirectUrl);
exit;
?>