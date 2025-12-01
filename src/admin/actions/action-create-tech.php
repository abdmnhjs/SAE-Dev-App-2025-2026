<?php
session_start();

// Vérifier que l'utilisateur est admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== "adminweb") {
    header("Location: ../admin-panel.php?error=unauthorized");
    exit();
}

$host = 'localhost';
$user = 'root';
$db_password = "!sae2025!"; // penser à le changer si vous faites des tests en locaux
$db = "infra";

$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if (!$loginToDb) {
    die("Erreur de connexion à la db: " . mysqli_connect_error());
}

// Récupérer et valider les données
$username = isset($_POST["username"]) ? trim($_POST["username"]) : '';
$password = isset($_POST["password"]) ? trim($_POST["password"]) : '';

// Validation des champs
if (empty($username) || empty($password)) {
    mysqli_close($loginToDb);
    header("Location: ../admin-panel.php?error=empty_fields");
    exit();
}

// Vérifier si l'utilisateur existe déjà
$check_query = "SELECT username FROM users WHERE username = ?";
$check_stmt = mysqli_prepare($loginToDb, $check_query);

if ($check_stmt) {
    mysqli_stmt_bind_param($check_stmt, "s", $username);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        mysqli_stmt_close($check_stmt);
        mysqli_close($loginToDb);
        header("Location: ../admin-panel.php?error=user_exists");
        exit();
    }
    mysqli_stmt_close($check_stmt);
}

// Insérer le nouvel utilisateur
$query = "INSERT INTO users (name, mdp) VALUES (?, ?)";
$stmt = mysqli_prepare($loginToDb, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($loginToDb);
        header("Location: ../admin-panel.php?success=1");
        exit();
    } else {
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($loginToDb);
        error_log("Erreur SQL: " . $error);
        header("Location: ../admin-panel.php?error=sql_error&details=" . urlencode($error));
        exit();
    }
} else {
    $error = mysqli_error($loginToDb);
    mysqli_close($loginToDb);
    error_log("Erreur préparation: " . $error);
    header("Location: ../admin-panel.php?error=prepare_failed&details=" . urlencode($error));
    exit();
}
?>