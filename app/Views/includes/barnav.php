<nav>
    <a href="/"><img src="images/logo.png" alt="logo-img" width="80" height="80"></a>
    <a href="inventaire" class="sections">Inventaire</a>
    <?php $username = $_SESSION['name'] ?? null; ?>

    <?php if(($_SESSION['rank'] ?? 0) >= Roles::TECH): ?>
        <a href="dashboard" class="sections">dashboard</a>
    <?php endif; ?>

    <?php if($username ?? false): ?>
        <p class='sections'>Bonjour <?= htmlspecialchars($username) ?> Ton rank : <?= $_SESSION['rank']?></p>
        <a href='handleLogout' class='sections'>Se déconnecter</a>
    <?php else: ?>
        <a href="connexion" class="sections">Se connecter</a>
        <a href="inscription" class="sections">S'inscrire</a>
        <p class='sections'>Vous n'êtes pas connecté.</p>
    <?php endif; ?>
</nav>
