<?php
session_start();

require '../includes/init.php';
require 'LogsSAE.php';

if ($_SESSION["role"] !== "sysadmin") {
    header('Location: ../index.php');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$sidebarBase = '../';
$sidebarSysadminPrefix = '';

$logsJson = new LogsSAE();

$DOSSIER_AUTORISE_FAILS = '/var/www/html/SAE-Dev-App-2025-2026/json-logs/fails-logs';
$DOSSIER_AUTORISE_SUCCESS = '/var/www/html/SAE-Dev-App-2025-2026/json-logs/success-logs';
$resultat = null;
$output = [];

// ── POST : suppression ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fichier'])) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('Requête invalide.');
    }

    // On choisit le dossier autorisé selon le type passé en POST
    $type_post = $_POST['type'] ?? '';
    if ($type_post === 'success') {
        $dossier_post = $DOSSIER_AUTORISE_SUCCESS;
    } elseif ($type_post === 'fails') {
        $dossier_post = $DOSSIER_AUTORISE_FAILS;
    } else {
        die('Type invalide.');
    }

    $resultat = $logsJson->supprimerFichierSecurise($_POST['fichier'], $dossier_post);

    // Redirection pour éviter la re-soumission du formulaire (POST → GET)
    $redirect_type = urlencode($type_post);
    header("Location: gestion-logs.php?type=$redirect_type");
    exit();
}

// ── GET : affichage ───────────────────────────────────────────────────────────
// Valeurs autorisées uniquement (whitelist)
$types_autorises = ['success', 'fails'];
$type_get = $_GET['type'] ?? 'success';

if (!in_array($type_get, $types_autorises, true)) {
    $type_get = 'success';
}

$dossier_actif = ($type_get === 'success') ? $DOSSIER_AUTORISE_SUCCESS : $DOSSIER_AUTORISE_FAILS;
$output = $logsJson->allLogsFrom($dossier_actif);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Journaux d'activité — Sysadmin</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/adminweb.css">
    <link rel="stylesheet" href="../css/sysadmin.css">
</head>
<body class="with-sidebar">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>

<main class="main-with-sidebar sysadmin-logs-main" style="gap: 0">
    <h1>Journaux d'activité — Logs JSON</h1>

    <!-- Filtre type de log -->
    <div class="filters-panel">
        <form method="GET" action="gestion-logs.php" class="filters-form">
            <label for="type">Type de logs :</label>
            <select name="type" id="type" >
                <option style="color: black;" value="success" <?php echo $type_get === 'success' ? 'selected' : ''; ?>>
                    Succès
                </option>
                <option style="color: black;" value="fails" <?php echo $type_get === 'fails' ? 'selected' : ''; ?>>
                    Échecs
                </option>
            </select>
            <button type="submit">Filtrer</button>
        </form>
    </div>

    <!-- Résultat suppression -->
    <?php if ($resultat !== null): ?>
        <div class="alert <?php echo $resultat['ok'] ? 'alert-success' : 'alert-error'; ?>">
            <?php echo htmlspecialchars($resultat['message'] ?? $resultat['erreur']); ?>
        </div>
    <?php endif; ?>

    <!-- Tableau des fichiers -->
    <div class="logs-table-wrap">
        <h2>
            Logs — <?php echo $type_get === 'success' ? 'Succès' : 'Échecs'; ?>
            <small>(<?php echo count($output); ?> fichier(s))</small>
        </h2>

        <?php if (empty($output)): ?>
            <p>Aucun fichier trouvé.</p>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>Fichiers</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($output as $log): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['log_name']); ?>

                            <form method="POST" action="gestion-logs.php" style="margin:0; padding:0; display:inline"
                                  onsubmit="return confirm('Supprimer <?php echo htmlspecialchars(addslashes($log['log_name'])); ?> ?')">
                                <input type="hidden" name="csrf_token"
                                       value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <!-- Chemin complet pour supprimerFichierSecurise() -->
                                <input type="hidden" name="fichier"
                                       value="<?php echo htmlspecialchars($log['chemin_complet']); ?>">
                                <!-- Type pour choisir le bon dossier autorisé côté POST -->
                                <input type="hidden" name="type"
                                       value="<?php echo htmlspecialchars($type_get); ?>">
                                <button type="submit" style="background-color: red">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</main>
</body>
</html>