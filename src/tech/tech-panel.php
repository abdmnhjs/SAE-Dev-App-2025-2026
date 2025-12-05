<?php
session_start();


require "../includes/connexion_bdd.php";

$select = mysqli_select_db($loginToDb, $db); //les variables sont dans connexion_bdd.php, ca marche même si l'éditeur indique que les variables n'existe pas.
if (!$select) {
    die("Erreur");
}

$queryControlUnit = "SELECT * FROM control_unit";
$controlUnits = mysqli_query($loginToDb, $queryControlUnit);
$queryScreen = "SELECT * FROM screen";
$screens = mysqli_query($loginToDb, $queryScreen);
if (isset($_SESSION['username'])) {
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
        <a class='sidebar-section' href='tech-panel.php?section=screens'>Moniteurs</a>
        <a class='sidebar-section' href='tech-panel.php?section=control-units'>Unités de contrôle</a>
    </div>
</div>

<div>
    <h1>Ici c le tech panel</h1>
    <a href='../logout.php' class='sections'>Se déconnecter</a>
    <a href='add-screen-form.php'>Ajouter un écran</a>
    <a href='add-control-unit-form.php'>Ajouter une unité de controle</a>
</div>
<?php
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
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "<td><a href='edit-screen-form.php?serial=" . htmlspecialchars($row['serial']) . "'>Modifier</a><br>";
        echo "<a href='#'>Supprimer</a></td>";
        echo "</tr>";
    }
    echo "</table>";

} else if (isset($_GET['section']) && $_GET['section'] == "control-units") {

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
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "<td><a href='edit-screen-form.php?serial=" . htmlspecialchars($row['serial']) . "'>Modifier</a><br>";
        echo "<a href='#'>Supprimer</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}
}
?>
</body>
</html>
