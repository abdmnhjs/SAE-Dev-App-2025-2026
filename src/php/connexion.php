<?php
session_start();

$host = "localhost";
$db   = "mysql";
$user = "root";
$pass = "!sae2025!";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e){
    die("ERREUR CONNEXION BDD : " . $e->getMessage());
}

if (!empty($_POST['User']) && !empty($_POST['Password'])) {

    $identifiant = $_POST['User'];
    $password    = $_POST['Password'];

    $query = $pdo->prepare("SELECT * FROM user WHERE User = :identifiant");
    $query->execute(['identifiant' => $identifiant]);
    $user = $query->fetch();

    if ($user && $user['Password'] == $password) {

        $_SESSION['User'] = $user['User'];
        echo "Connexion réussie !";

    } else {
        echo "Identifiant ou mot de passe incorrect";
    }

} else {
    echo "Veuillez entrer votre identifiant et votre mot de passe.";
}
?>
