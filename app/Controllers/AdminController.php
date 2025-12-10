<?php

Class AdminController extends BaseController {
    private Database $database;
    private Os $osModel;
    private Manufacturer $manufacturerModel;
    public User $userModel;


    public function home(Array $params = []): Renderer {
        Auth::requireRole(Roles::ADMINWEB);
        $this->database = new Database();
        $this->osModel = new Os($this->database->getConnection());
        $this->manufacturerModel = new Manufacturer($this->database->getConnection());
        $this->userModel = new User($this->database->getConnection());



        $anwser = $params['anwser'] ?? 'Bienvenue sur la section administrateur web.';
        $data = compact('anwser');
        return $this->render('admin/home', $data);
    }
    /*
     * Technicians
     */
    public function displayTech(): Renderer {
        Auth::requireRole(Roles::ADMINWEB);
        $section = "tech";
        $db = new Database();
        $userModel = new User($db->getConnection());
        $tech = $userModel->getUsersByRank(Roles::TECH);
        $data = compact('section', 'tech');
        return $this->render('admin/home', $data);
    }
    public function add_tech(): Renderer {
        Auth::requireRole(Roles::ADMINWEB);
        $section = "add-tech";
        $data = compact('section');
        return $this->render('admin/home', $data);
    }
    public function del_tech(): Renderer {
        Auth::requireRole(Roles::ADMINWEB);
        $section = "del-tech";
        $data = compact('section');
        return $this->render('admin/home', $data);
    }
    /*
     * Operating system
     */
    public function displayOs(): Renderer {
        Auth::requireRole(Roles::ADMINWEB);
        $section = "os";
        $data = compact('section');
        return $this->render('admin/home', $data);
    }
    public function add_os(): Renderer {
        Auth::requireRole(Roles::ADMINWEB);
        $section = "add-os";
        $data = compact('section');
        return $this->render('admin/home', $data);
    }
    public function del_os(): Renderer {
        Auth::requireRole(Roles::ADMINWEB);
        $section = "del-os";
        $data = compact('section');
        return $this->render('admin/home', $data);
    }
    /*
     * Manufacturer
     */
    public function displayManufacturer(): Renderer {
        Auth::requireRole(Roles::ADMINWEB);
        $section = "manufacturer";
        $data = compact('section');
        return $this->render('admin/home', $data);
    }
    public function add_manufacturer(): Renderer {
        Auth::requireRole(Roles::ADMINWEB);
        $section = "add-manufacturer";
        $data = compact('section');
        return $this->render('admin/home', $data);
    }
    public function del_manufacturer(): Renderer {
        Auth::requireRole(Roles::ADMINWEB);
        $section = "del-manufacturer";
        $data = compact('section');
        return $this->render('admin/home', $data);
    }
}