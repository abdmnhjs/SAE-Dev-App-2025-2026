<?php
session_start();
require '../includes/init.php';
if ($_SESSION["role"] !== "adminweb") {
    header('location: ../index.php');
    exit();
}
$sidebarBase = '../';
$adminPrefix = '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Liste du rebut</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/adminweb.css">
    <link rel="stylesheet" href="../css/tech/tech-panel.css">
</head>
<body class="with-sidebar">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-with-sidebar tech-panel-main">
    <h1>Liste du rebut</h1>

    <div class="filters-panel">
        <p>Exporter la liste du rebut en 2 fichiers CSV :</p>
        <a href="actions/export-rebut-csv.php?type=screen">Télécharger écrans au rebut (CSV)</a>
        —
        <a href="actions/export-rebut-csv.php?type=central_unit">Télécharger unités centrales au rebut (CSV)</a>
    </div>

    <?php
    $screensRebut = mysqli_query($loginToDb, "SELECT s.* FROM screen s WHERE s.rebut_date IS NOT NULL ORDER BY s.rebut_date DESC, s.serial");
    $unitsRebut = mysqli_query($loginToDb, "SELECT cu.* FROM central_unit cu WHERE cu.rebut_date IS NOT NULL ORDER BY cu.rebut_date DESC, cu.name");
    ?>
    <h2>Écrans au rebut</h2>
    <table>
        <tr>
            <th>Numéro de série</th>
            <th>Fabricant</th>
            <th>Modèle</th>
            <th>Date rebut</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($screensRebut)) :
            $mfr = mysqli_fetch_assoc(mysqli_query($loginToDb, "SELECT name FROM manufacturer_list WHERE id = " . (int)$row['id_manufacturer']));
        ?>
        <tr>
            <td><?= htmlspecialchars($row['serial'] ?? '') ?></td>
            <td><?= $mfr ? htmlspecialchars($mfr['name']) : '—' ?></td>
            <td><?= htmlspecialchars($row['model'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['rebut_date'] ?? '') ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Unités centrales au rebut</h2>
    <table>
        <tr>
            <th>Nom</th>
            <th>Numéro de série</th>
            <th>Fabricant</th>
            <th>Modèle</th>
            <th>Date rebut</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($unitsRebut)) :
            $mfr = mysqli_fetch_assoc(mysqli_query($loginToDb, "SELECT name FROM manufacturer_list WHERE id = " . (int)$row['id_manufacturer']));
        ?>
        <tr>
            <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['serial'] ?? '') ?></td>
            <td><?= $mfr ? htmlspecialchars($mfr['name']) : '—' ?></td>
            <td><?= htmlspecialchars($row['model'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['rebut_date'] ?? '') ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</main>
</body>
</html>
