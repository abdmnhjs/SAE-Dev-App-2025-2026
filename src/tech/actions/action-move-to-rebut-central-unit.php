<?php
session_start();
require '../../includes/init.php';
if ($_SESSION["role"] !== "tech") {
    header('location: ../../index.php');
    exit();
}

$name = isset($_GET['name']) ? trim($_GET['name']) : null;
if ($name) {
    $stmt = mysqli_prepare($loginToDb, "UPDATE central_unit SET rebut_date = CURDATE(), is_active = FALSE WHERE name = ?");
    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Log de la mise au rebut d'une unité centrale
    $usernameTech = $_SESSION['username'] ?? 'inconnu';
    $logDescription = $usernameTech . " a mis au rebut l'unité centrale " . $name;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';

    $queryLog = "INSERT INTO logs (username, description, ip_address) VALUES (?, ?, ?)";
    $stmtLog = mysqli_prepare($loginToDb, $queryLog);
    if ($stmtLog) {
        mysqli_stmt_bind_param($stmtLog, "sss", $usernameTech, $logDescription, $ip_address);
        mysqli_stmt_execute($stmtLog);
        mysqli_stmt_close($stmtLog);
    }
}

header("Location: ../tech-panel.php?section=central-units");
exit;
