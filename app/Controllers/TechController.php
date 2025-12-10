<?php

class TechController extends BaseController
{

    public function home(array $params = []): Renderer
    {
        Auth::requireRole(Roles::TECH);


        $section = $params['section'] ?? 'default';
        $serial = $params['serial'] ?? '';
        $anwser = $params['anwser'] ?? '';


        $database = new Database();
        $screenModel = new Screen($database->getConnection());


        $unitModel = new ControlUnit($database->getConnection());


        $manufacturerModel = new Manufacturer($database->getConnection());


        $osModel = new Os($database->getConnection());


        $screens = [];
        $units = [];
        $manufacturers = [];
        $os = [];
        $getUnit = [];
        $getScreen = [];


        if ($section === 'screens') {
            $screens = $screenModel->all();
            foreach ($screens as &$screen) {
                $screen['manufacturer'] = $manufacturerModel->manufacturerName($screen['id_manufacturer']);
            }
        }

        if ($section === 'control-units') {
            $units = $unitModel->all();
            foreach ($units as &$unit) {
                $unit['manufacturer'] = $manufacturerModel->manufacturerName($unit['id_manufacturer']);
                $unit['os'] = $osModel->OsName($unit['id_os']);
            }
        }
        if ($section === 'add-screen' || $section === 'add-screens') {
            $units = $unitModel->all();
            $manufacturers = $manufacturerModel->all();
        }
        if ($section === 'add-control-unit' || $section === 'add-control-units') {
            $manufacturers = $manufacturerModel->all();
            $os = $osModel->all();

        }
        if ($section === 'edit-screen') {
            $units = $unitModel->all();
            $manufacturers = $manufacturerModel->all();
            $getScreen = $screenModel->getScreen($serial);
        }
        if ($section === 'edit-control-unit') {
            $manufacturers = $manufacturerModel->all();
            $os = $osModel->all();
            $getUnit = $unitModel->getUnit($serial);
        }


        $data = compact('screens', 'units', 'section', 'anwser', 'manufacturers', 'os', 'getUnit', 'getScreen');

        return $this->render('tech/home', $data);
    }

    /*
     * Screens
     */

    public function add_screen(): Renderer
    {
        Auth::requireRole(Roles::TECH);
        $data = [
            'serial' => $_POST['serial'],
            'id_manufacturer' => $_POST['manufacturer'],
            'model' => $_POST['model'],
            'size_inch' => $_POST['sizeInch'],
            'resolution' => $_POST['resolution'],
            'connector' => $_POST['connector'],
            'attached_to' => $_POST['attached_to']
        ];


        $database = new Database();

        $screenModel = new Screen($database->getConnection());

        if ($screenModel->addScreen($data)) {
            $anwser = 'Ajout de l\'écran "' . $data['serial'] . '" effectuer avec succès.';
        } else {
            $anwser = 'Ajout de l\'écran "' . $data['serial'] . '" échoué.';
        }
        $params = compact('anwser');

        return $this->render('tech/home', $params);

    }

    public function edit_screen(): Renderer
    {
        Auth::requireRole(Roles::TECH);

        $data = [
            'serial' => $_POST['serial'],
            'id_manufacturer' => $_POST['manufacturer'],
            'model' => $_POST['model'],
            'size_inch' => $_POST['sizeInch'],
            'resolution' => $_POST['resolution'],
            'connector' => $_POST['connector'],
            'attached_to' => $_POST['attached_to']
        ];



        $database = new Database();

        $screenModel = new Screen($database->getConnection());

        if ($screenModel->editScreen($data['serial'], $data)) {
            $anwser = 'Mise à jour de l\'écran "' . $data['serial'] . '" effectuer avec succès.';
        } else {
            $anwser = 'Mise à jour de l\'écran "' . $data['serial'] . '" échoué.';
        }
        $params = compact('anwser');

        return $this->render('tech/home', $params);
    }

    public function delete_screen(array $params = []): Renderer
    {
        Auth::requireRole(Roles::TECH);
        $serial = $params['serial'];
        $database = new Database();
        $screenModel = new Screen($database->getConnection());
        if ($screenModel->deleteScreen($serial)) {
            $anwser = 'Suppression de l\'unité de contrôle "' . $serial . '" effectuer avec succès.';
        } else {
            $anwser = 'Suppression de l\'unité de contrôle "' . $serial . '" échoué.';
        }



        $data = compact('serial', 'anwser');
        return $this->render('tech/home', $data);
    }

