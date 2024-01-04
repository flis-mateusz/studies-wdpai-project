<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/AnnouncementsRepository.php';
require_once __DIR__ . '/../responses/PostFormResponse.php';
require_once __DIR__ . '/../utils/logger.php';
require_once __DIR__ . '/../validation/PostDataValidator.php';
require_once __DIR__ . '/../validation/PostFilesValidator.php';


class AnnouncementController extends AppController
{
    private $announcemetsRepository;

    public function __construct()
    {
        parent::__construct();

        $this->announcemetsRepository = new AnnouncementsRepository();
    }

    // --------------------- RENDERS ---------------------------
    public function announcement($announcementId)
    {
        $user = $this->getLoggedUser();
        $announcement = $this->announcemetsRepository->getAnnouncementWithUserContext($announcementId, $user ? $user->getId() : null);
        
        if (!$announcement) {
            $this->exitWithError(404);
        }

        $this->render(
            "announcement",
            [
                'viewer' => $user,
                'announcement' => $announcement,
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
                'animalTypes' => $this->announcemetsRepository->getAnimalTypes(),
                'announcement' => null,
            ]
        );
    }

    public function edit($announcementId)
    {
        $this->loginRequired();

        $user = $this->getLoggedUser();
        $announcement = $this->announcemetsRepository->getAnnouncementWithUserContext($announcementId, $user->getId());

        if (!$announcement || $announcement->isDeleted()) {
            $this->exitWithError(404);
        } else if ($user->getId() !== $announcement->getUser()->getId()) {
            $this->exitWithError(403);
        } else {
            $this->render(
                "announcement_form",
                [
                    'user' => $user,
                    'animalFeatures' => $this->announcemetsRepository->getAnimalFeatures(),
                    'animalTypes' => $this->announcemetsRepository->getAnimalTypes(),
                    'announcement' => $announcement
                ]
            );
        }
    }
    // ----------------------------------------------------------------

    // --------------------- ACTIONS ---------------------------
    public function api_add()
    {
        $this->loginRequired();
        $response = new JsonResponse();
        //
        // VALIDATION
        //
        $validator = new PostDataValidator($_POST);
        $validator->addField('announcement-id', (new NumberValidation(''))->setCanValueBeEmpty(true));
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
        $validator->addField('pet-description', (new MinMaxLengthValidation(null, 'Opis musi mieć więcej niż 50 znaków i mniej niż 2000 znaków', 50, 2000))->setRejectHTMLSpecialChars(true));
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
        // EDITOR VALIDATION
        //
        $existingAnnouncement = null;
        if ($data['announcement-id']) {
            $existingAnnouncement = $this->announcemetsRepository->getAnnouncementWithUserContext($data['announcement-id'], $this->getLoggedUser()->getId());
            if (!$existingAnnouncement || $existingAnnouncement->isDeleted()) {
                (new JsonResponse())->setError('Ogłoszenie nie istnieje lub zostało usunięte', 404)->send();
            } else if ($this->getLoggedUser()->getId() !== $existingAnnouncement->getUser()->getId()) {
                (new JsonResponse())->setError('Nie masz uprawnień do edycji tego ogłoszenia', 403)->send();
            }
        }
        //
        // END VALIDATION
        //
        //
        // SECOND VALIDATION
        //
        $filesValidator = new PostFilesValidator($_FILES);
        $filesValidator->addField('pet-avatar', 'Dodaj zdjęcie zwierzaka', $existingAnnouncement ? false : true);
        if (!$filesValidator->validate()) {
            (new PostFormResponse($filesValidator->getErrors()))->send();
        }
        try {
            $newImage = $filesValidator->getFile('pet-avatar');
            if ($newImage) {
                $avatarName = $newImage->save();
            } else {
                $avatarName = $existingAnnouncement->getDetails()->getAvatarName();
            }
        } catch (Exception $e) {
            error_log($e);
            $response->setError('Wystąpił błąd zapisu załącznika, spróbuj ponownie', 500)->send();
        }

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

        $animalFeatures = [];
        foreach ($data['pet-characteristics'] ?? [] as $id => $value) {
            if ($value != 0) {
                $animalFeatures[] = new PetFeature($id, null, $value);
            }
        }

        $announcementDetail = new AnnouncementDetail(
            $existingAnnouncement ? $existingAnnouncement->getDetails()->getId() : null,
            $data['pet-name'],
            $data['pet-location'],
            $data['pet-price'],
            $data['pet-description'],
            $data['pet-age'],
            $data['pet-age'] ? $data['pet-age-type'] : null,
            $data['pet-gender'],
            $avatarName,
            $data['pet-kind'],
            $animalFeatures
        );

        $announcement = new Announcement(
            $existingAnnouncement ? $existingAnnouncement->getId() : null,
            new PetType($data['pet-type'], null),
            $this->getLoggedUser(),
            $announcementDetail,
            false,
            null
        );

        try {
            if ($existingAnnouncement) {
                $this->announcemetsRepository->updateAnnouncement($announcement);              
            } else {
                $this->announcemetsRepository->addAnnouncement($announcement);
            }
        } catch (Exception $e) {
            $response->setError('Wystąpił wewnętrzny błąd, spróbuj ponownie później', 500)->send();
        }

        if ($newImage && $existingAnnouncement) {
            AttachmentManager::delete($existingAnnouncement->getDetails()->getAvatarName());
        }

        $response->setData(['redirect_url' => '/announcement/' . $announcement->getId()]);
        $response->send();
    }

    public function api_announcement_delete()
    {
        $this->loginRequired();

        $response = new JsonResponse();
        $data = $this->getPOSTData();
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
            $response->setData(['redirect_url' => '/admin_approval']);
        } else if (isset($data['refferer']) && $data['refferer']) {
            $refferer = ValidationStrategy::sanitizeOnly($data['refferer']);
            $response->setData(['redirect_url' => parse_url($refferer, PHP_URL_PATH)]);
        } else {
            $response->setData(['redirect_url' => '/my_announcements']);
        }
        $response->send();
    }

    public function api_announcement_report()
    {
        $this->loginRequired();
        $response = new JsonResponse();

        $id = $this->getPostAnnouncementId();

        $currentUser = $this->getLoggedUser();
        $announcement = $this->announcemetsRepository->getAnnouncementWithUserContext($id, $currentUser->getId(), false);

        if (!$announcement || $announcement->isDeleted() || !$announcement->isAccepted()) {
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
        if (!$announcement || $announcement->isDeleted() || !$announcement->isAccepted()) {
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
        $data = $this->getPOSTData();
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            $response = new JsonResponse();
            $response->setError('Nieprawidłowy identyfikator ogłoszenia', 400);
            $response->send();
        }
        return $data['id'];
    }
}
