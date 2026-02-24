<?php
session_start();
require '../includes/init.php';
if($_SESSION["role"] !== "tech"){
    header('location: ../index.php');
    exit();
}
$sidebarBase = '../';
$sidebarTechPrefix = '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset='UTF-8'>
    <title>Tech Panel</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/tech/tech-panel.css">
</head>
<body class="with-sidebar">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-with-sidebar tech-panel-main">
<div>
    <form method="post" action="actions/action-add-control-unit.php">
        <table class="form-table">

            <tr>
                <td><label>Nom</label></td>
                <td><input type="text" name="name" required></td>
            </tr>

            <tr>
                <td><label>Numéro de série</label></td>
                <td><input type="text" name="serial" required></td>
            </tr>

            <?php
            $sql = "SELECT id, name FROM manufacturer_list ORDER BY name ASC";
            $result = mysqli_query($loginToDb, $sql);
            ?>
            <tr>
                <td><label>Fabricant</label></td>
                <td>
                    <select name="manufacturer" required>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <option value="<?= $row['id'] ?>">
                                <?= htmlspecialchars($row['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td><label>Modèle</label></td>
                <td><input type="text" name="model" required></td>
            </tr>

            <tr>
                <td><label>Type</label></td>
                <td><input type="text" name="type" placeholder="PC, Serveur, Laptop..." required></td>
            </tr>

            <tr>
                <td><label>CPU</label></td>
                <td><input type="text" name="cpu" required></td>
            </tr>

            <tr>
                <td><label>RAM (MB)</label></td>
                <td><input type="number" name="ramMb" placeholder="16384" required></td>
            </tr>

            <tr>
                <td><label>Disque (GB)</label></td>
                <td><input type="number" name="diskGb" placeholder="512" required></td>
            </tr>

            <?php
            $sql = "SELECT id, name FROM os_list ORDER BY name ASC";
            $result = mysqli_query($loginToDb, $sql);
            ?>
            <tr>
                <td><label>Système d'exploitation</label></td>
                <td>
                    <select name="os" required>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <option value="<?= htmlspecialchars($row['id']) ?>">
                                <?= htmlspecialchars($row['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td><label>Domaine</label></td>
                <td><input type="text" name="domain" placeholder="CORP.LOCAL"></td>
            </tr>

            <tr>
                <td><label>Localisation</label></td>
                <td><input type="text" name="location" required></td>
            </tr>

            <tr>
                <td><label>Bâtiment</label></td>
                <td><input type="text" name="building" required></td>
            </tr>

            <tr>
                <td><label>Salle</label></td>
                <td><input type="text" name="room" required></td>
            </tr>

            <tr>
                <td><label>Adresse MAC</label></td>
                <td><input type="text" name="macaddr" placeholder="00:1A:2B:3C:4D:5E" required></td>
            </tr>

            <tr>
                <td><label>Date d'achat</label></td>
                <td><input type="date" name="purchaseDate" required></td>
            </tr>

            <tr>
                <td><label>Fin de garantie</label></td>
                <td><input type="date" name="warrantyEnd" required></td>
            </tr>

            <tr>
                <td colspan="2" style="text-align:center;">
                    <button type="submit" name="submit" class="fullbtn">Ajouter l'unité de contrôle</button>
                </td>
            </tr>

        </table>
    </form>
    <form method="post" action="actions/action-add-control-unit-csv.php" enctype="multipart/form-data">

        <label for="control-units-csv" class="filebtn">Import CSV</label>
        <input type="file" accept="text/csv" name="control-units-csv" />

        <button type="submit">Ajouter des unités de contrôle via un fichier csv</button>
    </form>


    <p>
        Le fichier CSV doit comporter une ligne d'en-tête avec les colonnes suivantes : <br>
        <code>NAME, SERIAL, MANUFACTURER, MODEL, TYPE, CPU, RAM_MB, DISK_GB, OS, DOMAIN,
            LOCATION, BUILDING, ROOM, MACADDR, PURCHASE_DATE, WARRANTY_END</code>
    </p>

</div>
</main>
</body>
</html>

