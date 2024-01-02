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

    // --------------------- RENDERS ---------------------------
    public function announcement($id)
    {
        $currentUser = $this->getLoggedUser();
        $this->render(
            "announcement",
            [
                'viewer' => $this->getLoggedUser(),
                'announcement' => $this->announcemetsRepository->getAnnouncementWithUserContext($id, $currentUser ? $currentUser->getId() : null),
            ]
        );
    }

    public function add()
    {
        $this->loginRequired();
        $this->render(
            "announcement_form",
            [
                'user' => $this->getLoggedUser(),
                'animalFeatures' => $this->announcemetsRepository->getAnimalFeatures(),
                'animalTypes' => $this->announcemetsRepository->getAnimalTypes()
            ]
        );
    }
    // ----------------------------------------------------------------

    // --------------------- ACTIONS ---------------------------
    public function api_add()
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
                $animalFeatures[] = new PetFeature($id, null, $value);
            }
        }

        $announcementDetail = new AnnouncementDetail(
            $data['pet-name'],
            $data['pet-location'],
            $data['pet-price'],
            $data['pet-description'],
            $data['pet-age'],
            $data['pet-age'] ? $data['pet-age-type'] : null,
            $data['pet-gender'],
            null,
            $data['pet-kind'],
            $animalFeatures
        );

        $announcement = new Announcement(
            null,
            new PetType($data['pet-type'], null),
            $this->getLoggedUser(),
            $announcementDetail,
            false,
            null
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
            $this->announcemetsRepository->addAnnouncement($announcement);
        } catch (Exception $e) {
            $response->setError('Wystąpił wewnętrzny błąd, spróbuj ponownie później', 500);
        }

        $response->setData(['redirect_url' => '/announcement/' . $announcement->getId()]);
        $response->send();
    }

    public function api_announcement_delete()
    {
        $this->loginRequired();

        $response = new JsonResponse();
        $id = $this->getPostAnnouncementId();

        $currentUser = $this->getLoggedUser();
        $announcement = $this->announcemetsRepository->getAnnouncementWithUserContext($id, null, false);
        $adminId = null;

        if (!$announcement || $announcement->isDeleted()) {
            $response->setError('Ogłoszenie nie istnieje lub zostało już usunięte', 400);
            $response->send();
        } else if ($currentUser->getId() !== $announcement->getUser()->getId()) {
            $this->adminPrivilegesRequired();
            $adminId = $currentUser->getId();
        }

        try {
            $this->announcemetsRepository->delete($id, $adminId);
        } catch (Exception $e) {
            error_log($e);
            $response->setError('Wystąpił wewnętrzny błąd, spróbuj ponownie później', 500);
        }
        if ($adminId) {
            $response->setData(['redirect_url' => '/panel-announcements']);
        } else {
            $response->setData(['redirect_url' => '/profile-announcements']);
        }
        $response->send();
    }

    public function api_announcement_approve()
    {
        $this->adminPrivilegesRequired();

        $response = new JsonResponse();
        $id = $this->getPostAnnouncementId();

        try {
            $this->announcemetsRepository->approve($id);
        } catch (Exception $e) {
            error_log($e);
            $response->setError('Wystąpił wewnętrzny błąd, spróbuj ponownie później', 500);
            $response->send();
        }
        $response->setData('approved');
        $response->send();
    }

    public function api_announcement_report()
    {
        $this->loginRequired();
        $response = new JsonResponse();

        $id = $this->getPostAnnouncementId();

        $currentUser = $this->getLoggedUser();
        $announcement = $this->announcemetsRepository->getAnnouncementWithUserContext($id, $currentUser->getId(), false);

        if (!$announcement || $announcement->isDeleted()) {
            $response->setError('Ogłoszenie nie istnieje lub zostało usunięte', 400);
            $response->send();
        } else if ($currentUser->getId() == $announcement->getUser()->getId()) {
            $response->setError('Nie możesz zgłosić swojego ogłoszenia', 400);
            $response->send();
        } else if ($announcement->isReporedByUser()) {
            $response->setError('Zgłoszono już to ogłoszenie', 400);
            $response->send();
        }

        try {
            $this->announcemetsRepository->report($currentUser->getId(), $announcement->getId());
        } catch (Exception $e) {
            $response->setError('Wystąpił wewnętrzny błąd, spróbuj ponownie później', 500);
            $response->send();
        }
        $response->setData('reported');
        $response->send();
    }

    public function api_announcement_like()
    {
        $this->loginRequired();
        $response = new JsonResponse();

        $id = $this->getPostAnnouncementId();
        $currentUser = $this->getLoggedUser();
        $announcement = $this->announcemetsRepository->getAnnouncementWithUserContext($id, $currentUser->getId(), false);
        if (!$announcement || $announcement->isDeleted()) {
            $response->setError('Ogłoszenie nie istnieje lub zostało usunięte', 404);
            $response->send();
        }

        try {
            if ($announcement->isUserFavourite()) {
                $this->announcemetsRepository->unlike($currentUser->getId(), $announcement->getId());
                $response->setData('unliked');
                $response->send();
            } else {
                $this->announcemetsRepository->like($currentUser->getId(), $announcement->getId());
                $response->setData('liked');
                $response->send();
            }
        } catch (Exception $e) {
            error_log($e);
            $response->setError('Wystąpił wewnętrzny błąd, spróbuj ponownie później', 500);
            $response->send();
        }
    }

    // ----------------------------------------------------------------
    public function getUsersFavorite($user)
    {
        return $this->announcemetsRepository->getUsersFavorite($user);
    }

    private function getPostAnnouncementId(): ?int
    {
        $data = $this->getJsonData();
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            $response = new JsonResponse();
            $response->setError('Nieprawidłowy identyfikator ogłoszenia', 400);
            $response->send();
        }
        return $data['id'];
    }
}
