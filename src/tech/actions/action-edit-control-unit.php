<?php
session_start();

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

    $name = $_POST['name'];
    $manufacturer = $_POST['manufacturer'];
    $model = $_POST['model'];
    $type = $_POST['type'];
    $cpu = $_POST['cpu'];
    $ramMb = $_POST['ramMb'];
    $diskGb = $_POST['diskGb'];
    $os = $_POST['os'];
    $domain = $_POST['domain'];
    $location = $_POST['location'];
    $building = $_POST['building'];
    $room = $_POST['room'];
    $macaddr = $_POST['macaddr'];
    $purchaseDate = $_POST['purchaseDate'];
    $warrantyEnd = $_POST['warrantyEnd'];

    $query = "UPDATE control_unit SET 
            name = ?, manufacturer = ?, 
            model = ?, type = ?, cpu = ?, ram_mb = ?, 
            disk_gb = ?, os = ?, domain = ?, 
            location = ?, building = ?, room = ?, 
            macaddr = ?, purchase_date = ?, warranty_end = ?
          WHERE serial = ?";

    $stmt = mysqli_prepare($loginToDb, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssisssssssss",
            $name, $manufacturer,
            $model, $type, $cpu, $ramMb,
            $diskGb, $os, $domain,
            $location, $building, $room,
            $macaddr, $purchaseDate, $warrantyEnd,
            $serial
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../tech-panel.php?section=control-units");
        } else {
            header("Location: ../tech-panel.php?error=update_failed");
        }

        mysqli_stmt_close($stmt);
    } else {
        die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
    }

    mysqli_close($loginToDb);

?>