<?php
session_start();
require '../../includes/init.php';
ensureUserAuthorized("tech");

// --- CORRECTION MAJEURE DE SÉCURITÉ ET LOGIQUE ---

// 1. Gérer le Fichier Téléchargé de Manière Sûre
if (empty($_FILES["screens-csv"]) || $_FILES["screens-csv"]["error"] !== UPLOAD_ERR_OK) {
    header("Location: ../tech-panel.php?error=no_file_uploaded");
    exit;
}

$uploadedFilePath = $_FILES["screens-csv"]["tmp_name"];
$errors = []; // Tableau pour stocker les erreurs par ligne

// 2. Ouvrir le fichier temporaire
if (($fp = fopen($uploadedFilePath, "r")) !== FALSE) {

    // Lire l'en-tête (pour l'ignorer)
    $header = fgetcsv($fp, 1024, ",");

    // --- PRÉPARATION DES REQUÊTES (UNE SEULE FOIS) ---

    // A. Requête d'Insertion (screen)
    $insertQuery = "INSERT INTO screen (serial, id_manufacturer, model, 
                          size_inch, resolution, connector, 
                          attached_to)                          
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = mysqli_prepare($loginToDb, $insertQuery);

    // B. Requête de Sélection (manufacturer_list)
    $selectManufQuery = "SELECT id FROM manufacturer_list WHERE name = ?";
    $stmtSelectManuf = mysqli_prepare($loginToDb, $selectManufQuery);

    // C. NOUVEAU : Requête de Sélection (control_unit) pour vérifier 'attached_to'
    // ATTENTION : On vérifie maintenant la colonne 'name'
    $selectAttachedQuery = "SELECT name FROM control_unit WHERE name = ?";
    $stmtSelectAttached = mysqli_prepare($loginToDb, $selectAttachedQuery);


    // Vérification de la préparation des requêtes
    if (!$stmtInsert || !$stmtSelectManuf || !$stmtSelectAttached) {
        // En cas d'échec de préparation, on arrête tout
        die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
    }

    $lineNumber = 1;

    // 3. Boucle et Exécution
    while (($result = fgetcsv($fp, 1024, ",")) !== FALSE) {
        $lineNumber++;

        // S'assurer qu'il y a 7 colonnes
        if (count(array_filter($result)) < 7) { // Utilisez array_filter pour compter les colonnes non vides
            $errors[] = "Ligne $lineNumber ignorée: pas assez de colonnes non vides.";
            continue;
        }

        $serial = trim($result[0]);
        $manufacturerName = trim($result[1]); // Nom du fabricant (Ex: "Dell")
        $model = trim($result[2]);
        $sizeInch = trim($result[3]);
        $resolution = trim($result[4]);
        $connector = trim($result[5]);
        $attachedToName  = trim($result[6]); // Nom de l'unité de contrôle (Ex: "PC-Admin-01")

        // --- 1. Récupération de l'ID du Fabricant ---
        $manufacturerId = null;

        mysqli_stmt_bind_param($stmtSelectManuf, "s", $manufacturerName);
        mysqli_stmt_execute($stmtSelectManuf);

        // Correction de synchronisation
        mysqli_stmt_store_result($stmtSelectManuf);

        mysqli_stmt_bind_result($stmtSelectManuf, $fetchedId);

        if (mysqli_stmt_fetch($stmtSelectManuf)) {
            $manufacturerId = $fetchedId;
        } else {
            $errors[] = "Échec à la ligne $lineNumber : Fabricant **'".$manufacturerName."'** non trouvé dans la table de référence.";
            mysqli_stmt_free_result($stmtSelectManuf);
            continue;
        }
        mysqli_stmt_free_result($stmtSelectManuf);

        // --- 2. Vérification de l'Unité de Contrôle Attachée (attached_to) par NOM ---

        $attachedSerialExists = false;
        $finalAttachedTo = $attachedToName;

        if (!empty($attachedToName)) {
            // Lier le NOM de l'unité à la requête SELECT
            mysqli_stmt_bind_param($stmtSelectAttached, "s", $attachedToName);
            mysqli_stmt_execute($stmtSelectAttached);

            // Correction de synchronisation
            mysqli_stmt_store_result($stmtSelectAttached);

            // Vérifie si un résultat a été trouvé
            if (mysqli_stmt_num_rows($stmtSelectAttached) > 0) {
                $attachedSerialExists = true;
            }

            // Libérer le résultat avant l'INSERT
            mysqli_stmt_free_result($stmtSelectAttached);

            if (!$attachedSerialExists) {
                // Si la valeur est fournie MAIS la référence n'existe pas
                $errors[] = "Échec à la ligne $lineNumber : Nom d'unité de contrôle **'".$attachedToName."'** non trouvé dans la table control_unit.";
                continue;
            }
        } else {
            // Si le champ est vide dans le CSV, le transformer en NULL pour la DB
            $finalAttachedTo = NULL;
        }


        // --- 3. Insertion dans 'screen' ---

        // Le type de liaison reste "sisdsss" car 'attached_to' est un VARCHAR (s)
        mysqli_stmt_bind_param($stmtInsert, "sisdsss",
            $serial, $manufacturerId, $model,
            $sizeInch, $resolution, $connector,
            $finalAttachedTo // Utilisation du Nom vérifié (NULL ou Nom valide)
        );

        if (!mysqli_stmt_execute($stmtInsert)) {
            $errors[] = "Échec de l'insertion à la ligne $lineNumber: " . mysqli_stmt_error($stmtInsert);
        }
    }

    // 4. Fermeture des ressources
    mysqli_stmt_close($stmtInsert);
    mysqli_stmt_close($stmtSelectManuf);
    mysqli_stmt_close($stmtSelectAttached);
    fclose($fp);

} else {
    $errors[] = "Impossible d'ouvrir le fichier CSV temporaire.";
}


// 5. Redirection Finale Unique
if (empty($errors)) {
    // Succès total
    header("Location: ../tech-panel.php?section=screens&message=import_success");
} else {
    // Succès partiel ou échec total, affiche les erreurs
    $_SESSION['import_errors'] = $errors;
    header("Location: ../tech-panel.php?error=screen_insert_failed&details=errors_in_session");
}
exit; // Toujours mettre exit après une redirection
?>