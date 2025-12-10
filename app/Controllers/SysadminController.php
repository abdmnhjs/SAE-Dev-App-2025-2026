<?php

Class SysadminController extends BaseController {


    public function home(): Renderer {
        Auth::requireRole(Roles::SYSADMIN);
        $rank = $_SESSION['rank'];
        $rank2 = Roles::SYSADMIN;
        $data = compact('rank', 'rank2');
        return $this->render('sysadmin/home', $data);
    }



}