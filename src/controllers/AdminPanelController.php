<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/AdminRepository.php';
require_once __DIR__ . '/../repository/UsersRepository.php';

class AdminPanelController extends AppController
{
    private $adminRepository;
    private $usersRepository;
    
    private $requiredVars;

    public function __construct()
    {
        parent::__construct();

        $this->adminPrivilegesRequired();
        $this->adminRepository = new AdminRepository();
        $this->usersRepository = new UsersRepository();

        $this->requiredVars = ['user' => $this->getLoggedUser()];
    }

    public function admin_approval()
    {
        $this->render('admin/admin-approval', [...$this->requiredVars, 'announcements' => $this->adminRepository->getAnnouncementsToApprove()]);
    }

    public function admin_reports()
    {
        $this->render('admin/admin-reports', [...$this->requiredVars]);
    }
    public function admin_users()
    {
        $this->render('admin/admin-users', [...$this->requiredVars, 'users' => $this->usersRepository->getAllUsers()]);
    }
    public function admin_pet_types()
    {
        $this->render('admin/admin-pet-types', [...$this->requiredVars]);
    }
    public function admin_pet_features()
    {
        $this->render('admin/admin-pet-features', [...$this->requiredVars]);
    }

    // --------------------- ACTIONS ---------------------------
    public function api_announcement_approve()
    {
        $response = new JsonResponse();
        $data = $this->getPOSTData();
        $id = $this->getPostAnnouncementId();

        try {
            $this->adminRepository->AnnouncementApprove($id);
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
