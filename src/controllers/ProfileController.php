<?php

require_once 'AppController.php';
require_once 'SecurityController.php';

class ProfileController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $security = new SecurityController();

        $security->login_required();
    }

    public function profile()
    {
        $this->render("profile");
    }
}
