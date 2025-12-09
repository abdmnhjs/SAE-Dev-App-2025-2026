<?php
session_start();

require "includes/connexion_bdd.php";

//pour logs
if (!isset($_SESSION['session_start_time'])) {
    $_SESSION['session_start_time'] = time();
}

$username = $_POST["username"];
$password = $_POST["password"];

$query = "SELECT * FROM users WHERE name = ? AND mdp = ?";
$stmt = mysqli_prepare($loginToDb, $query); //les variables sont dans connexion_bdd.php

if($stmt){

    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0){
        $_SESSION["username"] = $username;
        mysqli_stmt_close($stmt);
        mysqli_close($loginToDb);
        if($username === "adminweb"){
            header('Location: admin/admin_panel-logs.php?section=os');
        } else {
            header('Location: tech/tech-panel.php?section=screens');
        }
        exit();
    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($loginToDb);
        header("Location: connexion.php?error=1");
        exit();
    }
} else {
    die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
}
?>