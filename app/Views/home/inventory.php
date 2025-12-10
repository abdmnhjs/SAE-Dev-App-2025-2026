<!DOCTYPE html>
<html lang="fr">
<head>
    <base href="/rpi12/">
    <meta charset="UTF-8">
    <title><?= htmlentities($title ?? 'Inventaire') ?></title>


    <link rel="stylesheet" href="css/inventory.css">
</head>
<body>
<?php include BASE_INCLUDES_PATH . 'barnav.php' ?>
<?php include BASE_INCLUDES_PATH . 'stats-bar.php' ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Connexions</title>
<body>
    <div class="container">
        <h1>📊 Statistiques des Connexions</h1>

        <!-- Statistiques générales -->
        <div class="stats-grid">
            <div class="card stat-box">
                <div class="stat-value"><?php echo number_format($get_total_connections); ?></div>
                <div class="stat-label">Connexions totales</div>
            </div>

            <div class="card stat-box">
                <div class="stat-value">
                    <?php
                    $hours = floor($get_average_session_duration / 3600);
                    $minutes = floor(($get_average_session_duration % 3600) / 60);
                    $seconds = $get_average_session_duration % 60;

                    if ($hours > 0) {
                        echo $hours . 'h ' . $minutes . 'min';
                    } elseif ($minutes > 0) {
                        echo $minutes . 'min ' . $seconds . 's';
                    } else {
                        echo $seconds . 's';
                    }
                    ?>
                </div>
                <div class="stat-label">Durée moyenne</div>
            </div>
        </div>

        <!-- Distribution des durées de session -->
        <div class="card">
            <h2>📈 Distribution des durées de session</h2>
            <table>
                <thead>
                    <tr>
                        <th>Plage de durée</th>
                        <th>Nombre</th>
                        <th style="width: 50%;">Pourcentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($get_session_duration_distribution as $row): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['duration_range']); ?></strong></td>
                        <td><?php echo number_format($row['count']); ?></td>
                        <td>
                            <div class="percentage-bar">
                                <div class="percentage-fill" style="width: <?php echo $row['percentage']; ?>%;"></div>
                                <span class="percentage-text"><?php echo $row['percentage']; ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Sessions anormalement courtes -->
        <div class="card" style="margin-top: 20px;">
            <h2>⚠️ Sessions anormalement courtes (< 1 minute)</h2>

            <?php if (count($get_anomalously_short_sessions) > 0): ?>
                <div class="alert alert-warning">
                    <strong><?php echo count($get_anomalously_short_sessions); ?></strong> sessions très courtes détectées
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Adresse IP</th>
                            <th>Durée</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $displayLimit = 20;
                        $sessions = array_slice($get_anomalously_short_sessions, 0, $displayLimit);
                        foreach ($sessions as $session):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($session['login']); ?></td>
                            <td><?php echo htmlspecialchars($session['ip_address']); ?></td>
                            <td>
                                <span class="duration">
                                    <?php echo $session['duration_seconds']; ?>s
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (count($get_anomalously_short_sessions) > $displayLimit): ?>
                    <p style="text-align: center; margin-top: 10px; color: #666;">
                        ... et <?php echo count($get_anomalously_short_sessions) - $displayLimit; ?> autres sessions
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <p style="color: #666;">Aucune session anormalement courte détectée.</p>
            <?php endif; ?>
        </div>

        <!-- Sessions anormalement longues -->
        <div class="card" style="margin-top: 20px;">
            <h2>🔴 Sessions anormalement longues (> 8 heures)</h2>

            <?php if (count($get_anomalously_long_sessions) > 0): ?>
                <div class="alert alert-danger">
                    <strong><?php echo count($get_anomalously_long_sessions); ?></strong> sessions très longues détectées
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Adresse IP</th>
                            <th>Durée</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $displayLimit = 20;
                        $sessions = array_slice($get_anomalously_long_sessions, 0, $displayLimit);
                        foreach ($sessions as $session):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($session['login']); ?></td>
                            <td><?php echo htmlspecialchars($session['ip_address']); ?></td>
                            <td>
                                <span class="duration">
                                    <?php
                                    $hours = floor($session['duration_seconds'] / 3600);
                                    $minutes = floor(($session['duration_seconds'] % 3600) / 60);
                                    echo $hours . 'h ' . $minutes . 'min';
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (count($get_anomalously_long_sessions) > $displayLimit): ?>
                    <p style="text-align: center; margin-top: 10px; color: #666;">
                        ... et <?php echo count($get_anomalously_long_sessions) - $displayLimit; ?> autres sessions
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <p style="color: #666;">Aucune session anormalement longue détectée.</p>
            <?php endif; ?>
        </div>
    </div>


