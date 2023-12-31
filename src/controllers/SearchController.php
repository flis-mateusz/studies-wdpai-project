<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/AnnouncementsRepository.php';
require_once __DIR__ . '/../responses/PostFormResponse.php';
require_once __DIR__ . '/../validation/PostDataValidator.php';

class SearchController extends AppController
{
    private $announcemetsRepository;

    public function __construct()
    {
        parent::__construct();

        $this->announcemetsRepository = new AnnouncementsRepository();
    }

    public function query_animal_types()
    {
        $this->loginRequired();

        $response = new PostFormResponse();

        $validator = new PostDataValidator($this->getJsonData());
        $validator->addField('search', new SanitizeOnly());
        if (!$validator->validate()) {
            $errors = $validator->getErrors();
            $response->setErrorFields($errors);
            $response->send();
        }
        $data = $validator->getSanitizedData();

        $query = $data['search'];

        $animal_types = $this->announcemetsRepository->getAnimalTypes($query);
        $response->setData($animal_types);
        $response->send();
    }
}
