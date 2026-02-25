<?php
session_start();

require '../includes/init.php';
if ($_SESSION["role"] !== "adminweb") {
    header('location: ../index.php');
    exit();
}
$sidebarBase = '../';
$sidebarAdminPrefix = '';

// Récupérer tous les fabricants
$manufacturerList = [];
$sql = "SELECT id, name FROM manufacturer_list ORDER BY name ASC";
$result = mysqli_query($loginToDb, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $manufacturerList[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Ajouter un fabricant</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/adminweb.css">
</head>
<body class="with-sidebar">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-with-sidebar">
    <?php if (isset($_SESSION['username']) && $_SESSION['username'] === "adminweb") : ?>
    <section class="admin-section">
        <h2>Ajouter un fabricant</h2>
        <form method="post" action="actions/action-add-manufacturer.php">
            <fieldset>
                <legend>Nouveau fabricant</legend>
                <div class="form-group">
                    <label for="add-manufacturer-name">Nom</label>
                    <input type="text" name="manufacturer_name" id="add-manufacturer-name" required>
                </div>
                <div class="form-group form-actions">
                    <button type="submit">Ajouter le fabricant</button>
                </div>
            </fieldset>
        </form>
    </section>

    <section class="admin-section admin-section-list">
        <h2>Fabricants existants</h2>
        <?php if (count($manufacturerList) === 0) : ?>
            <p>Aucun fabricant enregistré.</p>
        <?php else : ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($manufacturerList as $m) : ?>
                        <tr>
                            <td><?= (int) $m['id'] ?></td>
                            <td><?= htmlspecialchars($m['name']) ?></td>
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
