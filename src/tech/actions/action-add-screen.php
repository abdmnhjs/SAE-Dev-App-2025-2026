<?php
session_start();
require '../../includes/init.php';
ensureUserAuthorized("tech");
// Vérifier que l'utilisateur est admin
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