    /*
     * Control Units
     */

    public function add_control_unit(): Renderer
    {
        Auth::requireRole(Roles::TECH);
        $data = [
            'name' => $_POST['name'],
            'serial' => $_POST['serial'],
            'id_manufacturer' => $_POST['manufacturer'],
            'model' => $_POST['model'],
            'type' => $_POST['type'],
            'cpu' => $_POST['cpu'],
            'ram_mb' => $_POST['ramMb'],
            'disk_gb' => $_POST['diskGb'],
            'id_os' => $_POST['os'],
            'domain' => $_POST['domain'] ?? null,
            'location' => $_POST['location'],
            'building' => $_POST['building'],
            'room' => $_POST['room'],
            'macaddr' => $_POST['macaddr'],
            'purchase_date' => $_POST['purchaseDate'],
            'warranty_end' => $_POST['warrantyEnd']
        ];


        $database = new Database();

        $unitModel = new ControlUnit($database->getConnection());

        if ($unitModel->addUnit($data)) {
            $anwser = 'Ajout de l\'unité de contrôle "' . $data['name'] . '" effectuer avec succès.';
        } else {
            $anwser = 'Ajout de l\'unité de contrôle "' . $data['name'] . '" échoué.';
        }
        $params = compact('anwser');

        return $this->render('tech/home', $params);
    }

    public function edit_control_unit(): Renderer
    {
        Auth::requireRole(Roles::TECH);


        $data = [
            'name' => $_POST['name'],
            'serial' => $_POST['serial'],
            'id_manufacturer' => $_POST['manufacturer'],
            'model' => $_POST['model'],
            'type' => $_POST['type'],
            'cpu' => $_POST['cpu'],
            'ram_mb' => $_POST['ramMb'],
            'disk_gb' => $_POST['diskGb'],
            'id_os' => $_POST['os'],
            'domain' => $_POST['domain'] ?? null,
            'location' => $_POST['location'],
            'building' => $_POST['building'],
            'room' => $_POST['room'],
            'macaddr' => $_POST['macaddr'],
            'purchase_date' => $_POST['purchaseDate'],
            'warranty_end' => $_POST['warrantyEnd']
        ];


        $database = new Database();

        $unitModel = new ControlUnit($database->getConnection());

        if ($unitModel->editUnit($data['serial'], $data)) {
            $anwser = 'Mise à jour de l\'unité de contrôle "' . $data['name'] . '" effectuer avec succès.';
        } else {
            $anwser = 'Mise à jour de l\'unité de contrôle "' . $data['name'] . '" échoué.';
        }
        $params = compact('anwser');

        return $this->render('tech/home', $params);
    }

    public function delete_control_unit(array $params = []): Renderer
    {
        Auth::requireRole(Roles::TECH);
        $serial = $params['serial'] ?? 'default';
        $database = new Database();

        $unitModel = new ControlUnit($database->getConnection());
        if ($unitModel->deleteUnit($serial)) {
            $anwser = 'Suppression de l\'unité de contrôle "' . $serial . '" effectuer avec succès.';
        } else {
            $anwser = 'Suppression de l\'unité de contrôle "' . $serial . '" échoué.';
        }

        $data = compact('anwser');
        return $this->render('tech/home', $data);
    }

    public function add_control_units(): Renderer
    {
        if (!isset($_FILES['csv_file'])) {
            return (new ErrorController())->notFound();
        }

        $database = new Database();
        $unitModel = new ControlUnit($database->getConnection());
        $file = $_FILES['csv_file']['tmp_name'];
        if ($unitModel->addUnitsCSV($file)) {
            $anwser = 'Ajout CSV des unités centrales effectuer avec succès.';
        } else {
            $anwser = 'Ajout CSV des unités centrales échoué.';
        }

        $data = compact('anwser');
        return $this->render('tech/home', $data);
    }

        public function add_screens(): Renderer
    {

        if (!isset($_FILES['csv_file'])) {
            return (new ErrorController())->notFound();
        }

        $database = new Database();
        $screenModel = new Screen($database->getConnection());
        $file = $_FILES['csv_file']['tmp_name'];
        if ($screenModel->addScreensCSV($file)) {
            $anwser = 'Ajout CSV des écrans effectuer avec succès.';
        } else {
            $anwser = 'Ajout CSV des écrans échoué.';
        }

        $data = compact('anwser');
        return $this->render('tech/home', $data);
    }

}