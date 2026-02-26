<?php
session_start();
require '../../includes/init.php';
if ($_SESSION["role"] !== "tech") {
    header('location: ../../index.php');
    exit();
}

$serial = isset($_GET['serial']) ? trim($_GET['serial']) : null;
if ($serial) {
    $stmt = mysqli_prepare($loginToDb, "UPDATE screen SET rebut_date = CURDATE(), is_active = FALSE WHERE serial = ?");
    mysqli_stmt_bind_param($stmt, "s", $serial);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Log de la mise au rebut d'un écran
    $usernameTech = $_SESSION['username'] ?? 'inconnu';
    $logDescription = $usernameTech . " a mis au rebut l'écran au numéro de série " . $serial;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';

    $queryLog = "INSERT INTO logs (username, description, ip_address) VALUES (?, ?, ?)";
    $stmtLog = mysqli_prepare($loginToDb, $queryLog);
    if ($stmtLog) {
        mysqli_stmt_bind_param($stmtLog, "sss", $usernameTech, $logDescription, $ip_address);
        mysqli_stmt_execute($stmtLog);
        mysqli_stmt_close($stmtLog);
    }
}

header("Location: ../tech-panel.php?section=screens");
exit;
