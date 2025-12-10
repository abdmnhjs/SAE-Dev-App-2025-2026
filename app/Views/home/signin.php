<!DOCTYPE html>
<html lang="fr">
<head>
    <base href="/">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/styles.css">
    <title>Accueil</title>
</head>
<body>

<?php include BASE_INCLUDES_PATH . 'barnav.php' ?>


<main style="margin-top: 70px; display: flex; flex-direction: column; gap: 140px;">
    <?php if (isset($_GET['error'])): ?>
        <p class="error">Invalid username or password</p>
    <?php endif; ?>
    <form class="block" method="post" action="handleLogin">
        <img src="images/logo.png" alt="logo-img" width="80" height="80" class="center">
        <h2 class="titre">Bon retour parmi nous</h2>
        <p class="description">Connectez vous à votre compte infra' pour continuer</p>
        <div class="connexion">
            <label class="champs">Nom d'utilisateur</label>
            <input type="text" name="username" placeholder="Nom d'utilisateur" class="input" required>
            <label class="champs">Mot de passe</label>
            <input type="password" name="password" placeholder="Mot de passe" class="input" required>
            <div class="hero-buttons">
                <button type="submit" class="classic-button" style="color: #121212;">Se connecter</button>
            </div>
        </div>
    </form>
</main>

</body>
</html>