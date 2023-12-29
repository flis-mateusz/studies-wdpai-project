<?php

require_once 'AppController.php';

class AnnouncementController extends AppController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_announcement()
    {
        $this->loginRequired();
        $this->render("announcement_add", ['user' => $this->getLoggedUser()]);
    }

    public function announcement($id)
    {
        $this->render("announcement", ['user' => $this->getLoggedUser()]);
    }
}
