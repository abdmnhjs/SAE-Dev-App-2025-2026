<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/styles.css">
    <title>Connexion</title>
</head>
<body class="with-sidebar">
<?php require __DIR__ . '/includes/sidebar.php'; ?>
<main class="main-with-sidebar">
    <div class="connexion">
        <?php include_once "html/connexion.html"; ?>
        <?php if (isset($_GET['error'])) : ?>
            <p style="color: red">Erreur de connexion</p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
