<?php
session_start();
require '../includes/init.php';
if($_SESSION["role"] !== "tech"){
    header('location: ../index.php');
    exit();
}


$serial = isset($_GET['delete']) ? trim($_GET['delete']) : null;

if ($serial) {
    $query = "DELETE FROM central_unit WHERE serial = ?";
    $stmt = mysqli_prepare($loginToDb, $query);
    mysqli_stmt_bind_param($stmt, "s", $serial);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Log de l'action de suppression d'unité centrale
    $usernameTech = $_SESSION['username'] ?? 'inconnu';
    $logDescription = $usernameTech . " a supprimé l'unité centrale avec le numéro de série " . $serial;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';

    $queryLog = "INSERT INTO logs (username, description, ip_address) VALUES (?, ?, ?)";
    $stmtLog = mysqli_prepare($loginToDb, $queryLog);
    if ($stmtLog) {
        mysqli_stmt_bind_param($stmtLog, "sss", $usernameTech, $logDescription, $ip_address);
        mysqli_stmt_execute($stmtLog);
        mysqli_stmt_close($stmtLog);
    }
}

header("Location: tech-panel.php?section=central-units");
exit;
