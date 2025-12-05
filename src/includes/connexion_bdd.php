<?php

$host = 'localhost';
$user = 'root';
$db_password = "admin"; //penser a le changer si vous faites des tests en locaux, le mdp du rpi12 est : !sae2025!
$db = "infra";
$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if(!$loginToDb){
    die("Erreur de connexion à la db: " . mysqli_connect_error());
}
