<?php

require_once 'AppController.php';

class SecurityController extends AppController
{

    private $userRepository;

    public function __construct()
    {
        parent::__construct();
        session_start();
        // $this->userRepository = new UserRepository();
    }

    public function is_logged_in()
    {
        if (isset($_SESSION['user_id'])) {
            return true;
        }
        return false;
    }

    public function login_required() {
        if (!$this->is_logged_in()) {
            header('Location: /login?required&redirect_url='. $_SERVER['REQUEST_URI']);
        }
    }

    public function signin()
    {
        $data = array("success" => true);
        $this->jsonResponse($data);
    }

    public function signup()
    {
        $data = array("success" => true);
        $this->jsonResponse($data);
    }

    public function signout()
    {
        session_destroy();
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            header('Location: /login'); 
            exit;
        }
    }
}
