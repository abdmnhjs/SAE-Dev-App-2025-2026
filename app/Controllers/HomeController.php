<?php

class HomeController extends BaseController
{
    private static Database $database;
    private static Screen $screenModel;
    private static ControlUnit $unitModel;
    private static Logs $logsModel;
    public function home(): Renderer
    {
        Auth::requireRole(Roles::GUEST);
        self::$database = new Database();
        self::$screenModel = new Screen(self::$database->getConnection());
        self::$unitModel = new ControlUnit(self::$database->getConnection());
        self::$logsModel = new Logs(self::$database->getConnection());


        return $this->render('home/home');
    }

    public function inventory(): Renderer
    {
        Auth::requireRole(Roles::GUEST);
        self::$database = new Database();
        self::$logsModel = new Logs(self::$database->getConnection());
        $get_total_connections = self::$logsModel->getTotalConnections();
        $get_anomalously_short_sessions = self::$logsModel->getAnomalouslyShortSessions();
        $get_average_session_duration = self::$logsModel->getAverageSessionDuration();
        $get_session_duration_distribution = self::$logsModel->getSessionDurationDistribution();
        $get_anomalously_long_sessions = self::$logsModel->getAnomalouslyLongSessions();

        $data = compact('get_total_connections', 'get_anomalously_short_sessions', 'get_average_session_duration'
        , 'get_session_duration_distribution', 'get_anomalously_long_sessions');
        return $this->render('home/inventory', $data);
    }
    public function screens_stats(): Renderer
    {
        Auth::requireRole(Roles::GUEST);
        self::$database = new Database();
        self::$screenModel = new Screen(self::$database->getConnection());
        $get_manufacturer_distribution = self::$screenModel->getManufacturerDistribution();
        $get_connector_distribution = self::$screenModel->getConnectorDistribution();
        $get_resolution_distribution = self::$screenModel->getResolutionDistribution();
        $get_unattached_screens = self::$screenModel->getUnattachedScreens();
        $get_screens_per_unit = self::$screenModel->getScreensPerUnit();
        $get_size_distribution = self::$screenModel->getSizeDistribution();
        $data = compact('get_manufacturer_distribution', 'get_connector_distribution',
        'get_resolution_distribution', 'get_unattached_screens', 'get_screens_per_unit', 'get_size_distribution');
        return $this->render('home/screen-stats', $data);
    }
        public function units_stats(): Renderer
    {
        Auth::requireRole(Roles::GUEST);
        self::$database = new Database();
        self::$unitModel = new ControlUnit(self::$database->getConnection());
        $get_manufacturer_distribution = self::$unitModel->getManufacturerDistribution();
        $get_os_distribution = self::$unitModel->getOSDistribution();
        $get_location_distribution = self::$unitModel->getLocationDistribution();
        $get_average_age = self::$unitModel->getAverageAge();
        $get_disk_statistics = self::$unitModel->getDiskStatistics();
        $get_ram_statistics = self::$unitModel->getRAMStatistics();
        $get_type_distribution = self::$unitModel->getTypeDistribution();
        $get_warranty_status = self::$unitModel->getWarrantyStatus();
        $data = compact('get_manufacturer_distribution', 'get_os_distribution',
        'get_disk_statistics', 'get_ram_statistics', 'get_location_distribution','get_average_age', 'get_type_distribution', 'get_warranty_status');
        return $this->render('home/unit-stats', $data);
    }


    public function signin(): Renderer
    {
        Auth::requireRole(Roles::GUEST);
        return $this->render('home/signin');
    }

    public function signup(): Renderer
    {
        Auth::requireRole(Roles::GUEST);
        return $this->render('home/signup');
    }


    public function handleLogin(): void
    {
        // Get POST data
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null; //trouver une facon de respecter le MVC et de proteger le mdp lors du transfère POST

        if (!$username || !$password) {
            header('Location: /rpi12/dashboard?error=unfilled');
            exit();
        }

        $database = new Database();
        $userModel = new User($database->getConnection());

        $user = $userModel->login($username, $password);

        if (!$user) {
            $errorController = new ErrorController();
            $errorController->notFound(); // penser a créer des erreurs
            exit();
        }
        $_SESSION['id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['rank'] = $user['rank'];

        //va chercher quel type de dashboard a montrer en fonction du rank de l'utilisateur
        header('Location: /rpi12/dashboard');
        exit();
    }

    public function handleLogout(): void
    {
        $database = new Database();
        // Use the model to login
        $user = (new User($database->getConnection()))->logout();

        if (!$user) {
            $this->render("home/connexion?error=invalid");
            exit();
        }
        //remplace l'url par /rpi12, cela va trigger home(); qui va render la page du menu.
        header("Location: /rpi12");
        exit();
    }

    public function handleSignup(): void
    {
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;
        $database = new Database();

        //signup pour des utilisateurs, WIP : faire un signup dans adminweb pour créer des techniciens
        $user = (new User($database->getConnection()));

        // vérifie que l'utilisateur n'existe pas déjà
        $DuplicateCheck = $user->DuplicateName($username);

        //anti duplication de users
        if ($DuplicateCheck) {
            $errorController = new ErrorController();
            $errorController->notFound(); // penser a un moyen de renvoyer a signup avec des erreurs
            exit();
        }

        $user->signup($username, $password, 1);
        if ($user) {
            //succès de l'ajout, envois l'user dans connexion pour qu'il ce connecte
            header("Location: /rpi12/inscription");
            exit();
        }

        //échoué, WIP : AJOUTER L'ERREUR
        header("Location: /rpi12/inscription?error=invalid");
        exit();
    }
}
