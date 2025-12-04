<?php
session_start();

$host = 'localhost';
$user = 'root';
$db_password = ""; //penser a le changer si vous faites des tests en locaux, le mdp du rpi12 est : !sae2025!
$db = "infra";
$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if(!$loginToDb){
    die("Erreur de connexion à la db: " . mysqli_connect_error());
}

$select = mysqli_select_db($loginToDb, $db);
if (!$select) {
    die("Erreur");
} else {
    $queryControlUnit = "SELECT * FROM control_unit";
    $controlUnits = mysqli_query($loginToDb, $queryControlUnit);
    $queryScreen = "SELECT * FROM screen";
    $screens = mysqli_query($loginToDb, $queryScreen);
    if (isset($_SESSION['username']) && $_SESSION['username'] !== "adminweb" && $_SESSION['username'] !== "sysadmin") {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Tech Panel</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Geist:wght@100;200;300;400;500;600;700;800;900&display=swap');

                /* Container de la sidebar */
                .sidebar {
                    position: fixed;
                    left: 0;
                    top: 0;
                    height: 100vh;
                    width: 250px;
                    background-color: #ffffff;
                    padding: 20px 0;
                    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
                    overflow-y: auto;
                }

                body {
                    background-color: #121212;
                    margin-left: 250px;
font-family: 'Geist', sans-serif                }

                * {
                    color: white;
                }

                .sidebar a {
                    color: black;
                }

                /* Container des sections */
                .sidebar-sections {
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                    padding: 0 15px;
                }

                /* Style des liens de section */
                .sidebar-section {
                    display: block;
                    padding: 15px 20px;
                    color: #ecf0f1;
                    text-decoration: none;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                    cursor: pointer;
                    font-size: 16px;
                }

                /* Effet au survol */
                .sidebar-section:hover {
                    color: #fff;
                    background-color: #121212;
                    transform: translateX(5px);
                }

                /* Section active (optionnel) */
                .sidebar-section.active {
                    background-color: #3498db;
                    font-weight: bold;
                }

                /* Style pour les tableaux */
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }

                table th, table td {
                    padding: 12px;
                    text-align: left;
                    border: 1px solid #444;
                }

                table th {
                    background-color: #333;
                    font-weight: bold;
                }

                table tr:hover {
                    background-color: #1a1a1a;
                }
            </style>
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
        if(isset($_GET['section']) && $_GET['section'] == "screens"){

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
                while($row = mysqli_fetch_assoc($screens)){
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['serial']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['manufacturer']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['model']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['size_inch']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['resolution']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['connector']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['attached_to']) . "</td>";
                    echo "<td>";
                    echo "<a href='edit-screen-form.php?serial=". htmlspecialchars($row['serial']) ."'>Modifier</a> ";
                    echo "<a href='#'>Supprimer</a>";
                    echo "</td>";
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
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['serial']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['manufacturer']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['model']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['cpu']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ram_mb']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['disk_gb']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['os']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['domain']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['building']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['room']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['macaddr']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['purchase_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['warranty_end']) . "</td>";
                    echo "<td>";
                    echo "<a href='edit-control-unit-form.php?serial=". htmlspecialchars($row['serial']) ."'>Modifier</a> ";
                    echo "<a href='#'>Supprimer</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        ?>
        </body>
        </html>
        <?php
}
?>