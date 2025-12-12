<?php
session_start();
require '../includes/init.php';
ensureUserAuthorized("tech");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Tech Panel</title>
    <link rel="stylesheet" href="../css/tech/tech-panel.css">
</head>
<body>
<div class='sidebar'>
    <div class='sidebar-sections'>
            <a class='sidebar-section' href='../logout.php' class='sections'>Se déconnecter</a>

        <a class='sidebar-section' href='tech-panel.php?section=screens'>écrans</a>
        <a class='sidebar-section' href='tech-panel.php?section=control-units'>Unités de contrôle</a>
            <a class='sidebar-section' href='add-screen-form.php'>Ajouter un écran</a>
    <a class='sidebar-section' href='add-control-unit-form.php'>Ajouter une unité de controle</a>

    </div>
</div>
<div>
<form method='post' action='actions/action-add-screen.php'>
    <table>
        <tr>
            <td><label for="serial">Numéro de série</label></td>
            <td><input type='text' name='serial' id="serial" required></td>
        </tr>

        <?php
        $sql = "SELECT id, name FROM manufacturer_list ORDER BY name ASC";
        $result = mysqli_query($loginToDb, $sql);
        ?>
        <tr>
            <td><label for="manufacturer">Fabricant</label></td>
            <td>
                <select name="manufacturer" id="manufacturer" required>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <option value="<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td><label for="model">Modèle</label></td>
            <td><input type='text' name='model' id="model" required></td>
        </tr>

        <tr>
            <td><label for="sizeInch">Taille (pouces)</label></td>
            <td><input type='number' step='0.1' name='sizeInch' id="sizeInch" required></td>
        </tr>

        <tr>
            <td><label for="resolution">Résolution</label></td>
            <td><input type='text' name='resolution' id="resolution" placeholder='1920x1080' required></td>
        </tr>

        <tr>
            <td><label for="connector">Connecteur</label></td>
            <td><input type='text' name='connector' id="connector" placeholder='HDMI, DisplayPort, VGA...' required></td>
        </tr>

        <?php
        $sql = "SELECT name FROM control_unit ORDER BY name ASC";
        $result = mysqli_query($loginToDb, $sql);
        ?>
        <tr>
            <td><label for="attachedTo">Attaché à l'unité</label></td>
            <td>
                <select name="attachedTo" id="attachedTo">
                    <option value="">-- Aucun --</option>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <option value="<?= $row['name'] ?>">
                            <?= htmlspecialchars($row['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="text-align:center;">
                <button  class="fullbtn" type='submit'>Ajouter l'écran</button>
            </td>
        </tr>
    </table>
</form>

    <form method="post" action="actions/action-add-screen-csv.php" enctype="multipart/form-data">
        <label for="screen-csv" id="label-screen-csv">Fichier csv (Nous vous recommandons que vos écrans soient attachés à des unités de contrôle existantes)</label>
        <input type="file" accept="text/csv" name="screens-csv" id="screen-csv"/>
        <button type="submit">Ajouter des écrans via un fichier csv</button>
    </form>

    <p>
        Le fichier CSV doit comporter une ligne d'en-tête avec les colonnes suivantes : <br>
        <code>SERIAL,MANUFACTURER,MODEL,SIZE_INCH,RESOLUTION,CONNECTOR,ATTACHED_TO</code>
    </p>
</div>
</body>
</html>