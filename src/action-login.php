<?php
session_start();

$host = 'localhost';
$user = 'root';
$db_password = "!sae2025!"; //penser a le changer si vous faites des tests en locaux, le mdp du rpi12 est : !sae2025!
$db = "infra";
$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if(!$loginToDb){
    die("Erreur de connexion à la db: " . mysqli_connect_error());
}

$username = $_POST["username"];
$password = $_POST["password"];

$query = "SELECT * FROM users WHERE name = ? AND mdp = ?";
$stmt = mysqli_prepare($loginToDb, $query);

if($stmt){

    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0){
        $_SESSION["username"] = $username;
        mysqli_stmt_close($stmt);
        mysqli_close($loginToDb);
        if($username === "adminweb"){
            header('Location: admin/admin-panel.php');
        } else {
            header('Location: index.php');
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