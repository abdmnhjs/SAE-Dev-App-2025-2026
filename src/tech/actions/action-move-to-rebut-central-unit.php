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
}

header("Location: ../tech-panel.php?section=central-units");
exit;
