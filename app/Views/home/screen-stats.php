<!DOCTYPE html>
<html lang="fr">
<head>
    <base href="/">
    <meta charset="UTF-8">
    <title><?= htmlentities($title ?? 'statistique des écrans') ?></title>


    <link rel="stylesheet" href="css/screens-stats.css">
</head>
<body>
<?php include BASE_INCLUDES_PATH . 'barnav.php' ?>
<?php include BASE_INCLUDES_PATH . 'stats-bar.php' ?>
<div class="container">
    <h1>📊 Statistiques des Moniteurs</h1>

    <div class="stats-grid">

        <!-- Distribution par Fabricant -->
        <div class="card">
            <h2>🏭 Distribution par Fabricant</h2>
            <?php if (!empty($get_manufacturer_distribution)): ?>
                <table>
                    <thead>
                    <tr>
                        <th>Fabricant</th>
                        <th>Nombre</th>
                        <th>Taille Moy.</th>
                        <th>Pourcentage</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($get_manufacturer_distribution as $row): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['id_manufacturer']) ?></strong></td>
                            <td><span class="badge badge-primary"><?= $row['count'] ?></span></td>
                            <td><?= $row['avg_size'] ?>"</td>
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
                <div class="no-data">Aucune donnée disponible</div>
            <?php endif; ?>
        </div>

        <!-- Distribution par Taille -->
        <div class="card">
            <h2>📏 Distribution par Taille</h2>
            <?php if (!empty($get_size_distribution)): ?>
                <table>
                    <thead>
                    <tr>
                        <th>Taille (pouces)</th>
                        <th>Nombre</th>
                        <th>Pourcentage</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($get_size_distribution as $row): ?>
                        <tr>
                            <td><strong><?= $row['size_inch'] ?>"</strong></td>
                            <td><span class="badge badge-primary"><?= $row['count'] ?></span></td>
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
                <div class="no-data">Aucune donnée disponible</div>
            <?php endif; ?>
        </div>

        <!-- Distribution par Résolution -->
        <div class="card">
            <h2>🖼️ Distribution par Résolution</h2>
            <?php if (!empty($get_resolution_distribution)): ?>
                <table>
                    <thead>
                    <tr>
                        <th>Résolution</th>
                        <th>Nombre</th>
                        <th>Pourcentage</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($get_resolution_distribution as $row): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['resolution']) ?></strong></td>
                            <td><span class="badge badge-primary"><?= $row['count'] ?></span></td>
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
                <div class="no-data">Aucune donnée disponible</div>
            <?php endif; ?>
        </div>

        <!-- Distribution par Connecteur -->
        <div class="card">
            <h2>🔌 Distribution par Connecteur</h2>
            <?php if (!empty($get_connector_distribution)): ?>
                <table>
                    <thead>
                    <tr>
                        <th>Type de Connecteur</th>
                        <th>Nombre</th>
                        <th>Pourcentage</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($get_connector_distribution as $row): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['connector']) ?></strong></td>
                            <td><span class="badge badge-primary"><?= $row['count'] ?></span></td>
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
                <div class="no-data">Aucune donnée disponible</div>
            <?php endif; ?>
        </div>

        <!-- Écrans Non Attachés -->
        <div class="card">
            <h2>📦 Écrans Disponibles</h2>
            <?php if (isset($get_unattached_screens)): ?>
                <div class="stat-highlight">
                    <p><strong>Écrans non attachés :</strong>
                        <span class="badge badge-success" style="font-size: 1.2em;">
                                <?= $get_unattached_screens['count'] ?>
                            </span>
                    </p>
                    <div class="progress-bar" style="margin-top: 10px;">
                        <div class="progress-fill" style="width: <?= $get_unattached_screens['percentage'] ?>%">
                            <span><?= $get_unattached_screens['percentage'] ?>% du total</span>
                        </div>
                    </div>
                </div>
                <p style="margin-top: 15px; color: #666; font-size: 0.95em;">
                    Ces écrans sont disponibles pour être assignés à de nouvelles unités centrales.
                </p>
            <?php else: ?>
                <div class="no-data">Aucune donnée disponible</div>
            <?php endif; ?>
        </div>

        <!-- Écrans par Unité Centrale -->
        <div class="card">
            <h2>🖥️ Écrans par Unité Centrale</h2>
            <?php if (!empty($get_screens_per_unit)): ?>
                <table>
                    <thead>
                    <tr>
                        <th>Unité Centrale</th>
                        <th>Nombre d'Écrans</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($get_screens_per_unit as $row): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['attached_to']) ?></strong></td>
                            <td>
                                        <span class="badge <?= $row['screen_count'] > 1 ? 'badge-warning' : 'badge-success' ?>">
                                            <?= $row['screen_count'] ?> écran<?= $row['screen_count'] > 1 ? 's' : '' ?>
                                        </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php
                $multi_screen = array_filter($get_screens_per_unit, function ($row) {
                    return $row['screen_count'] > 1;
                });
                if (!empty($multi_screen)):
                    ?>
                    <div class="stat-highlight" style="margin-top: 15px; border-left-color: #f39c12;">
                        <p>
                            <strong>ℹ️ Information :</strong>
                            <?= count($multi_screen) ?> unité(s) centrale(s)
                            possède(nt) plusieurs écrans
                        </p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-data">Aucune unité centrale n'a d'écran attaché</div>
            <?php endif; ?>
        </div>

    </div>
</div>
</body>
</html>