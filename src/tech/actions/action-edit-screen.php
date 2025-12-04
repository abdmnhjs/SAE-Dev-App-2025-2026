<?php
session_start();

// Vérifier que l'utilisateur est admin (correction de la logique)
if (!isset($_SESSION['username']) || $_SESSION['username'] === "adminweb" || $_SESSION['username'] === "sysadmin" || !isset($_GET['serial'])) {
    header("Location: ../tech-panel.php?error=unauthorized");
    exit();
}


    $host = 'localhost';
    $user = 'root';
    $db_password = ""; // penser à le changer si vous faites des tests en locaux
    $db = "infra";
    $serial = $_GET['serial'];

    $loginToDb = mysqli_connect($host, $user, $db_password, $db);

    if (!$loginToDb) {
        die("Erreur de connexion à la db: " . mysqli_connect_error());
    }

    // Vérifier que tous les champs POST existent
    if(isset($_POST['manufacturer'], $_POST['model'], $_POST['sizeInch'],
        $_POST['resolution'], $_POST['connector'], $_POST['attachedTo'])) {

        $manufacturer = $_POST['manufacturer'];
        $model = $_POST['model'];
        $sizeInch = $_POST['sizeInch'];
        $resolution = $_POST['resolution'];
        $connector = $_POST['connector'];
        $attachedTo = $_POST['attachedTo'];

        $query = "UPDATE screen SET 
                manufacturer = ?, 
                model = ?, 
                size_inch = ?, 
                resolution = ?, 
                connector = ?, 
                attached_to = ?
              WHERE serial = ?";

        $stmt = mysqli_prepare($loginToDb, $query);

        if ($stmt) {
            // Correction: suppression de l'espace dans "ssdsss s"
            mysqli_stmt_bind_param($stmt, "ssdssss",
                $manufacturer, $model,
                $sizeInch, $resolution, $connector,
                $attachedTo, $serial
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: ../tech-panel.php?section=screens&success=1");
                exit(); // Important: toujours exit après un header redirect
            } else {
                header("Location: ../tech-panel.php?error=screen_update_failed");
                exit();
            }

            mysqli_stmt_close($stmt);
        } else {
            die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
        }
    } else {
        header("Location: ../tech-panel.php?error=missing_fields");
        exit();
    }
mysqli_close($loginToDb);



?>