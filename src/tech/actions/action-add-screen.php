<?php
session_start();

// Vérifier que l'utilisateur est admin
if (!isset($_SESSION['username']) || $_SESSION['username'] === "adminweb" || $_SESSION['username'] === "sysadmin") {
    header("Location: ../tech-panel.php?error=unauthorized");
    exit();
}

$host = 'localhost';
$user = 'root';
$db_password = ""; // penser à le changer si vous faites des tests en locaux
$db = "infra";

$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if (!$loginToDb) {
    die("Erreur de connexion à la db: " . mysqli_connect_error());
}

$serial = $_POST['serial'];
$manufacturer = $_POST['manufacturer'];
$model = $_POST['model'];
$sizeInch = $_POST['sizeInch'];
$resolution = $_POST['resolution'];
$connector = $_POST['connector'];
$attachedTo = $_POST['attachedTo'];

$query = "INSERT INTO screen (serial, manufacturer, model, 
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