<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/AnnouncementsRepository.php';
require_once __DIR__ . '/../managers/AttachmentManager.php';
require_once __DIR__ . '/../responses/PostFormResponse.php';
require_once __DIR__ . '/../utils/logger.php';
require_once __DIR__ . '/../validation/PostDataValidator.php';


class AnnouncementController extends AppController
{
    private $announcemetsRepository;

    public function __construct()
    {
        parent::__construct();

        $this->announcemetsRepository = new AnnouncementsRepository();
    }

    public function add()
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

    public function add_announcement()
    {
        $this->loginRequired();
        //
        // VALIDATION
        //
        $validator = new PostDataValidator($_POST);
        $validator->addField('pet-name', new NotEmptyValidation('Wprowadź imię zwierzaka'));
        $validator->addField('pet-age', (new RangeValidation('int', 'Wiek musi być dodatnią liczbą całkowitą', 1, null))->setCanValueBeEmpty(true));
        $validator->addField('pet-age-type', new InArrayValidation('Wybrana opcja nie jest dostępna', ['day', 'month', 'year']));
        $validator->addField('pet-gender', new InArrayValidation('Wybrana opcja nie jest dostępna', ['male', 'female']));
        $validator->addField('pet-type', new InArrayValidation(
            'Musisz wybrać typ z listy',
            array_map(function ($animalType) {
                return $animalType->getId();
            }, $this->announcemetsRepository->getAnimalTypes())
        ));
        $validator->addField('pet-kind', (new NotEmptyValidation('Podaj gatunek', 0, null))->setCanValueBeEmpty(true));
        $validator->addField('pet-description', (new MinMaxLengthValidation(null, 'Opis musi mieć więcej niż 50 znaków i mniej niż 2000 znaków', 1, 2000))->setRejectHTMLSpecialChars(true));
        $validator->addField('pet-price', (new RangeValidation('float', 'Cena musi być dodatnia liczbą',  0, null))->setCanValueBeEmpty(true));
        $validator->addField('pet-characteristics', (new InCheckboxArrayValidation(
            'Błąd wypełniania charakterystyk',
            array_map(function ($animalFeature) {
                return $animalFeature->getId();
            }, $this->announcemetsRepository->getAnimalFeatures())
        ))->setCanValueBeEmpty(true));
        $validator->addField('pet-location', (new NotEmptyValidation('Podaj lokalizację')));

        if (!$validator->validate()) {
            (new PostFormResponse($validator->getErrors()))->send();
        }
        $data = $validator->getSanitizedData();
        //
        // SECOND VALIDATION
        //
        $animalFeaturesValues = [];
        foreach ($data['pet-characteristics'] ?? [] as $id => $value) {
            $animalFeaturesValues[] = $value;
        }
        $dataToCheck = ['pet-characteristics' => $animalFeaturesValues];
        $nextValidator = new PostDataValidator($dataToCheck);
        $nextValidator->addField('pet-characteristics', new InArrayValidation('Wybrana opcja nie jest dostępna', [0, 1, 2]));

        if (!$nextValidator->validate()) {
            (new PostFormResponse($nextValidator->getErrors()))->send();
        }
        //
        // END VALIDATION
        //

        $response = new JsonResponse();

        $animalFeatures = [];
        foreach ($_POST['pet-characteristics'] ?? [] as $id => $value) {
            if ($value != 0) {
                $animalFeatures[] = new AnimalFeature($id, null, $value);
            }
        }

        $announcement = new Announcement(
            null,
            new AnimalType($data['pet-type'], null),
            $this->getLoggedUser(),
            $data['pet-kind'],
            false,
            $data['pet-name'],
            $data['pet-location'],
            $data['pet-price'],
            $data['pet-description'],
            $data['pet-age'],
            $data['pet-age'] ? $data['pet-age-type'] : null,
            $data['pet-gender'],
            null,
            $animalFeatures
        );


        $avatar = new AttachmentManager($_FILES['pet-avatar']);
        if ($avatar->is_uploaded()) {
            try {
                $avatarName = $avatar->save();
            } catch (Exception $e) {
                Logger::debug('Upload exception: ' . $e->getMessage());
                $response->setError('Wystąpił błąd zapisu awataru, spróbuj ponownie', 500);
                $response->send();
            }
            $announcement->getDetails()->setAvatarName($avatarName);
        } else {
            $response->setError('Nie przesłano awatara', 400);
        }

        try {
            $this->announcemetsRepository->add_or_edit($announcement);
        } catch (Exception $e) {
            $response->setError('Wystąpił wewnętrzny błąd, spróbuj ponownie później', 500);
        }

        $response->setData(['redirect_url' => '/announcement/' . $announcement->getId()]);
        $response->send();
    }

    public function announcement($id)
    {
        $this->render("announcement", ['user' => $this->getLoggedUser()]);
    }
}
