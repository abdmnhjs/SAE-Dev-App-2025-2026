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
}

header("Location: ../tech-panel.php?section=screens");
exit;
