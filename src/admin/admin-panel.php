<?php
session_start();

require '../includes/init.php';
ensureUserAuthorized("adminweb");

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel d'administration</title>
    <style>
        /* Variables pour un thème cohérent */
        :root {
            --background-color: #121212;
            --primary-color: #70dbce; /* Jaune doré */
            --secondary-color: #1a1a1a;
            --link-color: #333;
            --hover-color: #e74c3c;
            --font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            --text-color: #fff;
            --border-radius: 8px;
        }

        /* Mise en page générale */
        body {
            font-family: var(--font-family);
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100vh;
        }

        /* En-tête */
        header {
            background-color: var(--secondary-color);
            padding: 20px;
            text-align: center;
        }

        header h1 {
            color: var(--primary-color);
            font-size: 2.5em;
            margin: 0;
        }

        /* Navigation */
        nav {
            background-color: var(--secondary-color);
            width: 100%;
            text-align: center;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        nav ul li {
            margin: 10px;
        }

        nav ul li a {
            display: inline-block;
            padding: 12px 20px;
            background-color: var(--link-color);
            color: var(--text-color);
            text-decoration: none;
            border-radius: var(--border-radius);
            font-size: 1.1em;
            transition: background-color 0.3s, transform 0.3s;
        }

        /* Effet au survol des liens */
        nav ul li a:hover {
            background-color: var(--primary-color);
            color: var(--background-color);
            transform: scale(1.05);
        }

        /* Lien de déconnexion */
        nav .logout {
            background-color: #c0392b; /* Rouge */
        }

        nav .logout:hover {
            background-color: var(--hover-color);
        }

        /* Contenu principal */
        main {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .welcome-message {
            background-color: var(--secondary-color);
            padding: 40px;
            border-radius: var(--border-radius);
            width: 100%;
            max-width: 700px;
            text-align: center;
        }

        /* Pied de page */
        footer {
            background-color: var(--secondary-color);
            text-align: center;
            padding: 10px;
            font-size: 0.9em;
        }

        /* Responsive : ajustement sur petit écran */
        @media (max-width: 768px) {
            header h1 {
                font-size: 2em;
            }

            nav ul {
                flex-direction: column;
            }

            nav ul li a {
                padding: 10px;
                font-size: 1em;
            }

            .welcome-message {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <h1>Admin Panel</h1>
    </div>
</header>

<nav>
    <ul>
        <li><a href="create-tech-form.php">Créer un technicien</a></li>
        <li><a href="add-os-form.php">Ajouter un système d'exploitation</a></li>
        <li><a href="add-manufacturer-form.php">Ajouter un fabriquant</a></li>
        <li><a href="../logout.php" class="logout">Se déconnecter</a></li>
    </ul>
</nav>

<main>
    <?php if (isset($_SESSION['username']) && $_SESSION['username'] === "adminweb") : ?>
        <section class="welcome-message">
            <h2>Bienvenue, adminweb</h2>
            <p>Vous avez accès à toutes les fonctionnalités d'administration.</p>
        </section>
    <?php else : ?>
        <p>Accès refusé. Veuillez vous connecter avec des privilèges d'administrateur.</p>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; 2025 Admin Panel. Tous droits réservés.</p>
</footer>

</body>
</html>
