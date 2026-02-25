<?php
session_start();

require '../includes/init.php';
if ($_SESSION["role"] !== "adminweb") {
    header('location: ../index.php');
    exit();
}
$sidebarBase = '../';
$sidebarAdminPrefix = '';

// Récupérer tous les techniciens (rôle tech)
$techList = [];
$sql = "SELECT name FROM users WHERE role = 'tech' ORDER BY name ASC";
$result = mysqli_query($loginToDb, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $techList[] = $row['name'];
    }
}

$errorMessage = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'empty_fields':
            $errorMessage = 'Tous les champs sont obligatoires.';
            break;
        case 'password_mismatch':
            $errorMessage = 'Les deux mots de passe ne correspondent pas.';
            break;
        case 'user_exists':
            $errorMessage = 'Un technicien avec ce nom existe déjà.';
            break;
        default:
            $errorMessage = 'Une erreur est survenue.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Créer un technicien</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/adminweb.css">
</head>
<body class="with-sidebar">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-with-sidebar">
    <section class="admin-section">
        <h2>Créer un technicien</h2>
        <?php if ($errorMessage !== '') : ?>
            <p class="form-error" role="alert"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
        <form method="post" action="actions/action-create-tech.php" id="create-tech-form">
            <fieldset>
                <legend>Nouveau technicien</legend>

                <div class="form-group">
                    <label for="create-tech-username">Nom d'utilisateur</label>
                    <input type="text" name="username" id="create-tech-username" required autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="create-tech-password">Mot de passe</label>
                    <input type="password" name="password" id="create-tech-password" required autocomplete="new-password">
                </div>

                <div class="form-group">
                    <label for="create-tech-password-confirm">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirm" id="create-tech-password-confirm" required autocomplete="new-password">
                </div>

                <div class="form-group form-actions">
                    <button type="submit" name="submit">Créer le technicien</button>
                </div>
            </fieldset>
        </form>
    </section>

    <section class="admin-section admin-section-tech-list">
        <h2>Techniciens existants</h2>
        <?php if (count($techList) === 0) : ?>
            <p>Aucun technicien enregistré.</p>
        <?php else : ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nom d'utilisateur</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($techList as $techName) : ?>
                        <tr>
                            <td><?= htmlspecialchars($techName) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
