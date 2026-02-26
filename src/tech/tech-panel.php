<?php
session_start();


require '../includes/init.php';
if($_SESSION["role"] !== "tech"){
    header('location: ../index.php');
    exit();
}
$select = mysqli_select_db($loginToDb, $db); //les variables sont dans connexion_bdd.php, ca marche même si l'éditeur indique que les variables n'existe pas.

$queryControlUnit = "SELECT * FROM central_unit";
$controlUnits = mysqli_query($loginToDb, $queryControlUnit);
$queryScreen = "SELECT * FROM screen";
$screens = mysqli_query($loginToDb, $queryScreen);

$sidebarBase = '../';
$sidebarTechPrefix = '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset='UTF-8'>
    <title>Tech Panel</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/tech/tech-panel.css">
</head>
<body class="with-sidebar">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-with-sidebar tech-panel-main">
<?php
echo "<div><h1>Bienvenue " . htmlspecialchars($_SESSION['username'] ?? 'technicien') . "</h1></div>";



if (isset($_GET['section']) && $_GET['section'] == "screens") {
    // Valeurs distinctes pour les filtres
    $manufacturers = mysqli_query($loginToDb, "SELECT id, name FROM manufacturer_list ORDER BY name");
    $modelsScreen = mysqli_query($loginToDb, "SELECT DISTINCT model FROM screen WHERE model IS NOT NULL AND model != '' ORDER BY model");
    $sizes = mysqli_query($loginToDb, "SELECT DISTINCT size_inch FROM screen WHERE size_inch IS NOT NULL ORDER BY size_inch");
    $resolutions = mysqli_query($loginToDb, "SELECT DISTINCT resolution FROM screen WHERE resolution IS NOT NULL AND resolution != '' ORDER BY resolution");
    $connectors = mysqli_query($loginToDb, "SELECT DISTINCT connector FROM screen WHERE connector IS NOT NULL AND connector != '' ORDER BY connector");
    $attachedTo = mysqli_query($loginToDb, "SELECT DISTINCT attached_to FROM screen WHERE attached_to IS NOT NULL AND attached_to != '' ORDER BY attached_to");

    echo "<div class=\"filters-panel\">";
    echo "<form method=\"get\" action=\"tech-panel.php\" class=\"filters-form\">";
    echo "<input type=\"hidden\" name=\"section\" value=\"screens\">";

    echo "<label>Fabricant <select name=\"filter_manufacturer\"><option value=\"\">— Tous</option>";
    while ($r = mysqli_fetch_assoc($manufacturers)) {
        $sel = (isset($_GET['filter_manufacturer']) && $_GET['filter_manufacturer'] === (string)$r['id']) ? ' selected' : '';
        echo "<option value=\"" . (int)$r['id'] . "\"{$sel}>" . htmlspecialchars($r['name']) . "</option>";
    }
    echo "</select></label>";

    echo "<label>Modèle <select name=\"filter_model\"><option value=\"\">— Tous</option>";
    while ($r = mysqli_fetch_assoc($modelsScreen)) {
        $sel = (isset($_GET['filter_model']) && $_GET['filter_model'] === $r['model']) ? ' selected' : '';
        echo "<option value=\"" . htmlspecialchars($r['model']) . "\"{$sel}>" . htmlspecialchars($r['model']) . "</option>";
    }
    echo "</select></label>";

    echo "<label>Taille <select name=\"filter_size\"><option value=\"\">— Tous</option>";
    while ($r = mysqli_fetch_assoc($sizes)) {
        $v = $r['size_inch'];
        $sel = (isset($_GET['filter_size']) && $_GET['filter_size'] === (string)$v) ? ' selected' : '';
        echo "<option value=\"" . htmlspecialchars($v) . "\"{$sel}>" . htmlspecialchars($v) . "</option>";
    }
    echo "</select></label>";

    echo "<label>Résolution <select name=\"filter_resolution\"><option value=\"\">— Tous</option>";
    while ($r = mysqli_fetch_assoc($resolutions)) {
        $sel = (isset($_GET['filter_resolution']) && $_GET['filter_resolution'] === $r['resolution']) ? ' selected' : '';
        echo "<option value=\"" . htmlspecialchars($r['resolution']) . "\"{$sel}>" . htmlspecialchars($r['resolution']) . "</option>";
    }
    echo "</select></label>";

    echo "<label>Connecteur <select name=\"filter_connector\"><option value=\"\">— Tous</option>";
    while ($r = mysqli_fetch_assoc($connectors)) {
        $sel = (isset($_GET['filter_connector']) && $_GET['filter_connector'] === $r['connector']) ? ' selected' : '';
        echo "<option value=\"" . htmlspecialchars($r['connector']) . "\"{$sel}>" . htmlspecialchars($r['connector']) . "</option>";
    }
    echo "</select></label>";

    echo "<label>Attaché à <select name=\"filter_attached_to\"><option value=\"\">— Tous</option>";
    while ($r = mysqli_fetch_assoc($attachedTo)) {
        $sel = (isset($_GET['filter_attached_to']) && $_GET['filter_attached_to'] === $r['attached_to']) ? ' selected' : '';
        echo "<option value=\"" . htmlspecialchars($r['attached_to']) . "\"{$sel}>" . htmlspecialchars($r['attached_to']) . "</option>";
    }
    echo "</select></label>";

    echo "<button type=\"submit\">Filtrer</button>";
    echo "</form>";
    echo "<a href=\"tech-panel.php?section=screens\" class=\"filters-reset\">Réinitialiser</a>";
    echo "</div>";

    // Construction du WHERE (valeurs issues des listes, échappées)
    $where = [];
    if (!empty($_GET['filter_manufacturer'])) {
        $where[] = "s.id_manufacturer = " . (int)$_GET['filter_manufacturer'];
    }
    if (!empty($_GET['filter_model'])) {
        $where[] = "s.model = '" . mysqli_real_escape_string($loginToDb, $_GET['filter_model']) . "'";
    }
    if (isset($_GET['filter_size']) && $_GET['filter_size'] !== '') {
        $where[] = "s.size_inch = '" . mysqli_real_escape_string($loginToDb, $_GET['filter_size']) . "'";
    }
    if (!empty($_GET['filter_resolution'])) {
        $where[] = "s.resolution = '" . mysqli_real_escape_string($loginToDb, $_GET['filter_resolution']) . "'";
    }
    if (!empty($_GET['filter_connector'])) {
        $where[] = "s.connector = '" . mysqli_real_escape_string($loginToDb, $_GET['filter_connector']) . "'";
    }
    if (!empty($_GET['filter_attached_to'])) {
        $where[] = "s.attached_to = '" . mysqli_real_escape_string($loginToDb, $_GET['filter_attached_to']) . "'";
    }
    $sql = "SELECT s.* FROM screen s";
    if (count($where) > 0) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY s.serial";
    $screens = mysqli_query($loginToDb, $sql);

    echo "<table>
                <tr>
                    <th>Numéro de série</th>
                    <th>Fabricant</th>
                    <th>Modèle</th>
                    <th>Taille (pouces)</th>
                    <th>Résolution</th>
                    <th>Connecteur</th>
                    <th>Attaché à</th>
                    <th>Actions</th>
                </tr>";

    while ($row = mysqli_fetch_assoc($screens)) {
        $manufacturerNameQuery = "SELECT name FROM `manufacturer_list` WHERE id = " . (int)$row['id_manufacturer'];
        $manufacturerNameResult = mysqli_query($loginToDb, $manufacturerNameQuery);
        $manufacturerData = $manufacturerNameResult ? mysqli_fetch_assoc($manufacturerNameResult) : null;

        echo "<tr>";
        foreach ($row as $key => $value) {
            if ($key == "id_manufacturer") {
                echo "<td>" . ($manufacturerData ? htmlspecialchars($manufacturerData["name"]) : '—') . "</td>";
            } elseif ($key == "is_active") {
                continue;
            } else {
                echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
            }
        }
        echo "<td><a href='edit-screen-form.php?serial=" . htmlspecialchars($row['serial']) . "'>Modifier</a><br>";
        echo "<a href='action-delete-screen.php?delete=" . htmlspecialchars($row['serial']) . "'>Supprimer</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}

if (isset($_GET['section']) && $_GET['section'] == "central-units") {
    // Valeurs distinctes pour les filtres
    $manufacturersCu = mysqli_query($loginToDb, "SELECT id, name FROM manufacturer_list ORDER BY name");
    $modelsCu = mysqli_query($loginToDb, "SELECT DISTINCT model FROM central_unit WHERE model IS NOT NULL AND model != '' ORDER BY model");
    $typesCu = mysqli_query($loginToDb, "SELECT DISTINCT type FROM central_unit WHERE type IS NOT NULL AND type != '' ORDER BY type");
    $osList = mysqli_query($loginToDb, "SELECT id, name FROM os_list ORDER BY name");
    $locations = mysqli_query($loginToDb, "SELECT DISTINCT location FROM central_unit WHERE location IS NOT NULL AND location != '' ORDER BY location");
    $buildings = mysqli_query($loginToDb, "SELECT DISTINCT building FROM central_unit WHERE building IS NOT NULL AND building != '' ORDER BY building");
    $rooms = mysqli_query($loginToDb, "SELECT DISTINCT room FROM central_unit WHERE room IS NOT NULL AND room != '' ORDER BY room");

    echo "<div class=\"filters-panel\">";
    echo "<form method=\"get\" action=\"tech-panel.php\" class=\"filters-form\">";
    echo "<input type=\"hidden\" name=\"section\" value=\"central-units\">";

    echo "<label>Fabricant <select name=\"filter_manufacturer\"><option value=\"\">— Tous</option>";
    while ($r = mysqli_fetch_assoc($manufacturersCu)) {
        $sel = (isset($_GET['filter_manufacturer']) && $_GET['filter_manufacturer'] === (string)$r['id']) ? ' selected' : '';
        echo "<option value=\"" . (int)$r['id'] . "\"{$sel}>" . htmlspecialchars($r['name']) . "</option>";
    }
    echo "</select></label>";

    echo "<label>Modèle <select name=\"filter_model\"><option value=\"\">— Tous</option>";
    while ($r = mysqli_fetch_assoc($modelsCu)) {
        $sel = (isset($_GET['filter_model']) && $_GET['filter_model'] === $r['model']) ? ' selected' : '';
        echo "<option value=\"" . htmlspecialchars($r['model']) . "\"{$sel}>" . htmlspecialchars($r['model']) . "</option>";
    }
    echo "</select></label>";

    echo "<label>Type <select name=\"filter_type\"><option value=\"\">— Tous</option>";
    while ($r = mysqli_fetch_assoc($typesCu)) {
        $sel = (isset($_GET['filter_type']) && $_GET['filter_type'] === $r['type']) ? ' selected' : '';
        echo "<option value=\"" . htmlspecialchars($r['type']) . "\"{$sel}>" . htmlspecialchars($r['type']) . "</option>";
    }
    echo "</select></label>";

    echo "<label>OS <select name=\"filter_os\"><option value=\"\">— Tous</option>";
    while ($r = mysqli_fetch_assoc($osList)) {
        $sel = (isset($_GET['filter_os']) && $_GET['filter_os'] === (string)$r['id']) ? ' selected' : '';
        echo "<option value=\"" . (int)$r['id'] . "\"{$sel}>" . htmlspecialchars($r['name']) . "</option>";
    }
    echo "</select></label>";

    echo "<label>Localisation <select name=\"filter_location\"><option value=\"\">— Tous</option>";
    while ($r = mysqli_fetch_assoc($locations)) {
        $sel = (isset($_GET['filter_location']) && $_GET['filter_location'] === $r['location']) ? ' selected' : '';
        echo "<option value=\"" . htmlspecialchars($r['location']) . "\"{$sel}>" . htmlspecialchars($r['location']) . "</option>";
    }
    echo "</select></label>";

    echo "<label>Bâtiment <select name=\"filter_building\"><option value=\"\">— Tous</option>";
    while ($r = mysqli_fetch_assoc($buildings)) {
        $sel = (isset($_GET['filter_building']) && $_GET['filter_building'] === $r['building']) ? ' selected' : '';
        echo "<option value=\"" . htmlspecialchars($r['building']) . "\"{$sel}>" . htmlspecialchars($r['building']) . "</option>";
    }
    echo "</select></label>";

    echo "<label>Salle <select name=\"filter_room\"><option value=\"\">— Tous</option>";
    while ($r = mysqli_fetch_assoc($rooms)) {
        $sel = (isset($_GET['filter_room']) && $_GET['filter_room'] === $r['room']) ? ' selected' : '';
        echo "<option value=\"" . htmlspecialchars($r['room']) . "\"{$sel}>" . htmlspecialchars($r['room']) . "</option>";
    }
    echo "</select></label>";

    echo "<button type=\"submit\">Filtrer</button>";
    echo "</form>";
    echo "<a href=\"tech-panel.php?section=central-units\" class=\"filters-reset\">Réinitialiser</a>";
    echo "</div>";

    // Construction du WHERE
    $whereCu = [];
    if (!empty($_GET['filter_manufacturer'])) {
        $whereCu[] = "cu.id_manufacturer = " . (int)$_GET['filter_manufacturer'];
    }
    if (!empty($_GET['filter_model'])) {
        $whereCu[] = "cu.model = '" . mysqli_real_escape_string($loginToDb, $_GET['filter_model']) . "'";
    }
    if (!empty($_GET['filter_type'])) {
        $whereCu[] = "cu.type = '" . mysqli_real_escape_string($loginToDb, $_GET['filter_type']) . "'";
    }
    if (!empty($_GET['filter_os'])) {
        $whereCu[] = "cu.id_os = " . (int)$_GET['filter_os'];
    }
    if (!empty($_GET['filter_location'])) {
        $whereCu[] = "cu.location = '" . mysqli_real_escape_string($loginToDb, $_GET['filter_location']) . "'";
    }
    if (!empty($_GET['filter_building'])) {
        $whereCu[] = "cu.building = '" . mysqli_real_escape_string($loginToDb, $_GET['filter_building']) . "'";
    }
    if (!empty($_GET['filter_room'])) {
        $whereCu[] = "cu.room = '" . mysqli_real_escape_string($loginToDb, $_GET['filter_room']) . "'";
    }
    $query = "SELECT cu.* FROM central_unit cu";
    if (count($whereCu) > 0) {
        $query .= " WHERE " . implode(" AND ", $whereCu);
    }
    $query .= " ORDER BY cu.name";

    $controlUnitsResult = mysqli_query($loginToDb, $query);
    echo "<table>
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
                    </tr>";
    while ($row = mysqli_fetch_assoc($controlUnitsResult)) {
        $manufacturerNameQuery = "SELECT name FROM `manufacturer_list` WHERE id = " . $row['id_manufacturer'];
        $manufacturerNameResult = mysqli_query($loginToDb, $manufacturerNameQuery);
        $manufacturerData = mysqli_fetch_assoc($manufacturerNameResult);

        $osNameQuery = "SELECT name FROM `os_list` WHERE id = " . $row['id_os'];
        $osNameResult = mysqli_query($loginToDb, $osNameQuery);
        $osData = mysqli_fetch_assoc($osNameResult);

        echo "<tr>";
        foreach ($row as $key => $value) {
            if ($key == "is_active") continue;
            if ($key == "id_manufacturer") {
                echo "<td>" . htmlspecialchars($manufacturerData["name"]) . "</td>";
            } elseif ($key == "id_os") {
                echo "<td>" . htmlspecialchars($osData["name"]) . "</td>";
            } else {
                echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
            }
        }
        echo "<td><a href='edit-central-unit-form.php?serial=" . htmlspecialchars($row['serial']) . "'>Modifier</a><br>";
        echo "<a href='action-delete-central-unit.php?delete=" . htmlspecialchars($row['serial']) . "'>Supprimer</a></td>";

        echo "</tr>";
    }
    echo "</table>";
}
?>
</main>
</body>
</html>
