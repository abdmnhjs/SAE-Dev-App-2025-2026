<?php
session_start();
require '../../../includes/init.php';
if($_SESSION["role"] !== "adminweb"){
    header('location: ../index.php');
    exit();
}

if(isset($_POST["os_id"])){
    $selectedOsId = (int)$_POST["os_id"];

    $queryName = "SELECT name FROM os_list WHERE id = " . $selectedOsId;
    $resultName = mysqli_query($loginToDb, $queryName);
    $osName = ($row = mysqli_fetch_assoc($resultName)) ? $row['name'] : "Inconnu";

    $queryCountSpecific = "SELECT COUNT(*) as count FROM central_unit WHERE id_os = " . $selectedOsId;
    $resultSpecific = mysqli_query($loginToDb, $queryCountSpecific);
    $dataSpecific = mysqli_fetch_assoc($resultSpecific);
    $countSpecific = (int)$dataSpecific['count'];

    $queryCountTotal = "SELECT COUNT(*) as count FROM central_unit";
    $resultTotal = mysqli_query($loginToDb, $queryCountTotal);
    $dataTotal = mysqli_fetch_assoc($resultTotal);
    $countTotal = (int)$dataTotal['count'];

    $percentResult = ($countTotal > 0) ? round(($countSpecific / $countTotal) * 100, 2) : 0;

    header("Location: ../../stats.php?os-name=" . $osName . "&percent-os=" . $percentResult);
    exit();
}

if(isset($_POST["manufacturer_id"])){
    $selectedManufacturerId = (int)$_POST["manufacturer_id"];

    $queryName = "SELECT name FROM manufacturer_list WHERE id = " . $selectedManufacturerId;
    $resultName = mysqli_query($loginToDb, $queryName);
    $manufacturerName = ($row = mysqli_fetch_assoc($resultName)) ? $row['name'] : "Inconnu";

    $queryCountSpecific = "SELECT COUNT(*) as count FROM screen WHERE id_manufacturer = " . $selectedManufacturerId;
    $resultSpecific = mysqli_query($loginToDb, $queryCountSpecific);
    $dataSpecific = mysqli_fetch_assoc($resultSpecific);
    $countSpecific = (int)$dataSpecific['count'];

    $queryCountTotal = "SELECT COUNT(*) as count FROM screen";
    $resultTotal = mysqli_query($loginToDb, $queryCountTotal);
    $dataTotal = mysqli_fetch_assoc($resultTotal);
    $countTotal = (int)$dataTotal['count'];

    $percentResult = ($countTotal > 0) ? round(($countSpecific / $countTotal) * 100, 2) : 0;

    header("Location: ../../stats.php?manufacturer-name=" . $manufacturerName . "&percent-manufacturer=" . $percentResult);
    exit();
}