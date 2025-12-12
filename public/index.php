<?php
session_start();
//require toutes les classes : controllers, models, core, config
spl_autoload_register(function ($class) {
    $className = basename(str_replace('\\', '/', $class));
    $paths = [
        __DIR__ . '/../app/Controllers/',
        __DIR__ . '/../app/Models/',
        __DIR__ . '/../app/Models/tech/',
        __DIR__ . '/../app/Core/',
        __DIR__ . '/../config/',
    ];

    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            include $file;
            return;
        }
    }
});

//variable BASE_VIEW_PATH = root/app/Views/
define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('BASE_VIEW_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app'. DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR);
define('BASE_PUBLIC_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
define('BASE_MODEL_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app'. DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR);
define('BASE_INCLUDES_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app'. DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR);


// Charger le .env avec la fonction static load()
EnvLoader::load(__DIR__ . '/../.env');


// Maintenant $_ENV contient toutes les variables
$user = $_ENV['USER'];
$debug = $_ENV['DEBUG'];







$router = new Router('/');

// enregistrement des différentes routes

// register : l'url afficher, [le controller, la méthode dans le controller]

//
$router->register('/', ['HomeController', 'home']); // Shows different page based on role
$router->register('/inventaire', ['HomeController', 'inventory']);

$router->register('/statistiques', ['HomeController', 'statistics']);

$router->register('/ecrans', ['HomeController', 'screens_stats']);
$router->register('/unites-centrales', ['HomeController', 'units_stats']);

$router->register('/connexion', ['HomeController', 'signin']);
$router->register('/inscription', ['HomeController', 'signup']);

//
$router->register('/handleSignup', ['HomeController', 'handleSignup']);
$router->register('/handleLogin', ['HomeController', 'handleLogin']);
$router->register('/handleLogout', ['HomeController', 'handleLogout']);

$router->register('/inventaire/stats', ['StatsController', 'home']);



// dashboard (anciennement nommé panel)
$router->register('/dashboard', ['DashboardController', 'index']);

$router->register('/dashboard/tech', ['TechController', 'home']);
//$router->register('/tech/ecran', ['TechController', 'displayScreens']);
$router->register('/dashboard/tech/ecran/ajouter', ['TechController', 'add_screen']);
$router->register('/dashboard/tech/ecran/ajouter_csv', ['TechController', 'add_screens']);
$router->register('/dashboard/tech/ecran/modifier', ['TechController', 'edit_screen']);
$router->register('/dashboard/tech/ecran/supprimer', ['TechController', 'delete_screen']);
//$router->register('/tech/unite-centrale', ['TechController', 'displayControlUnit']);
$router->register('/dashboard/tech/unite-centrale/ajouter', ['TechController', 'add_control_unit']);
$router->register('/dashboard/tech/unite-centrale/ajouter_csv', ['TechController', 'add_control_units']);
$router->register('/dashboard/tech/unite-centrale/modifier', ['TechController', 'edit_control_unit']);
$router->register('/dashboard/tech/unite-centrale/supprimer', ['TechController', 'delete_control_unit']);

$router->register('/dashboard/admin', ['AdminController', 'home']);
$router->register('/dashboard/admin/techniciens', ['AdminController', 'displayTech']);
$router->register('/dashboard/admin/techniciens/ajouter', ['AdminController', 'add_tech']);
$router->register('/dashboard/admin/techniciens/supprimer', ['AdminController', 'del_tech']);
//$router->register('/admin/os', ['AdminController', 'displayOs']);
$router->register('/dashboard/admin/os/ajouter', ['AdminController', 'add_os']);
$router->register('/dashboard/admin/os/supprimer', ['AdminController', 'del_os']);
//$router->register('/admin/manufactureur', ['AdminController', 'displayManufacturer']);
$router->register('/dashboard/admin/manufactureur/ajouter', ['AdminController', 'add_manufacturer']);
$router->register('/dashboard/admin/manufactureur/supprimer', ['AdminController', 'del_manufacturer']);

$router->register('/dashboard/sysadmin', ['SysadminController', 'home']);



//recherche (avec App.php)
(new App($router, $_SERVER['REQUEST_URI']))-> run();