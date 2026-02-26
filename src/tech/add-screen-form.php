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
<form method="post" action="actions/action-add-screen.php">
    <fieldset>
        <legend>Ajouter un écran</legend>

        <div class="form-group">
            <label for="add-screen-serial">Numéro de série</label>
            <input type="text" name="serial" id="add-screen-serial" required>
        </div>

        <?php
        $sql = "SELECT id, name FROM manufacturer_list ORDER BY name ASC";
        $result = mysqli_query($loginToDb, $sql);
        ?>
        <div class="form-group">
            <label for="add-screen-manufacturer">Fabricant</label>
            <select name="manufacturer" id="add-screen-manufacturer" required>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <option value="<?= $row['id'] ?>">
                        <?= htmlspecialchars($row['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="add-screen-model">Modèle</label>
            <input type="text" name="model" id="add-screen-model" required>
        </div>

        <div class="form-group">
            <label for="add-screen-sizeInch">Taille (pouces)</label>
            <input type="number" step="0.1" name="sizeInch" id="add-screen-sizeInch" required>
        </div>

        <div class="form-group">
            <label for="add-screen-resolution">Résolution</label>
            <input type="text" name="resolution" id="add-screen-resolution" placeholder="1920x1080" required>
        </div>

        <div class="form-group">
            <label for="add-screen-connector">Connecteur</label>
            <input type="text" name="connector" id="add-screen-connector" placeholder="HDMI, DisplayPort, VGA..." required>
        </div>

        <?php
        $sql = "SELECT name FROM control_unit ORDER BY name ASC";
        $result = mysqli_query($loginToDb, $sql);
        ?>
        <div class="form-group">
            <label for="add-screen-attachedTo">Attaché à l'unité (optionnel)</label>
            <select name="attachedTo" id="add-screen-attachedTo">
                <option value="">-- Aucun --</option>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <option value="<?= $row['name'] ?>">
                        <?= htmlspecialchars($row['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group form-actions">
            <button type="submit" class="fullbtn">Ajouter l'écran</button>
        </div>
    </fieldset>
</form>

    <form class="form-csv" method="post" action="actions/action-add-screen-csv.php" enctype="multipart/form-data">
        <label for="add-screen-csv-file">Fichier csv (Nous vous recommandons que vos écrans soient attachés à des unités de contrôle existantes)</label>
        <input type="file" id="add-screen-csv-file" accept=".csv,text/csv" name="screens-csv">
        <button type="submit" name="submit-csv">Ajouter des écrans via un fichier csv</button>
    </form>

    <p>
        Le fichier CSV doit comporter une ligne d'en-tête avec les colonnes suivantes : <br>
        <code>SERIAL,MANUFACTURER,MODEL,SIZE_INCH,RESOLUTION,CONNECTOR,ATTACHED_TO</code>
    </p>
</div>
</main>
</body>
</html>