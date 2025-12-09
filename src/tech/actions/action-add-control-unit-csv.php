<?php
session_start();
require '../../includes/init.php';
ensureUserAuthorized("tech");

if (!isset($_FILES["control-units-csv"])) {
    header("Location: ../tech-panel.php?error=no_file");
    exit;
}

$csvFile = $_FILES["control-units-csv"]["tmp_name"];

if (!file_exists($csvFile)) {
    header("Location: ../tech-panel.php?error=file_not_found");
    exit;
}

$fp = fopen($csvFile, "r");

if (!$fp) {
    header("Location: ../tech-panel.php?error=cannot_open_file");
    exit;
}

// Ignorer la ligne d'en-tête
$header = fgetcsv($fp, 1024, ",");

$successCount = 0;
$errorCount = 0;

while (($result = fgetcsv($fp, 1024, ",")) !== false) {
    // Vérifier que la ligne contient 16 colonnes
    if (count($result) < 16) {
        $errorCount++;
        continue;
    }

    // Extraire les données du CSV (ordre : NAME,SERIAL,MANUFACTURER,MODEL,TYPE,CPU,RAM_MB,DISK_GB,OS,DOMAIN,LOCATION,BUILDING,ROOM,MACADDR,PURCHASE_DATE,WARRANTY_END)
    $name = trim($result[0]);
    $serial = trim($result[1]);
    $manufacturerName = trim($result[2]);
    $model = trim($result[3]);
    $type = trim($result[4]);
    $cpu = trim($result[5]);
    // Conversion en entier pour les champs numériques (important pour 'i' dans bind_param)
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

    // Ignorer les lignes vides
    if (empty($serial)) {
        continue;
    }

    // Récupérer l'ID du fabricant depuis la table manufacturer_list
    $queryManufacturer = "SELECT id FROM manufacturer_list WHERE name = ?";
    $stmtManufacturer = mysqli_prepare($loginToDb, $queryManufacturer);

    $idManufacturer = NULL;
    if ($stmtManufacturer) {
        mysqli_stmt_bind_param($stmtManufacturer, "s", $manufacturerName);
        mysqli_stmt_execute($stmtManufacturer);
        $resultManufacturer = mysqli_stmt_get_result($stmtManufacturer);

        if ($row = mysqli_fetch_assoc($resultManufacturer)) {
            $idManufacturer = $row['id'];
        } else {
            // Si le fabricant n'existe pas, l'ajouter
            $insertManufacturer = "INSERT INTO manufacturer_list (name) VALUES (?)";
            $stmtInsertManufacturer = mysqli_prepare($loginToDb, $insertManufacturer);

            if ($stmtInsertManufacturer) {
                mysqli_stmt_bind_param($stmtInsertManufacturer, "s", $manufacturerName);
                if (mysqli_stmt_execute($stmtInsertManufacturer)) {
                    $idManufacturer = mysqli_insert_id($loginToDb);
                }
                mysqli_stmt_close($stmtInsertManufacturer);
            }
        }

        mysqli_stmt_close($stmtManufacturer);
    }

    // Récupérer l'ID de l'OS depuis la table os_list
    $queryOs = "SELECT id FROM os_list WHERE name = ?";
    $stmtOs = mysqli_prepare($loginToDb, $queryOs);

    $idOs = NULL;
    if ($stmtOs) {
        mysqli_stmt_bind_param($stmtOs, "s", $osName);
        mysqli_stmt_execute($stmtOs);
        $resultOs = mysqli_stmt_get_result($stmtOs);

        if ($row = mysqli_fetch_assoc($resultOs)) {
            $idOs = $row['id'];
        } else {
            // Si l'OS n'existe pas, l'ajouter
            $insertOs = "INSERT INTO os_list (name) VALUES (?)";
            $stmtInsertOs = mysqli_prepare($loginToDb, $insertOs);

            if ($stmtInsertOs) {
                mysqli_stmt_bind_param($stmtInsertOs, "s", $osName);
                if (mysqli_stmt_execute($stmtInsertOs)) {
                    $idOs = mysqli_insert_id($loginToDb);
                }
                mysqli_stmt_close($stmtInsertOs);
            }
        }

        mysqli_stmt_close($stmtOs);
    }

    // Insérer l'unité de contrôle
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
        // CORRECTION DE LA CHAÎNE DE TYPES : "ssisssiississsss" (16 caractères pour 16 variables)
        mysqli_stmt_bind_param($stmt, "ssisssiississsss",
            $name, $serial, $idManufacturer,
            $model, $type, $cpu, $ramMb,
            $diskGb, $idOs, $domain,
            $location, $building, $room,
            $macaddr, $purchaseDate, $warrantyEnd
        );

        if (mysqli_stmt_execute($stmt)) {
            $successCount++;
            header("Location: ../tech-panel.php?section=control-units");
        } else {
            $errorCount++;
            // Ajout du code d'erreur SQL pour un meilleur débogage
            error_log("Erreur insertion: " . mysqli_error($loginToDb) . " - Serial: " . $serial . " - Code: " . mysqli_errno($loginToDb));
        }

        mysqli_stmt_close($stmt);
    } else {
        $errorCount++;
        error_log("Erreur de préparation: " . mysqli_error($loginToDb));
    }
}

fclose($fp);
mysqli_close($loginToDb);

// Redirection avec message de résultat
if ($errorCount == 0) {
    header("Location: ../tech-panel.php?section=control-units&success=import_complete&count=" . $successCount);
} else {
    header("Location: ../tech-panel.php?section=control-units&warning=import_partial&success=" . $successCount . "&errors=" . $errorCount);
}
exit;
?>