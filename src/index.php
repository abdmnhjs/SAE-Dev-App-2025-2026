<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/styles.css">
    <title>Accueil</title>
</head>
<body>

<nav>
    <a href="index.php"><img src="images/logo.png" alt="logo-img" width="80" height="80"></a>
    <a href="stats.php" class="sections">Statistiques</a>
    <a href="connexion.php" class="sections">Se connecter</a>
    <a href="inscription.php" class="sections">S'inscrire</a>



<?php
session_start();



if(isset($_SESSION['username'])) {
    $username= $_SESSION['username'];
    echo "<p class='sections'>Bonjour " . $_SESSION['username'] . "</p>";
    echo "<a href='logout.php' class='sections'>Se déconnecter</a>";
} else {
    echo "<p class='sections'>Vous n'êtes pas connecté.</p>";
}
?>
</nav>
<main style="margin-top: 70px; display: flex; flex-direction: column; gap: 140px;">

    <div class="section">
        <h1 class="hero-title">Votre parc informatique tout en un.</h1>
        <p class="description">Commencez dès maintentant et sans inscription à consulter l'inventaire du parc infortmatique ou <br>commencez à le gérer à portée de mains depuis votre compte technicien.</p>
        <div class="hero-buttons">
            <a href="inventaire.php" class="classic-button" style="color: #121212; padding: 10px; border-radius: 5px; text-decoration: none; display: inline-block; text-align: center;">Commencer à découvrir</a>
            <a href="connexion.php" class="cta-technician" style="color: white; padding: 10px; border-radius: 5px; text-decoration: none; display: inline-block; text-align: center;">Commencer à gérer l'inventaire</a>
        </div>
        <iframe width="800" height="450"
                src="https://www.youtube.com/embed/f8biXsnWJS0"
                title="YouTube video player"
                style="border: none;"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
        </iframe>
    </div>

    <div class="section">
        <h2 class="features-title">Vous êtes un utilisateur non-inscrit ?</h2>
        <p class="description">Consultez les machines de l'inventaire dont les moniteurs et les unités centrales.</p>
        <img src="images/tab.png" alt="symbole de tableau minimaliste">
    </div>

    <div class="section">
        <h2 class="features-title">Vous êtes un technicien ?</h2>
        <p class="description">Connectez vous à votre espace afin de consulter et gérer les machines de l'inventaire.</p>
        <div class="technician-features">
            <div class="technician-feature">
                <img src="images/icones/desktop.svg" alt="desktop icon" width="50" height="50">
                <h3>Création et modification de machines dans l'inventaire</h3>
                <p style="color: white; opacity: 45%">Créez des machines dans l'inventaire via un simple formulaire <br>ou modifiez des informations relatives à elles.</p>
            </div>
            <div class="technician-feature">
                <img src="images/icones/file.svg" alt="file icon" width="50" height="50">
                <h3>Importation/exportation de machines via un fichier csv.</h3>
                <p style="color: white; opacity: 45%">Automatisez la création de machines via l'importation de machines depuis un fichier csv, ou bien la récupération via l'exportation. </p>
            </div>
            <div class="technician-feature">
                <img src="images/icones/search.svg" alt="search icon" width="50" height="50">
                <h3>Recherche de machines</h3>
                <p style="color: white; opacity: 45%">Retrouvez facilement des machines en naviguant entre les catégories<br> "moniteurs" et "unité centrales", ainsi que dans le rébut.</p>
            </div>
            <div class="technician-feature">
                <img src="images/icones/trash.svg" alt="trash icon" width="50" height="50">
                <h3>Suppression de machines dans l'inventaire</h3>
                <p style="color: white; opacity: 45%">Supprimez des machines de l'inventaire<br> et retrouvez les dans le rébut.</p>
            </div>
        </div>
    </div>

    <div>

    </div>
</main>

</body>
</html>
