<!DOCTYPE html>
<html lang="fr">
<head>
    <base href="/">
    <meta charset="UTF-8">
    <title><?= htmlentities($title ?? 'statistique des unités centrales') ?></title>


    <link rel="stylesheet" href="css/units-stats.css">
</head>
<body>
<?php include BASE_INCLUDES_PATH . 'barnav.php' ?>
<?php include BASE_INCLUDES_PATH . 'stats-bar.php' ?>
<div class="container">
    <h1>📊 Statistiques des Unités Centrales</h1>

    <!-- Âge moyen du parc -->
    <?php if ($get_average_age): ?>
        <div class="info-box">
            <h2>🕒 Âge du Parc Informatique</h2>
            <p><strong>Âge moyen :</strong>
                <?= !empty($get_average_age['avg_age_years']) || $get_average_age['avg_age_years'] === "0"
                    ? $get_average_age['avg_age_years'] . ' ans'
                    : 'N/A' ?>
            </p>
            <p><strong>Plus ancien achat :</strong>
                <?= !empty($get_average_age['oldest_purchase']) && strtotime($get_average_age['oldest_purchase'])
                    ? date('d/m/Y', strtotime($get_average_age['oldest_purchase']))
                    : 'N/A' ?>
            </p>
            <p><strong>Achat le plus récent :</strong>
                <?= !empty($get_average_age['newest_purchase']) && strtotime($get_average_age['newest_purchase'])
                    ? date('d/m/Y', strtotime($get_average_age['newest_purchase']))
                    : 'N/A' ?>
            </p>
            <p><strong>Total avec date :</strong> <?= $get_average_age['total_with_date'] ?> unités</p>
        </div>
    <?php endif; ?>

    <div class="stats-grid">

        <!-- Distribution par type -->
        <div class="stat-card">
            <h2>💾 Distribution par Type</h2>
            <?php if (!empty($get_type_distribution)): ?>
                <table>
                    <thead>
                    <tr>
                        <th>Type</th>
                        <th>Nombre</th>
                        <th>Pourcentage</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($get_type_distribution as $row): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['type']) ?></strong></td>
                            <td><?= $row['count'] ?></td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $row['percentage'] ?>%"></div>
                                    <span><?= $row['percentage'] ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>

        <!-- Distribution par fabricant -->
        <div class="stat-card">
            <h2>🏭 Distribution par Fabricant</h2>
            <?php if (!empty($get_manufacturer_distribution)): ?>
                <table>
                    <thead>
                    <tr>
                        <th>Fabricant</th>
                        <th>Nombre</th>
                        <th>Pourcentage</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($get_manufacturer_distribution as $row): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['id_manufacturer']) ?></strong></td>
                            <td><?= $row['count'] ?></td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $row['percentage'] ?>%"></div>
                                    <span><?= $row['percentage'] ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>

        <!-- Distribution par OS -->
        <div class="stat-card">
            <h2>🖥️ Distribution par Système d'Exploitation</h2>
            <?php if (!empty($get_os_distribution)): ?>
                <table>
                    <thead>
                    <tr>
                        <th>OS</th>
                        <th>Nombre</th>
                        <th>Pourcentage</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($get_os_distribution as $row): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['id_os']) ?></strong></td>
                            <td><?= $row['count'] ?></td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $row['percentage'] ?>%"></div>
                                    <span><?= $row['percentage'] ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>

        <!-- Statut des garanties -->
        <div class="stat-card">
            <h2>🛡️ Statut des Garanties</h2>
            <?php if (!empty($get_warranty_status)): ?>
                <table>
                    <thead>
                    <tr>
                        <th>Statut</th>
                        <th>Nombre</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($get_warranty_status as $row): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($row['status']) ?></strong>
                                <?php if ($row['status'] === 'Expirée'): ?>
                                    <span class="badge badge-danger">Attention</span>
                                <?php elseif ($row['status'] === 'Expire bientôt'): ?>
                                    <span class="badge badge-warning">À surveiller</span>
                                <?php else: ?>
                                    <span class="badge badge-success">OK</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>

    </div>

    <!-- Statistiques RAM -->
    <div class="stat-card" style="margin-bottom: 20px;">
        <h2>🧠 Statistiques RAM</h2>
        <?php if (!empty($get_ram_statistics)): ?>
            <table>
                <thead>
                <tr>
                    <th>Plage de RAM</th>
                    <th>Nombre d'unités</th>
                    <th>Distribution</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $total = array_sum(array_column($get_ram_statistics, 'count'));
                foreach ($get_ram_statistics as $row):
                    $percentage = round(($row['count'] / $total) * 100, 2);
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['ram_range']) ?></strong></td>
                        <td><?= $row['count'] ?></td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                                <span><?= $percentage ?>%</span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">Aucune donnée disponible</p>
        <?php endif; ?>
    </div>

    <!-- Statistiques Disque -->
    <div class="stat-card" style="margin-bottom: 20px;">
        <h2>💿 Statistiques Disque Dur</h2>
        <?php if (!empty($get_disk_statistics)): ?>
            <table>
                <thead>
                <tr>
                    <th>Plage de Disque</th>
                    <th>Nombre d'unités</th>
                    <th>Pourcentage</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($get_disk_statistics as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['disk_range']) ?></strong></td>
                        <td><?= $row['count'] ?></td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $row['percentage'] ?>%"></div>
                                <span><?= $row['percentage'] ?>%</span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">Aucune donnée disponible</p>
        <?php endif; ?>
    </div>

    <!-- Distribution par localisation -->
    <div class="stat-card">
        <h2>📍 Distribution par Localisation</h2>
        <?php if (!empty($get_location_distribution)): ?>
            <table>
                <thead>
                <tr>
                    <th>Localisation</th>
                    <th>Bâtiment</th>
                    <th>Nombre d'unités</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($get_location_distribution as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['location']) ?></strong></td>
                        <td><?= htmlspecialchars($row['building']) ?></td>
                        <td><?= $row['count'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">Aucune donnée disponible</p>
        <?php endif; ?>
    </div>

</div>
</body>
</html>