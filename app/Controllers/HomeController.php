<?php

class HomeController extends BaseController
{
    private static Database $database;
    private static Screen $screenModel;
    private static ControlUnit $unitModel;
    private static Logs $logsModel;
    private static Manufacturer $manufacturerModel;
    private static Os $osModel;

    public function home(): Renderer
    {
        Auth::requireRole(Roles::GUEST);


        return $this->render('home/home');
    }

    public function statistics(): Renderer
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
        return $this->render('home/logs-stats', $data);
    }

    public function inventory(): Renderer
    {
        Auth::requireRole(Roles::GUEST);
        self::$database = new Database();
        self::$unitModel = new ControlUnit(self::$database->getConnection());
        $get_units = self::$unitModel->all();
        self::$screenModel = new Screen(self::$database->getConnection());
        $get_screens = self::$screenModel->all();
        $data = compact('get_units', 'get_screens');
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
        $getUnattachedScreensList = self::$screenModel->getUnattachedScreensList();


        $data = compact('get_manufacturer_distribution', 'get_connector_distribution',
            'get_resolution_distribution', 'get_unattached_screens', 'get_screens_per_unit', 'get_size_distribution',
            'getUnattachedScreensList');
        return $this->render('home/screen-stats', $data);
    }

    public function units_stats(): Renderer
    {
        Auth::requireRole(Roles::GUEST);
        self::$database = new Database();
        self::$unitModel = new ControlUnit(self::$database->getConnection());
        self::$manufacturerModel = new Manufacturer(self::$database->getConnection());
        self::$osModel = new Os(self::$database->getConnection());
        $get_manufacturer_distribution = self::$unitModel->getManufacturerDistribution();
        foreach ($get_manufacturer_distribution as &$row) {
            if ($row['id_manufacturer']) {
                $row['id_manufacturer'] = self::$manufacturerModel->manufacturerName($row['id_manufacturer'])['name'];
            }
        }


        $get_os_distribution = self::$unitModel->getOSDistribution();
        foreach ($get_os_distribution as &$row) {
            if ($row['id_os']) {
                $row['id_os'] = self::$osModel->osName($row['id_os'])['name'];
            }
        }

        $get_location_distribution = self::$unitModel->getLocationDistribution();
        $get_average_age = self::$unitModel->getAverageAge();
        $get_disk_statistics = self::$unitModel->getDiskStatistics();
        $get_ram_statistics = self::$unitModel->getRAMStatistics();
        $get_type_distribution = self::$unitModel->getTypeDistribution();
        $get_warranty_status = self::$unitModel->getWarrantyStatus();
        $get_ram_gap = self::$unitModel->getRamGap();
        $get_disk_variance = self::$unitModel->getDiskVariances();
        $get_ram_mean = self::$unitModel->getRAMmean();
        $get_disk_mean = self::$unitModel->getDiskMean();
        $data = compact('get_manufacturer_distribution', 'get_os_distribution',
            'get_disk_statistics', 'get_ram_statistics', 'get_location_distribution', 'get_average_age',
            'get_type_distribution', 'get_warranty_status', 'get_disk_variance', 'get_ram_gap', 'get_disk_mean',
            'get_ram_mean');
        return $this->render('home/unit-stats', $data);
    }


    public function signin(): Renderer
    {
        Auth::requireRole(Roles::GUEST);

        return $this->render('home/signin');
    }

    public function handleLogin(): Renderer
    {
        // Get POST data
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null; //trouver une facon de respecter le MVC et de proteger le mdp lors du transfère POST


        if (!$username || !$password) {
            $anwser = "Vous n'avez pas rempli tout les champs.";
            $data = compact('anwser');
            return $this->render('home/signin', $data);
        }


        $database = new Database();
        $userModel = new User($database->getConnection());

        $user = $userModel->login($username, $password);

        if (!$user) {
            $anwser = "Nom d'utilisateur ou Mot de passe incorrecte.";
            $data = compact('anwser');
            return $this->render('home/signin', $data);
        }
        $_SESSION['id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['rank'] = $user['rank'];

        //va chercher quel type de dashboard a montrer en fonction du rank de l'utilisateur
        header('Location: /dashboard');
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
        //remplace l'url par /, cela va trigger home(); qui va render la page du menu.
        header("Location: /");
        exit();
    }

    public function handleSignup(): Renderer
    {
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;
        $password_confirm = $_POST['password_confirm'] ?? null;
        $database = new Database();
        if (!$password || !$username || !$password_confirm){
            $anwser = "Veuillez remplir tous les champs.";
            $data = compact('anwser');
            return $this->render('home/signup', $data);
        }
        if ($password_confirm != $password) {
            $anwser = "Vos mots de passe ne correspond pas.";
            $data = compact('anwser');
            return $this->render('home/signup', $data);
        }

        //signup pour des utilisateurs, WIP : faire un signup dans adminweb pour créer des techniciens
        $user = (new User($database->getConnection()));

        // vérifie que l'utilisateur n'existe pas déjà
        $DuplicateCheck = $user->DuplicateName($username);

        //anti duplication de users
        if ($DuplicateCheck) {
            $anwser = "Le nom d'utilisateur '" . $username . "' est déjà utiliser.";
            $data = compact('anwser');
            return $this->render('home/signup', $data);
        }

        $user->signup($username, $password, 1);
        if ($user) {
            //succès de l'ajout, envois l'user dans connexion pour qu'il ce connecte
            $anwser = "utilisateur créer avec succès.";
            $data = compact('anwser');
            return $this->render('home/signup', $data);
        }

        $anwser = "erreur dans la création de l'utilisateur, veuillez recommencer.";
        $data = compact('anwser');
        return $this->render('home/signup', $data);
    }

    public function signup(): Renderer
    {
        Auth::requireRole(Roles::GUEST);
        return $this->render('home/signup');
    }
}
