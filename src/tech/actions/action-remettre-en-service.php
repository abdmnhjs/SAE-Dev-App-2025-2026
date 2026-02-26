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
}

header("Location: ../tech-panel.php?section=rebut&success=remis_en_service");
exit;
