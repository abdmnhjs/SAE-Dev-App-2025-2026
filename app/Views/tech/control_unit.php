<table>
    <tr>
        <th>Numéro de série</th>
        <th>Nom</th>
        <th>Fabricant</th>
        <th>Modèle</th>
        <th>Type</th>
        <th>CPU</th>
        <th>RAM (Mo)</th>
        <th>Stockage (Go)</th>
        <th>Système d'exploitation</th>
        <th>Domaine</th>
        <th>Emplacement</th>
        <th>Bâtiment</th>
        <th>Pièce</th>
        <th>Adresse MAC</th>
        <th>Date d'achat</th>
        <th>Fin de garantie</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($units as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['serial']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['manufacturer']['name']) ?></td>
            <td><?= htmlspecialchars($row['model']) ?></td>
            <td><?= htmlspecialchars($row['type']) ?></td>
            <td><?= htmlspecialchars($row['cpu']) ?></td>
            <td><?= htmlspecialchars($row['ram_mb']) ?></td>
            <td><?= htmlspecialchars($row['disk_gb']) ?></td>
            <td><?= htmlspecialchars($row['os']['name']) ?></td>
            <td><?= htmlspecialchars($row['domain']) ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td><?= htmlspecialchars($row['building']) ?></td>
            <td><?= htmlspecialchars($row['room']) ?></td>
            <td><?= htmlspecialchars($row['macaddr']) ?></td>
            <td><?= htmlspecialchars($row['purchase_date']) ?></td>
            <td><?= htmlspecialchars($row['warranty_end']) ?></td>
            <td>
                <a class="button-modify" href="dashboard/tech?section=edit-control-unit&serial=<?= htmlspecialchars($row['serial']) ?>">Modifier</a>
                <a class="button-delete" href="dashboard/tech/unite-centrale/supprimer?serial=<?= htmlspecialchars($row['serial']) ?>">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<style>

</style>