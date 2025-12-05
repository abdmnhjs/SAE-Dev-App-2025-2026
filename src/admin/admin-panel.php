<?php
session_start();

// --- Configuration et Connexion à la Base de Données ---
$host = 'localhost';
$user = 'root';
$db_password = ""; // À changer pour les tests en local
$db = "infra";

$loginToDb = mysqli_connect($host, $user, $db_password, $db);

if (!$loginToDb) {
    die("Erreur de connexion à la db: " . mysqli_connect_error());
}

// Vérifie si l'utilisateur est l'administrateur
if (isset($_SESSION['username']) && $_SESSION['username'] === "adminweb") {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Admin Panel</title>
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
            <a class='sidebar-section' href='admin-panel.php?section=os'>Systèmes d'exploitation</a>
            <a class='sidebar-section' href='admin-panel.php?section=manufacturers'>Fabricants</a>
            <a class='sidebar-section' href='admin-panel.php?section=techs'>Techniciens</a>
        </div>
    </div>

    <div>
        <h1>Ici c'est le Panneau Administrateur</h1>
        <a href='../logout.php' class='sections'>Se déconnecter</a>
        <a href='create-tech-form.php'>Créer un technicien</a>
        <a href='add-os-form.php'>Ajouter un système d'exploitation</a>
        <a href='add-manufacturer-form.php'>Ajouter un fabriquant</a>

        <hr>

        <?php
        // --- AFFICHAGE DES SECTIONS ---

        $section = $_GET['section'] ?? ''; // Récupère la section ou chaîne vide par défaut

        if ($section == "os") {
            // Affichage des systèmes d'exploitation
            echo "<h2>Systèmes d'exploitation enregistrés</h2>";
            $query = "SELECT id, name FROM `os_list` ORDER BY name";
            $result = mysqli_query($loginToDb, $query);

            echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>";

            while($row = mysqli_fetch_assoc($result)){
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>";
                echo "<a href='edit-os-form.php?id=". htmlspecialchars($row['id']) ."'>Modifier</a> | ";
                echo "<a href='actions/action-delete-os.php?id=". htmlspecialchars($row['id']) ."' onclick='return confirm(\"Voulez-vous vraiment supprimer cet OS ?\")'>Supprimer</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";

        } else if ($section == "manufacturers") {
            // Affichage des fabricants
            echo "<h2>Fabricants enregistrés</h2>";
            $query = "SELECT id, name FROM `manufacturer_list` ORDER BY name";
            $result = mysqli_query($loginToDb, $query);

            echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>";

            while($row = mysqli_fetch_assoc($result)){
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>";
                echo "<a href='edit-manufacturer-form.php?id=". htmlspecialchars($row['id']) ."'>Modifier</a> | ";
                echo "<a href='actions/action-delete-manufacturer.php?id=". htmlspecialchars($row['id']) ."' onclick='return confirm(\"Voulez-vous vraiment supprimer ce fabricant ?\")'>Supprimer</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else if ($section == "techs") {
            // Affichage des utilisateurs
            echo "<h2>Techniciens</h2>";
            $query = "SELECT id, name FROM `users` 
          WHERE name NOT IN ('adminweb', 'sysadmin') 
          ORDER BY name";
            $result = mysqli_query($loginToDb, $query);

            echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>";

            while($row = mysqli_fetch_assoc($result)){
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>";
                // L'admin ne peut pas se supprimer lui-même ni supprimer le sysadmin/adminweb facilement
                if ($row['name'] !== 'adminweb' && $row['name'] !== 'sysadmin') {
                    echo "<a href='actions/action-delete-user.php?id=". htmlspecialchars($row['id']) ."' onclick='return confirm(\"Voulez-vous vraiment supprimer cet utilisateur ?\")'>Supprimer</a>";
                } else {
                    echo "N/A";
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
    </div>
    </body>
    </html>
    <?php
} else {
    // Redirection si non connecté ou non autorisé
    header("Location: ../login.php?error=admin_required");
    exit();
}
mysqli_close($loginToDb);
?>