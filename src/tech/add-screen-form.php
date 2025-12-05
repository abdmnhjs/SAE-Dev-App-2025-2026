<?php
session_start();
require '../includes/connexion_bdd.php';

if (!isset($_SESSION['username']) ||
    !in_array($_SESSION['username'], ["adminweb", "sysadmin", "tech", "tech1"])) {
    header('Location: ../tech-panel.php?error=403');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<div>
    <form method='post' action='actions/action-add-screen.php'>
        <label>Numéro de série</label>
        <input type='text' name='serial' required>

        <?php
        $sql = "SELECT id, name FROM manufacturer_list ORDER BY name ASC";
        $result = mysqli_query($loginToDb, $sql);
        ?>
        <label>Fabricant</label>
        <select name="manufacturer" required>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <option value="<?= $row['id'] ?>">
                    <?= htmlspecialchars($row['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Modèle</label>
        <input type='text' name='model' required>

        <label>Taille (pouces)</label>
        <input type='number' step='0.1' name='sizeInch' required>

        <label>Résolution</label>
        <input type='text' name='resolution' placeholder='1920x1080' required>

        <label>Connecteur</label>
        <input type='text' name='connector' placeholder='HDMI, DisplayPort, VGA...' required>

        <?php
        $sql = "SELECT serial FROM control_unit ORDER BY serial ASC";
        $result = mysqli_query($loginToDb, $sql);
        ?>
        <label>Attaché à l'unité</label>
        <select name="attachedTo">
            <option value="">-- Aucun --</option>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <option value="<?= $row['serial'] ?>">
                    <?= htmlspecialchars($row['serial']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type='submit'>Ajouter l'écran</button>
    </form>
</div>
</body>
</html>