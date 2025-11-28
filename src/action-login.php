<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = "";
$db = "infra";
$loginToDb = mysqli_connect($host,$user,$password,$db);

if(!$loginToDb){
    echo "Erreur de connexion à la db";
} else {
    $base = mysqli_select_db($loginToDb,$db);
    if(!$base){
        echo "erreur";
    } else {
        $query = "SELECT * FROM users";
        $result = mysqli_query($loginToDb,$query);
        $username = $_POST["username"];
        $password = $_POST["password"];

        $loginFound = false; // Variable pour suivre si la connexion a réussi

        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                if ($row["name"] == $username && $row["mdp"] == $password){
                    $_SESSION["username"] = $username;
                    header('Location: index.php');
                    exit();
                }
            }
            // Si on arrive ici, aucun utilisateur valide n'a été trouvé
            header("Location: login.php?error=1");
            exit();
        } else {
            header("Location: login.php?error=1");
            exit();
        }
    }
}
?>