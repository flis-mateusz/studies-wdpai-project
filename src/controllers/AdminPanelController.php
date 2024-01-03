<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/UsersRepository.php';

class AdminPanelController extends AppController
{
    private $usersRepository;
    private $reuiredVars;

    public function __construct()
    {
        parent::__construct();

        $this->adminPrivilegesRequired();
        $this->usersRepository = new UsersRepository();

        $this->reuiredVars = ['user' => $this->getLoggedUser()];
    }

    public function admin_approval()
    {
        $this->render('admin/admin-approval', [...$this->reuiredVars]);
    }

    public function admin_reports()
    {
        $this->render('admin/admin-reports', [...$this->reuiredVars]);
    }
    public function admin_users()
    {
        $this->render('admin/admin-users', [...$this->reuiredVars]);
    }
    public function admin_pet_types()
    {
        $this->render('admin/admin-pet-types', [...$this->reuiredVars]);
    }
    public function admin_pet_features()
    {
        $this->render('admin/admin-pet-features', [...$this->reuiredVars]);
    }
}
