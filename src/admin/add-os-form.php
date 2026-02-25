<?php
session_start();

require '../includes/init.php';
if ($_SESSION["role"] !== "adminweb") {
    header('location: ../index.php');
    exit();
}
$sidebarBase = '../';
$sidebarAdminPrefix = '';

// Récupérer tous les systèmes d'exploitation
$osList = [];
$sql = "SELECT id, name FROM os_list ORDER BY name ASC";
$result = mysqli_query($loginToDb, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $osList[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Ajouter un OS</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/adminweb.css">
</head>
<body class="with-sidebar">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-with-sidebar">
    <?php if (isset($_SESSION['username']) && $_SESSION['username'] === "adminweb") : ?>
    <section class="admin-section">
        <h2>Ajouter un système d'exploitation</h2>
        <form method="post" action="actions/action-add-os.php">
            <fieldset>
                <legend>Nouveau système d'exploitation</legend>
                <div class="form-group">
                    <label for="add-os-name">Nom</label>
                    <input type="text" name="os_name" id="add-os-name" required>
                </div>
                <div class="form-group form-actions">
                    <button type="submit">Ajouter le système d'exploitation</button>
                </div>
            </fieldset>
        </form>
    </section>

    <section class="admin-section admin-section-list">
        <h2>Systèmes d'exploitation existants</h2>
        <?php if (count($osList) === 0) : ?>
            <p>Aucun système d'exploitation enregistré.</p>
        <?php else : ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($osList as $os) : ?>
                        <tr>
                            <td><?= (int) $os['id'] ?></td>
                            <td><?= htmlspecialchars($os['name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
    <?php endif; ?>
</main>
</body>
</html>
