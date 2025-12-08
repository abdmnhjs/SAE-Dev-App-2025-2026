<?php
session_start();
require '../../includes/init.php';
ensureUserAuthorized("tech");

$screensCsv = $_POST["screens-csv"];
$fp = fopen($screensCsv, "r");
while ($result = fgetcsv($fp,1024,",")) {
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
            header("Location: ../tech-panel.php?section=screens");
        } else {
            header("Location: ../tech-panel.php?error=screen_insert_failed");
        }

        mysqli_stmt_close($stmt);
    } else {
        die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
    }

    mysqli_close($loginToDb);
}
fclose($fp);

