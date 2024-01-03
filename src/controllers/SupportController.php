<?php

require_once 'AppController.php';
require_once __DIR__ . '/../utils/logger.php';

class SupportController extends AppController
{
    public function __construct()
    {
        parent::__construct();

        $this->loginRequired();
    }

    public function support()
    {
        $this->render("support", ['user' => $this->getLoggedUser()]);
    }
}
