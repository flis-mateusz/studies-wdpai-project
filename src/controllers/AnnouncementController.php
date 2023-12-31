<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/AnnouncementsRepository.php';

class AnnouncementController extends AppController
{
    private $announcemetsRepository;

    public function __construct()
    {
        parent::__construct();

        $this->announcemetsRepository = new AnnouncementsRepository();
    }

    public function add_announcement()
    {
        $this->loginRequired();
        $this->render(
            "announcement_add",
            [
                'user' => $this->getLoggedUser(),
                'animalFeatures' => $this->announcemetsRepository->getAnimalFeatures(),
                'animalTypes' => $this->announcemetsRepository->getAnimalTypes()
            ]
        );
    }

    public function announcement($id)
    {
        $this->render("announcement", ['user' => $this->getLoggedUser()]);
    }
}
