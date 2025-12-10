<form method="post" action="dashboard/tech/ecran/modifier">

    <label>Numéro de série</label>
    <input type="text" name="serial" value="<?= htmlspecialchars($getScreen['serial']) ?>" readonly>

    <label>Fabricant</label>
    <select name="manufacturer">
        <option value=''>
            ---Aucun---
        </option>
        <?php foreach ($manufacturers as $manu): ?>
            <option value="<?= $manu['id'] ?>" <?= $manu['id'] == $getScreen['id_manufacturer'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($manu['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Modèle</label>
    <input type="text" name="model" value="<?= htmlspecialchars($getScreen['model']) ?>">

    <label>Taille (pouces)</label>
    <input type="number" step="0.1" name="sizeInch" value="<?= htmlspecialchars($getScreen['size_inch']) ?>">

    <label>Résolution</label>
    <input type="text" name="resolution" placeholder="1920x1080" value="<?= htmlspecialchars($getScreen['resolution']) ?>">

    <label>Connecteur</label>
    <input type="text" name="connector" placeholder="HDMI, DisplayPort..." value="<?= htmlspecialchars($getScreen['connector']) ?>">

    <label>Attaché à l'unité</label>
    <select name="attached_to">
        <option value=''>
            ---Aucun---
        </option>
        <?php foreach ($units as $unit): ?>
            <option value="<?= $unit['serial'] ?>" <?= $unit['serial'] == $getScreen['attached_to'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($unit['serial']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Modifier l'écran</button>
</form>