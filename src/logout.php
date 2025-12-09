<?php

session_start();
require 'includes/init.php';

if (isset($_SESSION['username']) && isset($_SESSION['session_start_time'])) {
    $login = $_SESSION['username'];
    $ip_address = mysqli_real_escape_string($loginToDb, $_SERVER['REMOTE_ADDR']);
    $duration = time() - $_SESSION['session_start_time'];

    $sql = "INSERT INTO logs (login, ip_address, duration_seconds) 
                VALUES ('$login', '$ip_address', $duration)";


    $stmt = mysqli_prepare($loginToDb, $sql);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
}



session_destroy();
header("Location: index.php");