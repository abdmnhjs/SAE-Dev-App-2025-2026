<?php

class DashboardController extends BaseController
{
    public function index(): Renderer
    {
        Auth::requireRole(Roles::TECH);

        $role = $_SESSION['rank'] ?? 0;

        if ($role == Roles::TECH) {
            $this->tech();
            exit();
        } elseif ($role == Roles::ADMINWEB) {
            $this->admin();
            exit();
        } elseif ($role == Roles::SYSADMIN) {
            $this->sysadmin();
            exit();
        } else {
            //si quelqu'un externe ce connecte, retourne au menu
            header('Location: /rpi12');
            exit();
        }

        // Unknown role // redondant car Auth::requireRole à déjà un return si le role est inconnu
        $error = new ErrorController();
        return $error->forbidden();
    }

    public function tech(): void
    {
        Auth::requireRole(Roles::TECH);
        header('Location: /rpi12/dashboard/tech');
        exit;
    }
    public function sysadmin(): void
    {
        Auth::requireRole(Roles::SYSADMIN);
        header('Location: /rpi12/dashboard/sysadmin');
        exit;
    }
    public function admin(): void
    {
        Auth::requireRole(Roles::ADMINWEB);
        header('Location: /rpi12/dashboard/admin');
        exit;
    }
}