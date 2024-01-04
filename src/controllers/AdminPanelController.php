<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/UsersRepository.php';

class AdminPanelController extends AppController
{
    private $usersRepository;
    private $requiredVars;

    public function __construct()
    {
        parent::__construct();

        $this->adminPrivilegesRequired();
        $this->usersRepository = new UsersRepository();

        $this->requiredVars = ['user' => $this->getLoggedUser()];
    }

    public function admin_approval()
    {
        $this->render('admin/admin-approval', [...$this->requiredVars]);
    }

    public function admin_reports()
    {
        $this->render('admin/admin-reports', [...$this->requiredVars]);
    }
    public function admin_users()
    {
        $this->render('admin/admin-users', [...$this->requiredVars]);
    }
    public function admin_pet_types()
    {
        $this->render('admin/admin-pet-types', [...$this->requiredVars]);
    }
    public function admin_pet_features()
    {
        $this->render('admin/admin-pet-features', [...$this->requiredVars]);
    }
}
