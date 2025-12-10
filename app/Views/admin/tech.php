<table>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Mot de passe</th>
        <th>rang</th>
    </tr>

    <?php foreach ($tech as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= md5($row['password']) ?></td>
            <td><?= htmlspecialchars($row['rank']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>