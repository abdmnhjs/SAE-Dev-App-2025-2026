<form method="post" action="/rpi12/dashboard/tech/unite-centrale/modifier">

    <label>Numéro de série</label>
    <input type="text" name="serial" value="<?= htmlspecialchars($getUnit['serial']) ?>" readonly>

    <label>Nom</label>
    <input type="text" name="name" value="<?= htmlspecialchars($getUnit['name']) ?>" required>

    <label>Fabricant</label>
    <select name="manufacturer">
        <?php foreach ($manufacturers as $manu): ?>
            <option value="<?= $manu['id'] ?>" <?= $manu['id'] == $getUnit['id_manufacturer'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($manu['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Modèle</label>
    <input type="text" name="model" value="<?= htmlspecialchars($getUnit['model']) ?>">

    <label>Type</label>
    <input type="text" name="type" placeholder="PC, Serveur, Laptop..." value="<?= htmlspecialchars($getUnit['type']) ?>">

    <label>CPU</label>
    <input type="text" name="cpu" value="<?= htmlspecialchars($getUnit['cpu']) ?>">

    <label>RAM (MB)</label>
    <input type="number" name="ramMb" placeholder="16384" value="<?= htmlspecialchars($getUnit['ram_mb']) ?>">

    <label>Disque (GB)</label>
    <input type="number" name="diskGb" placeholder="512" value="<?= htmlspecialchars($getUnit['disk_gb']) ?>">

    <label>Système d'exploitation</label>
    <select name="os" required>
        <?php foreach ($os as $os_solo): ?>
            <option value="<?= $os_solo['id'] ?>" <?= $os_solo['id'] == $getUnit['id_os'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($os_solo['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Domaine</label>
    <input type="text" name="domain" placeholder="your.domain" value="<?= htmlspecialchars($getUnit['domain']) ?>">

    <label>Localisation</label>
    <input type="text" name="location" value="<?= htmlspecialchars($getUnit['location']) ?>">

    <label>Bâtiment</label>
    <input type="text" name="building" value="<?= htmlspecialchars($getUnit['building']) ?>">

    <label>Salle</label>
    <input type="text" name="room" value="<?= htmlspecialchars($getUnit['room']) ?>">

    <label>Adresse MAC</label>
    <input type="text" name="macaddr" placeholder="FF:FF:FF:FF:FF:FF" value="<?= htmlspecialchars($getUnit['macaddr']) ?>">

    <label>Date d'achat</label>
    <input type="date" name="purchaseDate" value="<?= htmlspecialchars($getUnit['purchase_date']) ?>">

    <label>Fin de garantie</label>
    <input type="date" name="warrantyEnd" value="<?= htmlspecialchars($getUnit['warranty_end']) ?>">

    <button type="submit" name="submit">Modifier l'unité centrale</button>

</form>