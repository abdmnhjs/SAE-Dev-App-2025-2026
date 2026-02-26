<?php
session_start();
require '../../includes/init.php';
if ($_SESSION["role"] !== "tech") {
    header('location: ../../index.php');
    exit();
}

$type = isset($_GET['type']) ? $_GET['type'] : '';
$ref = isset($_GET['ref']) ? trim($_GET['ref']) : '';

if ($ref && ($type === 'screen' || $type === 'central_unit')) {
    if ($type === 'screen') {
        $stmt = mysqli_prepare($loginToDb, "UPDATE screen SET rebut_date = NULL, is_active = TRUE WHERE serial = ?");
        mysqli_stmt_bind_param($stmt, "s", $ref);
    } else {
        $stmt = mysqli_prepare($loginToDb, "UPDATE central_unit SET rebut_date = NULL, is_active = TRUE WHERE name = ?");
        mysqli_stmt_bind_param($stmt, "s", $ref);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Log de la remise en service
    $usernameTech = $_SESSION['username'] ?? 'inconnu';
    if ($type === 'screen') {
        $logDescription = $usernameTech . " a remis en service l'écran au numéro de série " . $ref;
    } else {
        $logDescription = $usernameTech . " a remis en service l'unité centrale " . $ref;
    }
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';

    $queryLog = "INSERT INTO logs (username, description, ip_address) VALUES (?, ?, ?)";
    $stmtLog = mysqli_prepare($loginToDb, $queryLog);
    if ($stmtLog) {
        mysqli_stmt_bind_param($stmtLog, "sss", $usernameTech, $logDescription, $ip_address);
        mysqli_stmt_execute($stmtLog);
        mysqli_stmt_close($stmtLog);
    }
}

header("Location: ../tech-panel.php?section=rebut&success=remis_en_service");
exit;
