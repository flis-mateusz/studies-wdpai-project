<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/DogsRepository.php';


class DefaultController extends AppController
{

    private $dogsRepository;

    public function __construct()
    {
        // parent::__construct();
        $this->dogsRepository = new DogsRepository();
    }

    public function index()
    {
        $this->render("dashboard");
    }

    public function login()
    {
        $this->render("login");
    }

    public function dashboard()
    {

        $this->render("dashboard", [
            "title" => "Hello on my dashboard",
            "dogs" => $this->dogsRepository->getDogs()
        ]);
    }
}
