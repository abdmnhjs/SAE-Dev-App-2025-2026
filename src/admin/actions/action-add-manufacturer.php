<?php
session_start();

// Vérification de l'autorisation et de la soumission du formulaire
if (!isset($_SESSION['username']) || $_SESSION['username'] !== "adminweb") {
    header("Location: ../admin-panel.php?error=unauthorized");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../admin-panel.php?error=not_a_post_request");
    exit();
}

$host = 'localhost';
$user = 'root';
$db_password = ""; // À changer pour les tests en local
$db = "infra";

$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if (!$loginToDb) {
    die("Erreur de connexion à la db: " . mysqli_connect_error());
}

// CORRECTION CRUCIALE : Utiliser le nom correct du champ du formulaire (ex: os_name)
$manufacturerName = isset($_POST['manufacturer_name']) ? trim($_POST['manufacturer_name']) : '';
// Si votre formulaire a 'name="os"', changez la ligne ci-dessus en : $osName = isset($_POST['os']) ? trim($_POST['os']) : '';

if (empty($manufacturerName)) {
    mysqli_close($loginToDb);
    header("Location: ../admin-panel.php?error=empty_name");
    exit();
}

$query = "INSERT INTO manufacturer_list (name) VALUES (?)";
$stmt = mysqli_prepare($loginToDb, $query);

if($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $manufacturerName); // Lie la variable $osName

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../admin-panel.php?success=manufacturer_added");
        exit();
    } else {
        // En cas d'échec d'insertion (ex: violation de la contrainte UNIQUE)
        $error_message = urlencode(mysqli_stmt_error($stmt) ?: "fail");
        header("Location: ../admin-panel.php?error=db_fail:$error_message");
        exit();
    }

    mysqli_stmt_close($stmt);
} else {
    die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
}

mysqli_close($loginToDb);
?>