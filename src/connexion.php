
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

<?php
include_once "html/connexion.html";

if (isset($_GET['error'])) {
    echo "<p style='color: red'>Erreur de connexion</p>";
}
