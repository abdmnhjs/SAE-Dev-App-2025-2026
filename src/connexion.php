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
        <form class="block" method="post" action="action-login.php">
            <img src="images/logo.png" alt="logo-img" width="80" height="80" class="center">
            <h2 class="titre">Bon retour parmi nous</h2>
            <p class="description">Connectez vous à votre compte infra' pour continuer</p>
            <div class="connexion">
                <label class="champs">Nom d'utilisateur</label>
                <input type="text" name="username" class="input">
                <label class="champs">Mot de passe</label>
                <input type="password" name="password" class="input">
                <div class="hero-buttons">
                    <button type="submit" class="classic-button" style="color: #121212;">Se connecter</button>
                </div>
                <?php if (isset($_GET['error'])) : ?>
                    <p style="color: red; margin-top: 8px;">Erreur de connexion</p>
                <?php endif; ?>
            </div>
        </form>
    </div>
</main>
</body>
</html>
