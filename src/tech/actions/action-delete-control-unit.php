<?php
session_start();
require '../includes/init.php';
if($_SESSION["role"] !== "tech"){
    header('location: ../index.php');
    exit();
}


$serial = isset($_GET['delete']) ? trim($_GET['delete']) : null;

if ($serial) {
    $query = "DELETE FROM control_unit WHERE serial = ?";
    $stmt = mysqli_prepare($loginToDb, $query);
    mysqli_stmt_bind_param($stmt, "s", $serial);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: tech-panel.php?section=control-units");
exit;
