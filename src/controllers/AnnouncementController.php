<?php

require_once 'AppController.php';

class AnnouncementController extends AppController
{

    public function add_announcement()
    {
        $this->render("announcement_add");
    }

    public function announcement($id)
    {
        $this->render("announcement");
    }
}
