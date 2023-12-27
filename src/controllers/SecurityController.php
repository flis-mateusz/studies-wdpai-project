<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/UsersRepository.php';
require_once __DIR__ . '/../models/JsonResponse.php';
require_once __DIR__ . '/../models/User.php';

class SecurityController extends AppController
{

    private $usersRepository;

    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->usersRepository = new UsersRepository();
    }

    public function is_logged_in()
    {
        if (isset($_SESSION['user_id'])) {
            return true;
        }
        return false;
    }

    public function login_required()
    {
        if (!$this->is_logged_in()) {
            header('Location: /login?required&redirect_url=' . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

    public function signin()
    {
        $response = new JsonResponse();

        if (empty($_POST['login-email']) || empty($_POST['login-password'])) {
            $response->setMessage('Email i hasło jest wymagane');
            $response->send();
        }

        $email = $_POST['login-email'];
        $password = $_POST['login-password'];
        $user = $this->usersRepository->getUser($email);

        if ($user === null) {
            $response->setMessage('Nie znaleziono użytkownika');
            $response->send();
        }

        if (password_verify($password, $user->getPassword())) {
            $_SESSION['user_id'] = $user->getId();
            $response->setSuccess(true);
        } else {
            $response->setMessage('Nie znaleziono użytkownika');
        }
        $response->send();
    }

    public function signup()
    {
        $response = new JsonResponse();

        if (empty($_POST['register-names']) || empty($_POST['register-email']) || empty($_POST['register-password']) || empty($_POST['register-repassword'])) {
            $response->setMessage('Uzupełnij wszystkie pola');
            $response->send();
        }
        $name = explode(' ', $_POST['register-names']);
        $email = $_POST['register-email'];
        $password = $_POST['register-password'];
        $repassword = $_POST['register-repassword'];

        if ($password != $repassword) {
            $response->setMessage('Hasła nie są takie same');
            $response->send();
        }

        $user = new User(null, $email, password_hash($password, PASSWORD_DEFAULT), null, $name[0], $name[1], null);

        if ($this->usersRepository->getUser($email) === null) {
            if ($id = $this->usersRepository->addUser($user)) {
                $_SESSION['user_id'] = $id;
                $response->setSuccess(true);
            }
        } else {
            $response->setMessage('Użytkownik już istnieje');
        }
        $response->send();
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
