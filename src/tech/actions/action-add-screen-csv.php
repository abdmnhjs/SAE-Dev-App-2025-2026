<?php
session_start();
// Activation des rapports d'erreurs stricts pour MySQLi (Try/Catch)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require '../../includes/init.php';

if($_SESSION["role"] !== "tech"){
    header('location: ../index.php');
    exit();
}

// 1. Validation du fichier
if (empty($_FILES["screens-csv"]) || $_FILES["screens-csv"]["error"] !== UPLOAD_ERR_OK) {
    header("Location: ../tech-panel.php?section=screens");
    exit;
}

$uploadedFilePath = $_FILES["screens-csv"]["tmp_name"];

if (!file_exists($uploadedFilePath)) {
    header("Location: ../tech-panel.php?section=screens");
    exit;
}

$fp = fopen($uploadedFilePath, "r");
if (!$fp) {
    header("Location: ../tech-panel.php?section=screens");
    exit;
}

// Ignorer l'en-tête
$header = fgetcsv($fp, 1024, ",");

// Compteurs
$successCount = 0;
$errorCount = 0;
$duplicateCount = 0;
$errorsDetails = [];

// --- PRÉPARATION DES REQUÊTES (Optimisation) ---

// A. Gestion Fabricant (Récupérer ou Créer)
$queryManuf = "SELECT id FROM manufacturer_list WHERE name = ?";
$stmtManuf = mysqli_prepare($loginToDb, $queryManuf);
$insertManuf = "INSERT INTO manufacturer_list (name) VALUES (?)";
$stmtInsertManuf = mysqli_prepare($loginToDb, $insertManuf);

// B. Vérification Unité Centrale (Attached_to)
// On vérifie si le nom de l'ordinateur existe vraiment
$queryUnit = "SELECT name FROM control_unit WHERE name = ?";
$stmtUnit = mysqli_prepare($loginToDb, $queryUnit);

// C. Insertion Écran
$insertQuery = "INSERT INTO screen (serial, id_manufacturer, model, 
                      size_inch, resolution, connector, 
                      attached_to)                          
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmtInsert = mysqli_prepare($loginToDb, $insertQuery);


// 3. Boucle et Exécution
while (($result = fgetcsv($fp, 1024, ",")) !== FALSE) {

    // Vérification basique des colonnes (7 colonnes attendues)
    if (count($result) < 7) {
        $errorCount++;
        continue;
    }

    // Nettoyage des entrées
    $serial = trim($result[0]);
    $manufacturerName = trim($result[1]);
    $model = trim($result[2]);
    $sizeInch = trim($result[3]);
    $resolution = trim($result[4]);
    $connector = trim($result[5]);
    $attachedToName  = trim($result[6]);

    // Skip si pas de numéro de série
    if (empty($serial)) {
        continue;
    }

    // --- 1. GESTION FABRICANT (Auto-création) ---
    $manufacturerId = null;
    mysqli_stmt_bind_param($stmtManuf, "s", $manufacturerName);
    mysqli_stmt_execute($stmtManuf);
    $resMan = mysqli_stmt_get_result($stmtManuf);

    if ($row = mysqli_fetch_assoc($resMan)) {
        $manufacturerId = $row['id'];
    } else {
        // Le fabricant n'existe pas, on le crée
        mysqli_stmt_bind_param($stmtInsertManuf, "s", $manufacturerName);
        if (mysqli_stmt_execute($stmtInsertManuf)) {
            $manufacturerId = mysqli_insert_id($loginToDb);
        }
    }

    // --- 2. GESTION LIAISON UNITÉ (Attached To) ---
    $finalAttachedTo = NULL;

    if (!empty($attachedToName)) {
        mysqli_stmt_bind_param($stmtUnit, "s", $attachedToName);
        mysqli_stmt_execute($stmtUnit);
        $resUnit = mysqli_stmt_get_result($stmtUnit);

        if ($rowUnit = mysqli_fetch_assoc($resUnit)) {
            // L'unité existe, on valide le lien
            $finalAttachedTo = $attachedToName;
        } else {
            // L'unité n'existe pas dans la base
            // CHOIX : On rejette la ligne OU on importe sans lier ?
            // Ici, on compte une erreur car lier un écran à un PC fantôme est risqué
            $errorCount++;
            $errorsDetails[] = "Serial $serial : Unité '$attachedToName' inconnue.";
            continue; // On passe à la ligne suivante du CSV
        }
    }

    // --- 3. INSERTION ÉCRAN ---
    mysqli_stmt_bind_param($stmtInsert, "sisdsss",
        $serial, $manufacturerId, $model,
        $sizeInch, $resolution, $connector,
        $finalAttachedTo
    );

    try {
        mysqli_stmt_execute($stmtInsert);
        $successCount++;
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            // Doublon (Serial déjà existant)
            $duplicateCount++;
        } else {
            // Autre erreur SQL
            $errorCount++;
            $errorsDetails[] = "Erreur SQL Serial $serial : " . $e->getMessage();
        }
    }
}

// Fermeture des ressources
fclose($fp);
mysqli_stmt_close($stmtInsert);
mysqli_stmt_close($stmtManuf);
mysqli_stmt_close($stmtInsertManuf);
mysqli_stmt_close($stmtUnit);

// --- LOGS ---
if ($successCount > 0) {
    $usernameTech = $_SESSION['username'];
    // Correction ici : Utilisation de $usernameTech et non $username
    $logDescription = "$usernameTech a importé des écrans (CSV). Succès: $successCount, Doublons: $duplicateCount, Erreurs: $errorCount";
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

// --- REDIRECTION ---
// On redirige avec les détails
header("Location: ../tech-panel.php?section=screens&success=import_done&added=$successCount&dupes=$duplicateCount&errors=$errorCount");
exit;
?>