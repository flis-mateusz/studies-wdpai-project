<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/AnnouncementsRepository.php';


class DefaultController extends AppController
{
    private $announcementsRepository;

    public function __construct()
    {
        parent::__construct();
        $this->announcementsRepository = new AnnouncementsRepository();
    }

    public function index()
    {
        $announcements = $this->announcementsRepository->getAnnouncements();
        $this->render("dashboard", ['announcements' => $announcements, 'user' => $this->getLoggedUser()]);
    }

    public function login()
    {
        $this->render("login");
    }
}
