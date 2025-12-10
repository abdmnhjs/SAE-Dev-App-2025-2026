<form method="post" action="dashboard/tech/unite-centrale/ajouter">

    <label>Nom</label>
    <input type="text" name="name" required>

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

    <label>Type</label>
    <input type="text" name="type" placeholder="PC, Serveur, Laptop...">

    <label>CPU</label>
    <input type="text" name="cpu">

    <label>RAM (MB)</label>
    <input type="number" name="ramMb" placeholder="16384">

    <label>Disque (GB)</label>
    <input type="number" name="diskGb" placeholder="512">

    <label>Système d'exploitation</label>
    <select name="os">
        <option value='' selected>
            ---Aucun---
        </option>
        <?php foreach ($os as $os_solo): ?>
            <option value="<?= $os_solo['id'] ?>">
                <?= htmlspecialchars($os_solo['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Domaine</label>
    <input type="text" name="domain" placeholder="your.domain">

    <label>Localisation</label>
    <input type="text" name="location">

    <label>Bâtiment</label>
    <input type="text" name="building">

    <label>Salle</label>
    <input type="text" name="room">

    <label>Adresse MAC</label>
    <input type="text" name="macaddr" placeholder="FF:FF:FF:FF:FF:FF">

    <label>Date d'achat</label>
    <input type="date" name="purchaseDate">

    <label>Fin de garantie</label>
    <input type="date" name="warrantyEnd">

    <button type="submit" name="submit">Ajouter l'unité centrale</button>

</form>