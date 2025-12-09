<?php

session_start();
require 'includes/init.php';

if (isset($_SESSION['username']) && isset($_SESSION['session_start_time'])) {
    $username = mysqli_real_escape_string($loginToDb, $_SESSION['username']);
    $ip_address = mysqli_real_escape_string($loginToDb, $_SERVER['REMOTE_ADDR']);
    $duration = time() - $_SESSION['session_start_time'];

    // Check if a log already exists for this user and IP
    $sql = "SELECT duration_seconds FROM logs WHERE username = '$username' AND ip_address = '$ip_address' LIMIT 1";
    $result = mysqli_query($loginToDb, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Update existing record by adding duration
        $row = mysqli_fetch_assoc($result);
        $newDuration = $row['duration_seconds'] + $duration;
        $update = "UPDATE logs SET duration_seconds = $newDuration, log_time = NOW() 
                   WHERE username = '$username' AND ip_address = '$ip_address'";
        mysqli_query($loginToDb, $update);
    } else {
        // Insert new record
        $insert = "INSERT INTO logs (username, ip_address, duration_seconds) 
                   VALUES ('$username', '$ip_address', $duration)";
        mysqli_query($loginToDb, $insert);
    }
}



session_destroy();
header("Location: index.php");