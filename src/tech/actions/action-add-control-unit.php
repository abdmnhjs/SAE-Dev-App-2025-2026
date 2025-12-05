<?php
session_start();
require '../../includes/init.php';
ensureUserAuthorized("tech");



$name = $_POST['name'];
$serial = $_POST['serial'];
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
    mysqli_stmt_bind_param($stmt, "ssssssiissssssss",
        $name, $serial, $manufacturer,
        $model, $type, $cpu, $ramMb,
        $diskGb, $os, $domain,
        $location, $building, $room,
        $macaddr, $purchaseDate, $warrantyEnd
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../tech-panel.php?section=control-units");
    } else {
        header("Location: ../tech-panel.php?error=insert_failed");
    }

    mysqli_stmt_close($stmt);
} else {
    die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
}

mysqli_close($loginToDb);
?>