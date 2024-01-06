<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/AnnouncementsRepository.php';
require_once __DIR__ . '/../repository/AnimalTypesRepository.php';
require_once __DIR__ . '/../repository/AnimalFeaturesRepository.php';


class DefaultController extends AppController
{
    private $announcementsRepository;
    private $animalTypesRepository;
    private $animalFeaturesRepository;

    public function __construct()
    {
        parent::__construct();
        $this->announcementsRepository = new AnnouncementsRepository();
        $this->animalTypesRepository = new AnimalTypesRepository();
        $this->animalFeaturesRepository = new AnimalFeaturesRepository();
    }

    public function index()
    {
        $announcements = $this->announcementsRepository->getAnnouncements();
        $this->render("dashboard", [
            'announcements' => $announcements, 'user' => $this->getLoggedUser(),
            'dogType' => $this->animalTypesRepository->getByName('pies'),
            'catType' => $this->animalTypesRepository->getByName('kot')
        ]);
    }

    public function login()
    {
        $this->render("login");
    }

    public function help()
    {
        $this->render("help", ['user' => $this->getLoggedUser()]);
    }
}
