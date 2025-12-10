<form method="post" action="/rpi12/dashboard/tech/ecran/ajouter">

    <label>Numéro de série</label>
    <input type="text" name="serial" required>

    <label>Fabricant</label>
    <select name="manufacturer">
        <option value='' selected>
            ---Aucun---
        </option>
        <?php foreach ($manufacturers as $manu): ?>
            <option value="<?= $manu['id'] ?>">
                <?= htmlspecialchars($manu['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Modèle</label>
    <input type="text" name="model">

    <label>Taille (pouces)</label>
    <input type="number" step="0.1" name="sizeInch">

    <label>Résolution</label>
    <input type="text" name="resolution" placeholder="1920x1080">

    <label>Connecteur</label>
    <input type="text" name="connector" placeholder="HDMI, DisplayPort...">

    <label>Attaché à l'unité</label>
    <select name="attached_to">
        <option value='' selected>
            ---Aucun---
        </option>
        <?php foreach ($units as $unit): ?>
            <option value="<?= $unit['name'] ?>">
                <?= htmlspecialchars($unit['name']) ?>
            </option>
        <?php endforeach; ?>

    </select>

    <button type="submit">Ajouter l'écran</button>
</form>
