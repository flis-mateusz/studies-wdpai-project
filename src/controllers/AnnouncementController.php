<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/AnnouncementsRepository.php';
require_once __DIR__ . '/../repository/AnimalTypesRepository.php';
require_once __DIR__ . '/../repository/AnimalFeaturesRepository.php';
require_once __DIR__ . '/../repository/ReportsRepository.php';
require_once __DIR__ . '/../responses/PostFormResponse.php';
require_once __DIR__ . '/../utils/logger.php';
require_once __DIR__ . '/../validation/PostDataValidator.php';
require_once __DIR__ . '/../validation/PostFilesValidator.php';


class AnnouncementController extends AppController
{
    private $announcementsRepository;
    private $animalTypesRepository;
    private $animalFeaturesRepository;
    private $reportsRepository;

    public function __construct()
    {
        parent::__construct();

        $this->announcementsRepository = new AnnouncementsRepository();
        $this->animalTypesRepository = new AnimalTypesRepository();
        $this->animalFeaturesRepository = new AnimalFeaturesRepository();
        $this->reportsRepository = new ReportsRepository();
    }

    // --------------------- RENDERS ---------------------------
    public function announcements()
    {
        $this->render('announcements', [
            'user' => $this->getLoggedUser(),
            'animalFeatures' => $this->animalFeaturesRepository->getAll(),
            'animalTypes' => $this->animalTypesRepository->getByPopularity(),
        ]);
    }

    public function announcement($announcementId)
    {
        $user = $this->getLoggedUser();
        $announcement = $this->announcementsRepository->getAnnouncementWithUserContext($announcementId, $user ? $user->getId() : null);

        if (!$announcement) {
            $this->exitWithError(404);
        }

        $announcement->getDetails()->setFeatures($this->animalFeaturesRepository->getForAnnouncement($announcement->getDetails()->getId()));

        if ($user && $user->isAdmin()) {
            $reportsCount = $this->reportsRepository->getAnnouncementReportsCount($announcement->getId());
            $announcement->getDetails()->setReportsCount($reportsCount);
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
                'animalFeatures' => $this->animalFeaturesRepository->getAll(),
                'animalTypes' => $this->animalTypesRepository->getByPopularity(),
                'announcement' => null,
            ]
        );
    }

    public function edit($announcementId)
    {
        $this->loginRequired();

        $user = $this->getLoggedUser();
        $announcement = $this->announcementsRepository->getAnnouncementWithUserContext($announcementId, $user->getId());
        
        if (!$announcement || $announcement->isDeleted()) {
            $this->exitWithError(404);
        } else if ($user->getId() !== $announcement->getUser()->getId()) {
            $this->exitWithError(403);
        } else {
            $announcement->getDetails()->setFeatures($this->animalFeaturesRepository->getForAnnouncement($announcement->getDetails()->getId()));

            $this->render(
                "announcement_form",
                [
                    'user' => $user,
                    'animalFeatures' => $this->animalFeaturesRepository->getAll(),
                    'animalTypes' => $this->animalTypesRepository->getByPopularity(),
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
        $validator->addField('pet-name', new MinMaxLengthValidation(null, 'Wprowadź imię zwierzaka', 2, 20));
        $validator->addField('pet-age', (new RangeValidation('int', 'Wiek musi być dodatnią liczbą całkowitą', 1, null))->setCanValueBeEmpty(true));
        $validator->addField('pet-age-type', new InArrayValidation('Wybrana opcja nie jest dostępna', ['day', 'month', 'year']));
        $validator->addField('pet-gender', new InArrayValidation('Wybrana opcja nie jest dostępna', ['male', 'female']));
        $validator->addField('pet-type', new InArrayValidation(
            'Musisz wybrać typ z listy',
            array_map(function ($animalType) {
                return $animalType->getId();
            }, $this->animalTypesRepository->getAll())
        ));
        $validator->addField('pet-kind', (new NotEmptyValidation('Podaj gatunek', 0, null))->setCanValueBeEmpty(true));
        $validator->addField('pet-description', (new MinMaxLengthValidation(null, 'Opis musi mieć więcej niż 50 znaków i mniej niż 2000 znaków', 50, 2000))->setRejectHTMLSpecialChars(true));
        $validator->addField('pet-price', (new RangeValidation('float', 'Cena musi być dodatnia liczbą',  0, null))->setCanValueBeEmpty(true));
        $validator->addField('pet-characteristics', (new InCheckboxArrayValidation(
            'Błąd wypełniania charakterystyk',
            array_map(function ($animalFeature) {
                return $animalFeature->getId();
            }, $this->animalFeaturesRepository->getAll())
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
            $existingAnnouncement = $this->announcementsRepository->getAnnouncementWithUserContext($data['announcement-id'], $this->getLoggedUser()->getId());
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
                $animalFeatures[] = new AnimalFeature($id, null, $value);
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
            new AnimalType($data['pet-type'], null),
            $this->getLoggedUser(),
            $announcementDetail,
            false,
            null
        );

        try {
            if ($existingAnnouncement) {
                $this->announcementsRepository->updateAnnouncement($announcement);
            } else {
                $this->announcementsRepository->addAnnouncement($announcement);
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
        $id = $this->getPostAnnouncementId($this->getPOSTData());

        $currentUser = $this->getLoggedUser();
        $announcement = $this->announcementsRepository->getAnnouncementWithUserContext($id, null, false);
        $adminId = null;

        if (!$announcement || $announcement->isDeleted()) {
            $response->setError('Ogłoszenie nie istnieje lub zostało już usunięte', 400);
            $response->send();
        } else if ($currentUser->getId() !== $announcement->getUser()->getId()) {
            $this->adminPrivilegesRequired();
            $adminId = $currentUser->getId();
        }

        try {
            $this->announcementsRepository->delete($id, $adminId);
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

    public function api_announcement_like()
    {
        $this->loginRequired();
        $response = new JsonResponse();

        $id = self::getPostAnnouncementId($this->getPOSTData());
        $currentUser = $this->getLoggedUser();

        $announcement = $this->announcementsRepository->getAnnouncementWithUserContext($id, $currentUser->getId(), false);
        if (!$announcement || $announcement->isDeleted() || !$announcement->isAccepted()) {
            $response->setError('Ogłoszenie nie istnieje lub zostało usunięte', 404);
            $response->send();
        } else if ($announcement->getUser()->getId() == $currentUser->getId()) {
            $response->setError('Nie możesz polubić swojego ogłoszenia', 400);
            $response->send();
        }

        try {
            if ($announcement->isUserFavourite()) {
                $this->announcementsRepository->unlike($currentUser->getId(), $announcement->getId());
                $response->setData('unliked');
                $response->send();
            } else {
                $this->announcementsRepository->like($currentUser->getId(), $announcement->getId());
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
        return $this->announcementsRepository->getUsersFavorite($user);
    }

    public static function getPostAnnouncementId($data): ?int
    {
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            $response = new JsonResponse();
            $response->setError('Nieprawidłowy identyfikator ogłoszenia', 400);
            $response->send();
        }
        return $data['id'];
    }
}
