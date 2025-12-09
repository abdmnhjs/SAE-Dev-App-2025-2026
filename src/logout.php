<?php

session_start();
require 'includes/init.php';

if (isset($_SESSION['username']) && isset($_SESSION['session_start_time'])) {
    $username = mysqli_real_escape_string($loginToDb, $_SESSION['username']);
    $ip_address = mysqli_real_escape_string($loginToDb, $_SERVER['REMOTE_ADDR']);
    $duration = time() - $_SESSION['session_start_time'];
        $insert = "INSERT INTO logs (username, ip_address, duration_seconds) 
                   VALUES ('$username', '$ip_address', $duration)";
        mysqli_query($loginToDb, $insert);

}



session_destroy();
header("Location: index.php");