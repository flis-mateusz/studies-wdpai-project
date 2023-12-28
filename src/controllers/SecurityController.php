<?php

require_once 'AppController.php';
require_once 'UserSessionController.php';
require_once __DIR__ . '/../repository/UsersRepository.php';
require_once __DIR__ . '/../models/ValidationErrorResponse.php';
require_once __DIR__ . '/../models/JsonResponse.php';
require_once __DIR__ . '/../models/User.php';

class SecurityController extends AppController
{
    private $usersRepository;
    private $session;

    public function __construct()
    {
        parent::__construct();
        $this->session = new UserSessionController();
        $this->usersRepository = new UsersRepository();
    }

    public function login_required()
    {
        if (!$this->session->is_logged_in()) {
            if ($this->isPost()) {
                $response = new JsonResponse();
                $response->setStatusCode(401);
                $response->setError('Nie jesteś zalogowany');
                $response->send();
            } else {
                header('Location: /login?required&redirect_url=' . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
        return $this->session->get_user();
    }

    public function signin()
    {
        $response = new ValidationErrorResponse();

        $validator = new PostDataValidator();
        try {
            $validator->sanitizeData($_POST, [
                'login-email', 'login-password'
            ], true);
        } catch (Exception $e) {
            $response->setStatusCode(400);
            $response->setData($e->getMessage());
            $response->send();
        }

        $sanitizedData = $validator->getSanitizedData();

        $email = $sanitizedData['login-email'];
        $password = $sanitizedData['login-password'];
        $user = $this->usersRepository->getUser($email);

        if ($user === null) {
            $response->setError('Nie znaleziono użytkownika');
            $response->send();
        }

        if (password_verify($password, $user->getPassword())) {
            $this->session->set_user($user);
            $response->setStatusCode(200);
        } else {
            $response->setError('Nie znaleziono użytkownika');
        }
        $response->send();
    }

    public function signup()
    {
        $response = new ValidationErrorResponse();

        $validator = new PostDataValidator();
        try {
            $validator->sanitizeData($_POST, [
                'register-names', 'register-email', 'register-phone', 'register-password', 'register-repassword'
            ], true);
        } catch (Exception $e) {
            $response->setStatusCode(400);
            $response->setData($e->getMessage());
            $response->send();
        }
        $sanitizedData = $validator->getSanitizedData();

        $name = explode(' ', $sanitizedData['register-names']);
        $email = $sanitizedData['register-email'];
        $phone = $sanitizedData['register-phone'];
        $password = $sanitizedData['register-password'];
        $repassword = $sanitizedData['register-repassword'];

        if ($password != $repassword) {
            $response->addErrorField('register-repassword', 'Hasła różnią się');
            $response->send();
        }

        $user = new User(null, $email, password_hash($password, PASSWORD_DEFAULT), null, $name[0], $name[1], null, $phone, false);

        if ($this->usersRepository->getUser($email) === null) {
            if ($id = $this->usersRepository->addUser($user)) {
                $this->session->set_user($user);
                $response->setStatusCode(200);
            }
        } else {
            $response->setStatusCode(409);
            $response->setError('Użytkownik już istnieje');
        }
        $response->send();
    }

    public function signout()
    {
        $this->session->destroy();
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            header('Location: /login');
            exit;
        }
    }
}
