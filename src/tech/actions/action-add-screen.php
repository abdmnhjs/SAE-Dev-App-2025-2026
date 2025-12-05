<?php
session_start();

// Vérifier que l'utilisateur est autorisé (Technicien, pas Admin)
if (!isset($_SESSION['username']) || $_SESSION['username'] === "adminweb" || $_SESSION['username'] === "sysadmin") {
    header("Location: ../tech-panel.php?error=unauthorized");
    exit();
}

$host = 'localhost';
$user = 'root';
$db_password = ""; // penser à le changer si vous faites des tests en locaux
$db = "infra";

$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if (!$loginToDb) {
    die("Erreur de connexion à la db: " . mysqli_connect_error());
}

// --- Validation et Nettoyage des données POST ---
// VÉRIFICATION CRUCIALE : Assurez-vous que le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../tech-panel.php?error=form_not_submitted");
    mysqli_close($loginToDb);
    exit();
}

// Validation et conversion des types
$serial = isset($_POST['serial']) ? trim($_POST['serial']) : '';
// CORRECTION: Utilisation de 'manufacturer_id' (attendu par le formulaire corrigé) et validation INT
$manufacturerId = filter_input(INPUT_POST, 'manufacturer_id', FILTER_VALIDATE_INT);
$model = isset($_POST['model']) ? trim($_POST['model']) : '';
// Validation FLOAT
$sizeInch = filter_input(INPUT_POST, 'sizeInch', FILTER_VALIDATE_FLOAT);
$resolution = isset($_POST['resolution']) ? trim($_POST['resolution']) : '';
$connector = isset($_POST['connector']) ? trim($_POST['connector']) : '';
$attachedTo = isset($_POST['attachedTo']) ? trim($_POST['attachedTo']) : '';

// Vérification que les champs obligatoires sont présents et valides
if (empty($serial) || $manufacturerId === false || $manufacturerId === null ||
    empty($model) || $sizeInch === false || $sizeInch === null ||
    empty($resolution) || empty($connector)) {

    header("Location: ../tech-panel.php?error=missing_or_invalid_fields");
    mysqli_close($loginToDb);
    exit();
}

// Gérer la valeur NULL pour 'attached_to' si l'écran n'est attaché à rien
$attachedToValue = empty($attachedTo) ? NULL : $attachedTo;

// --- Requête Préparée pour l'Insertion ---
// CORRECTION MAJEURE : Changement de 'manufacturer' à 'id_manufacturer'
$query = "INSERT INTO screen (serial, id_manufacturer, model, 
                          size_inch, resolution, connector, 
                          attached_to)
                          
                          VALUES (?, ?, ?,
                                  ?, ?, ?,
                                  ?)";

$stmt = mysqli_prepare($loginToDb, $query);

if ($stmt) {
    // CORRECTION MAJEURE : Changement du type de liaison du fabricant de 's' à 'i'
    mysqli_stmt_bind_param($stmt, "sisdsss", // s(serial), i(id_manufacturer), s(model), d(sizeInch), s, s, s
        $serial,
        $manufacturerId,
        $model,
        $sizeInch,
        $resolution,
        $connector,
        $attachedToValue
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../tech-panel.php?section=screens&success=screen_added");
        exit();
    } else {
        // En cas d'erreur de clé primaire/étrangère ou autre
        $error = urlencode(mysqli_stmt_error($stmt));
        header("Location: ../tech-panel.php?error=screen_insert_failed:$error");
        exit();
    }

    mysqli_stmt_close($stmt);
} else {
    die("Erreur de préparation de la requête: " . mysqli_error($loginToDb));
}

mysqli_close($loginToDb);
?>