<?php

require_once 'AppController.php';
require_once __DIR__ . '/AnnouncementController.php';
require_once __DIR__ . '/../repository/AdminRepository.php';
require_once __DIR__ . '/../repository/UsersRepository.php';
require_once __DIR__ . '/../repository/AnimalTypesRepository.php';
require_once __DIR__ . '/../repository/AnimalFeaturesRepository.php';
require_once __DIR__ . '/../validation/PostDataValidator.php';


class AdminPanelController extends AppController
{
    private $adminRepository;
    private $usersRepository;
    private $animalTypesRepository;
    private $animalFeaturesRepository;
    private $supportedListsFastEditor;

    private $requiredVars;

    public function __construct()
    {
        parent::__construct();

        $this->adminPrivilegesRequired();
        $this->adminRepository = new AdminRepository();
        $this->usersRepository = new UsersRepository();
        $this->animalTypesRepository = new AnimalTypesRepository();
        $this->animalFeaturesRepository = new AnimalFeaturesRepository();
        $this->supportedListsFastEditor = ['animal_types' => $this->animalTypesRepository, 'animal_features' => $this->animalFeaturesRepository];

        $this->requiredVars = ['user' => $this->getLoggedUser()];
    }

    public function admin_approval()
    {
        $this->render('admin/admin-approval', [...$this->requiredVars, 'announcements' => $this->adminRepository->getAnnouncementsToApprove()]);
    }

    public function admin_reports()
    {
        $this->render('admin/admin-reports', [...$this->requiredVars, 'announcements' => $this->adminRepository->getReportedAnnouncements()]);
    }
    public function admin_users()
    {
        $this->render('admin/admin-users', [...$this->requiredVars, 'users' => $this->usersRepository->getAllUsers()]);
    }
    public function admin_pet_types()
    {
        $this->render('admin/admin-pet-types', [...$this->requiredVars, 'animalTypes' => $this->animalTypesRepository->getByPopularity()]);
    }
    public function admin_pet_features()
    {
        $this->render('admin/admin-pet-features', [...$this->requiredVars, 'animalFeatures' => $this->animalFeaturesRepository->getByPopularity()]);
    }

    // --------------------- ACTIONS ---------------------------
    public function api_announcement_approve()
    {
        $response = new JsonResponse();
        $data = $this->getPOSTData();
        $id = AnnouncementController::getPostAnnouncementId($this->getPOSTData());

        try {
            $this->adminRepository->approveAnnouncement($id);
        } catch (Exception $e) {
            error_log($e);
            $response->setError('Wystąpił wewnętrzny błąd, spróbuj ponownie później', 500);
            $response->send();
        }
        if (isset($data['refferer']) && $data['refferer']) {
            $refferer = ValidationStrategy::sanitizeOnly($data['refferer']);
            $response->setData(['redirect_url' => parse_url($refferer, PHP_URL_PATH)]);
        }
        $response->setData('approved');
        $response->send();
    }

    public function api_fast_list_update()
    {
        $response = new JsonResponse();

        $data = $this->getPOSTData();
        $validator = new PostDataValidator($data);

        switch ($data['action']) {
            case 'add':
                $validator->addField('value', (new MinMaxLengthValidation(null, 'Minimum 3 znaki maksymalnie 20', 3, 20)));
                break;
            case 'delete':
                $validator->addField('id', (new NumberValidation('Niewłaściwe ID')));
                break;
            default:
                $response->setError('Nieznana akcja', 400);
        }
        $validator->addField('list_name', (new InArrayValidation('Nieznana lista', ['animal_types', 'animal_features'])));

        if (!$validator->validate()) {
            $response->setError($validator->getFirstErrorMessage(), 400);
            $response->send();
        }
        $sanitizedData = $validator->getSanitizedData();

        try {
            switch ($data['action']) {
                case 'add':
                    $repository =  $this->supportedListsFastEditor[$data['list_name']];
                    if (!$repository->getByName($sanitizedData['value'])) {
                        $newId = $repository->add($sanitizedData['value']);
                        $response->setData(['id' => $newId]);
                    } else {
                        $response->setError('Taka wartość już istnieje', 400);
                        $response->send();
                    }
                    break;
                case 'delete':
                    $deleted = $this->supportedListsFastEditor[$data['list_name']]->delete($sanitizedData['id']);
                    if (!$deleted) {
                        $response->setError('Nie udało się usunąć rekordu', 500);
                        $response->send();
                    }
                    break;
            }
        } catch (Exception $e) {
            $response->setError('Wystąpił wewnętrzny błąd, spróbuj ponownie później', 500);
            $response->send();
        }
        $response->send();
    }
}
