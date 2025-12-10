<table>
    <tr>
        <th>Numéro de série</th>
        <th>Fabricant</th>
        <th>Modèle</th>
        <th>Taille (pouces)</th>
        <th>Résolution</th>
        <th>Connecteur</th>
        <th>Attaché à</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($screens as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['serial']) ?></td>
            <td><?= htmlspecialchars($row['manufacturer']['name']) ?></td>
            <td><?= htmlspecialchars($row['model']) ?></td>
            <td><?= htmlspecialchars($row['size_inch']) ?></td>
            <td><?= htmlspecialchars($row['resolution']) ?></td>
            <td><?= htmlspecialchars($row['connector']) ?></td>
            <td><?= htmlspecialchars($row['attached_to']) ?></td>
            <td>
                <a class="button-modify" href="dashboard/tech?section=edit-screen&serial=<?= htmlspecialchars($row['serial']) ?>">Modifier</a>
                <a class="button-delete" href="dashboard/tech/ecran/supprimer?serial=<?= htmlspecialchars($row['serial']) ?>">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>