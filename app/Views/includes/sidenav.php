<div class='sidebar'>
    <div class='sidebar-sections'>
        <a href="/rpi12/"><img src="images/logo.png" alt="logo-img" width="80" height="80"></a>
        <?php $username = $_SESSION['name'] ?? null; ?>
        <?php if ($username ?? false): ?>
            <p style="color: black">Bonjour <?= htmlspecialchars($username) ?> Ton rank
                : <?= $_SESSION['rank'] ?></p>
            <a href='/rpi12/handleLogout' class='sidebar-section'>Se déconnecter</a>
        <?php else: ?>
            <a href="/rpi12/connexion" class='sidebar-section'>Se connecter</a>
            <a href="/rpi12/inscription" class='sidebar-section'>S'inscrire</a>
            <p class='sidebar-section'>Vous n'êtes pas connecté.</p>
        <?php endif; ?>
        <a href="/rpi12/inventaire" class='sidebar-section'>Inventaire</a>


        <?php if (($_SESSION['rank'] ?? 0) >= Roles::TECH): ?>
            <a href="/rpi12/dashboard" class='sidebar-section'>dashboard</a>
        <?php endif; ?>

        <!-- OPTIONS DU TECHNICIEN -->
        <?php if (($_SESSION['rank'] ?? 0) == Roles::TECH): ?>
            <a class='sidebar-section' href='/rpi12/dashboard/tech?section=screens'>Moniteurs</a>
            <a class='sidebar-section' href='/rpi12/dashboard/tech?section=control-units'>Unités centrales</a>
            <a class='sidebar-section' href='/rpi12/dashboard/tech?section=add-screen'>Ajouter un écran</a>
            <a class='sidebar-section' href='/rpi12/dashboard/tech?section=add-control-unit'>Ajouter une unité centrale</a>
                    <a class='sidebar-section' href='/rpi12/dashboard/tech?section=add-screens'>Ajouter des écrans</a>
            <a class='sidebar-section' href='/rpi12/dashboard/tech?section=add-control-units'>Ajouter des unités centrales</a>
        <?php endif; ?>

        <!-- OPTIONS DU ADMINWEB -->
        <?php if (($_SESSION['rank'] ?? 0) == Roles::ADMINWEB): ?>
            <a class='sidebar-section' href='/rpi12/dashboard/admin/techniciens'>Techniciens</a>
        <?php endif; ?>

        <!-- OPTIONS DU SYSADMIN -->
        <?php if (($_SESSION['rank'] ?? 0) == Roles::SYSADMIN): ?>
            <a class='sidebar-section' href='/rpi12/dashboard/tech?section=screens'>Moniteurs</a>
            <a class='sidebar-section' href='/rpi12/dashboard/tech?section=control-units'>Unités centrales</a>
            <a class='sidebar-section' href='/rpi12/dashboard/tech/ecran/ajouter'>Ajouter un écran</a>
            <a class='sidebar-section' href='/rpi12/dashboard/tech/unite-centrale/ajouter'>Ajouter une unité centrale</a>
        <?php endif; ?>
    </div>
</div>
