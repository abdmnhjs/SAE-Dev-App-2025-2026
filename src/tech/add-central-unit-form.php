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
        <fieldset>
            <legend>Ajouter une unité centrale</legend>

            <div class="form-group">
                <label for="add-cu-name">Nom</label>
                <input type="text" name="name" id="add-cu-name" required>
            </div>

            <div class="form-group">
                <label for="add-cu-serial">Numéro de série</label>
                <input type="text" name="serial" id="add-cu-serial" required>
            </div>

            <?php
            $sql = "SELECT id, name FROM manufacturer_list ORDER BY name ASC";
            $result = mysqli_query($loginToDb, $sql);
            ?>
            <div class="form-group">
                <label for="add-cu-manufacturer">Fabricant</label>
                <select name="manufacturer" id="add-cu-manufacturer" required>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <option value="<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="add-cu-model">Modèle</label>
                <input type="text" name="model" id="add-cu-model" required>
            </div>

            <div class="form-group">
                <label for="add-cu-type">Type</label>
                <input type="text" name="type" id="add-cu-type" placeholder="PC, Serveur, Laptop..." required>
            </div>

            <div class="form-group">
                <label for="add-cu-cpu">CPU</label>
                <input type="text" name="cpu" id="add-cu-cpu" required>
            </div>

            <div class="form-group">
                <label for="add-cu-ramMb">RAM (MB)</label>
                <input type="number" name="ramMb" id="add-cu-ramMb" placeholder="16384" required>
            </div>

            <div class="form-group">
                <label for="add-cu-diskGb">Disque (GB)</label>
                <input type="number" name="diskGb" id="add-cu-diskGb" placeholder="512" required>
            </div>

            <?php
            $sql = "SELECT id, name FROM os_list ORDER BY name ASC";
            $result = mysqli_query($loginToDb, $sql);
            ?>
            <div class="form-group">
                <label for="add-cu-os">Système d'exploitation</label>
                <select name="os" id="add-cu-os" required>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <option value="<?= htmlspecialchars($row['id']) ?>">
                            <?= htmlspecialchars($row['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="add-cu-location">Localisation</label>
                <input type="text" name="location" id="add-cu-location" required>
            </div>

            <div class="form-group">
                <label for="add-cu-building">Bâtiment</label>
                <input type="text" name="building" id="add-cu-building" required>
            </div>

            <div class="form-group">
                <label for="add-cu-room">Salle</label>
                <input type="text" name="room" id="add-cu-room" required>
            </div>

            <div class="form-group">
                <label for="add-cu-macaddr">Adresse MAC</label>
                <input type="text" name="macaddr" id="add-cu-macaddr" placeholder="00:1A:2B:3C:4D:5E" required>
            </div>

            <div class="form-group">
                <label for="add-cu-purchaseDate">Date d'achat</label>
                <input type="date" name="purchaseDate" id="add-cu-purchaseDate" required>
            </div>

            <div class="form-group">
                <label for="add-cu-warrantyEnd">Fin de garantie</label>
                <input type="date" name="warrantyEnd" id="add-cu-warrantyEnd" required>
            </div>

            <div class="form-group">
                <label for="add-cu-domain">Domaine (optionnel)</label>
                <input type="text" name="domain" id="add-cu-domain" placeholder="CORP.LOCAL">
            </div>

            <div class="form-group form-actions">
                <button type="submit" name="submit" class="fullbtn">Ajouter l'unité centrale</button>
            </div>
        </fieldset>
    </form>
    <form method="post" action="actions/action-add-central-unit-csv.php" enctype="multipart/form-data">

        <label for="add-cu-csv-file" class="filebtn">Import CSV</label>
        <input type="file" id="add-cu-csv-file" accept=".csv,text/csv" name="control-units-csv" />

        <button type="submit" name="submit-csv">Ajouter des unités centrales via un fichier csv</button>
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

