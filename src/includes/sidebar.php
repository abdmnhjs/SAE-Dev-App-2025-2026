<?php
$base = $sidebarBase ?? '';
$techPrefix = $sidebarTechPrefix ?? $base . 'tech/';
$adminPrefix = $sidebarAdminPrefix ?? $base . 'admin/';
$sysadminPrefix = $sidebarSysadminPrefix ?? $base . 'sysadmin/';
?>
<style>
/* Sidebar : thème sombre (inclus directement pour cohérence sur toutes les pages) */
.sidebar-app {
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  width: 250px;
  background-color: #1a1a1a;
  padding: 20px 0;
  box-shadow: 2px 0 12px rgba(0, 0, 0, 0.3);
  overflow-y: auto;
}
.sidebar-app .sidebar-header {
  padding: 0 15px 20px;
  border-bottom: 1px solid #333;
  margin-bottom: 15px;
}
.sidebar-app .sidebar-header img { display: block; }
.sidebar-app .sidebar-sections {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 0 15px;
}
.sidebar-app .sidebar-section {
  display: block;
  padding: 12px 16px;
  color: #e0e0e0;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.2s ease;
  font-size: 15px;
}
.sidebar-app .sidebar-section:hover {
  color: #fff;
  background-color: #282828;
}
.sidebar-app .sidebar-label {
  display: block;
  padding: 12px 16px 4px;
  color: #888;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.sidebar-app .sidebar-user {
  display: block;
  padding: 16px 16px 8px;
  color: #aaa;
  font-size: 13px;
  margin-top: 8px;
  border-top: 1px solid #333;
}
body.with-sidebar { margin-left: 270px; margin-right: 40px; }
.main-with-sidebar { margin-top: 40px; display: flex; flex-direction: column; gap: 140px; }
.tech-panel-main.main-with-sidebar { gap: 2rem; }
</style>
<div class="sidebar-app">
    <div class="sidebar-header">
        <a href="<?php echo $base; ?>index.php"><img src="<?php echo $base; ?>images/logo.png" alt="logo-infra" width="60" height="60"></a>
    </div>
    <div class="sidebar-sections">
        <a class="sidebar-section" href="<?php echo $base; ?>index.php">Accueil</a>
        <a class="sidebar-section" href="<?php echo $base; ?>stats.php">Statistiques</a>

        <?php if (!isset($_SESSION['role'])) : ?>
            <a class="sidebar-section" href="<?php echo $base; ?>connexion.php">Se connecter</a>
        <?php elseif ($_SESSION['role'] === 'tech') : ?>
            <span class="sidebar-label">Technicien</span>
            <a class="sidebar-section" href="<?php echo $techPrefix; ?>tech-panel.php?section=screens">Écrans</a>
            <a class="sidebar-section" href="<?php echo $techPrefix; ?>tech-panel.php?section=central-units">Unités centrales</a>
            <a class="sidebar-section" href="<?php echo $techPrefix; ?>add-screen-form.php">Ajouter un écran</a>
            <a class="sidebar-section" href="<?php echo $techPrefix; ?>add-central-unit-form.php">Ajouter une unité centrale</a>
            <a class="sidebar-section" href="<?php echo $techPrefix; ?>tech-panel.php?section=rebut">Liste du rebut</a>
            <span class="sidebar-user"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
            <a class="sidebar-section" href="<?php echo $base; ?>logout.php">Se déconnecter</a>
        <?php elseif ($_SESSION['role'] === 'adminweb') : ?>
            <span class="sidebar-label">Administration</span>
            <a class="sidebar-section" href="<?php echo $adminPrefix; ?>create-tech-form.php">Créer un technicien</a>
            <a class="sidebar-section" href="<?php echo $adminPrefix; ?>add-os-form.php">Ajouter un système d'exploitation</a>
            <a class="sidebar-section" href="<?php echo $adminPrefix; ?>add-manufacturer-form.php">Ajouter un fabricant</a>
            <a class="sidebar-section" href="<?php echo $adminPrefix; ?>admin_panel-logs.php">Logs</a>
            <a class="sidebar-section" href="<?php echo $adminPrefix; ?>rebut-list.php">Liste du rebut</a>
            <span class="sidebar-user"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
            <a class="sidebar-section" href="<?php echo $base; ?>logout.php">Se déconnecter</a>
        <?php elseif ($_SESSION['role'] === 'sysadmin') : ?>
            <span class="sidebar-label">Sysadmin</span>
            <a class="sidebar-section" href="<?php echo $sysadminPrefix; ?>logs.php">Logs</a>
            <span class="sidebar-user"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
            <a class="sidebar-section" href="<?php echo $base; ?>logout.php">Se déconnecter</a>
        <?php endif; ?>
    </div>
</div>
