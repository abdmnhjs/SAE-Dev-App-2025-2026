<?php

$host = 'localhost'; //en prod c'est localhost
$user = 'root'; //en prod c'est root
$db_password = "!sae2025!"; //penser a le changer si vous faites des tests en locaux, le mdp du rpi12 est : !sae2025!
$db = "infra"; //en prod c'est "infra"
$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if(!$loginToDb){
    die("Erreur de connexion à la db: " . mysqli_connect_error());
}
