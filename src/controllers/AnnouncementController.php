<?php

require_once 'AppController.php';
require_once 'SecurityController.php';

class AnnouncementController extends AppController
{
    private $securityController;

    public function __construct()
    {
        parent::__construct();
        $this->securityController = new SecurityController();
    }

    public function add_announcement()
    {
        $this->securityController->login_required();
        $this->render("announcement_add");
    }

    public function announcement($id)
    {
        $this->render("announcement");
    }
}
