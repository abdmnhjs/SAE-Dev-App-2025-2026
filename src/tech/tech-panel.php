<?php
session_start();


require '../includes/init.php';
ensureUserAuthorized("tech");
$select = mysqli_select_db($loginToDb, $db); //les variables sont dans connexion_bdd.php, ca marche même si l'éditeur indique que les variables n'existe pas.

$queryControlUnit = "SELECT * FROM control_unit";
$controlUnits = mysqli_query($loginToDb, $queryControlUnit);
$queryScreen = "SELECT * FROM screen";
$screens = mysqli_query($loginToDb, $queryScreen);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Tech Panel</title>
    <link rel="stylesheet" href="../css/tech/tech-panel.css">
</head>
<body>
<div class='sidebar'>
    <div class='sidebar-sections'>
            <a class='sidebar-section' href='../logout.php' class='sections'>Se déconnecter</a>

        <a class='sidebar-section' href='tech-panel.php?section=screens'>Moniteurs</a>
        <a class='sidebar-section' href='tech-panel.php?section=control-units'>Unités de contrôle</a>
            <a class='sidebar-section' href='add-screen-form.php'>Ajouter un écran</a>
    <a class='sidebar-section' href='add-control-unit-form.php'>Ajouter une unité de controle</a>
        <a class="sidebar-section" href="stats.php">Statistiques</a>
        <a class="sidebar-section" href="probas.php">Probabilités</a>

    </div>
</div>


<?php
echo "
<div>
    <h1>Bienvenue tech1</h1>
</div>
";



if (isset($_GET['section']) && $_GET['section'] == "screens") {

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

    $query = "SELECT * FROM `screen` ";
    $screens = mysqli_query($loginToDb, $query);

    while ($row = mysqli_fetch_assoc($screens)) {
        $manufacturerNameQuery = "SELECT name FROM `manufacturer_list` WHERE id = " . $row['id_manufacturer'];
        $manufacturerNameResult = mysqli_query($loginToDb, $manufacturerNameQuery);
        $manufacturerData = mysqli_fetch_assoc($manufacturerNameResult);

        echo "<tr>";
        foreach ($row as $key => $value) {
            if ($key == "id_manufacturer") {
                echo "<td>" . htmlspecialchars($manufacturerData["name"]) . "</td>";
            } else {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }

        }
        echo "<td><a href='edit-screen-form.php?serial=" . htmlspecialchars($row['serial']) . "'>Modifier</a><br>";
        echo "<a href='action-delete-screen.php?delete=" . htmlspecialchars($row['serial']) . "'>Supprimer</a></td>";

        echo "</tr>";
    }
    echo "</table>";

}

if (isset($_GET['section']) && $_GET['section'] == "control-units") {
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
    $query = "SELECT * FROM `control_unit` ";
    $controlUnitsResult = mysqli_query($loginToDb, $query);
    while ($row = mysqli_fetch_assoc($controlUnitsResult)) {
        $manufacturerNameQuery = "SELECT name FROM `manufacturer_list` WHERE id = " . $row['id_manufacturer'];
        $manufacturerNameResult = mysqli_query($loginToDb, $manufacturerNameQuery);
        $manufacturerData = mysqli_fetch_assoc($manufacturerNameResult);

        $osNameQuery = "SELECT name FROM `os_list` WHERE id = " . $row['id_os'];
        $osNameResult = mysqli_query($loginToDb, $osNameQuery);
        $osData = mysqli_fetch_assoc($osNameResult);

        echo "<tr>";
        foreach ($row as $key => $value) {
            if ($key == "id_manufacturer") {
                echo "<td>" . htmlspecialchars($manufacturerData["name"]) . "</td>";
            } elseif ($key == "id_os") {
                echo "<td>" . htmlspecialchars($osData["name"]) . "</td>";
            } else {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
        }
        echo "<td><a href='edit-control-unit-form.php?serial=" . htmlspecialchars($row['serial']) . "'>Modifier</a><br>";
        echo "<a href='action-delete-control-unit.php?delete=" . htmlspecialchars($row['serial']) . "'>Supprimer</a></td>";

        echo "</tr>";
    }
    echo "</table>";
}
?>
</body>
</html>
