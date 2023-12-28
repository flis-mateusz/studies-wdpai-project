<?php

require_once 'AppController.php';
require_once 'UserSessionController.php';
require_once __DIR__ . '/../repository/UsersRepository.php';
require_once __DIR__ . '/../models/PostFormResponse.php';
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

    public function loginRequired($redirect_uri_if_post = null): ?int
    {
        if (!$this->session->isLoggedIn()) {
            $redirect_url = '/login?required&redirect_url=' . ($this->isPost() && $redirect_uri_if_post ? $redirect_uri_if_post : $_SERVER['REQUEST_URI']);

            if ($this->isPost()) {
                $response = new JsonResponse();
                $response->setError('Nie jesteś zalogowany', 401);
                $response->setData(['redirect_url' => $redirect_url]);
                $response->send();
            } else {
                header('Location: ' . $redirect_url);
                exit;
            }
        }
        return $this->session->getUserID();
    }

    public function getSession()
    {
        return $this->session;
    }

    public function signin()
    {
        $response = new PostFormResponse();

        // VALIDATION
        $validator = new PostFormValidator($_POST);
        $validator->addField('login-email', new EmailValidation('Podaj adres e-mail'));
        $validator->addField('login-password', new NotEmptyValidation('Podaj hasło'));
        if (!$validator->validate()) {
            $errors = $validator->getErrors();
            $response->setErrorFields($errors);
            $response->send();
        }
        $data = $validator->getSanitizedData();

        // SIGNIN
        $email = $data['login-email'];
        $password = $data['login-password'];
        $user = $this->usersRepository->getUser($email);

        if ($user === null || !password_verify($password, $user->getPassword())) {
            $response->setError('Nie znaleziono użytkownika', 200);
            $response->send();
        }

        $this->session->setUserID($user->getId());
        $response->send();
    }

    public function signup()
    {
        $response = new PostFormResponse();

        // VALIDATION
        $validator = new PostFormValidator($_POST);
        $validator->addField('register-names', new TwoOrMoreWordsValidation('Podaj imię i nazwisko'));
        $validator->addField('register-email', new EmailValidation('Podaj adres e-mail'));
        $validator->addField('register-phone', new NotEmptyValidation('Podaj numer telefonu'));
        $validator->addField('register-password', new PasswordValidation());
        $validator->addField('register-repassword', new AreValuesSameValidation('Hasła nie są takie same', 'register-password'));
        if (!$validator->validate()) {
            $errors = $validator->getErrors();
            $response->setErrorFields($errors);
            $response->send();
        }
        $data = $validator->getSanitizedData();

        // SIGNUP
        $name = explode(' ', $data['register-names']);
        $email = $data['register-email'];
        $phone = $data['register-phone'];
        $password = $data['register-password'];

        $user = new User(null, $email, password_hash($password, PASSWORD_DEFAULT), null, $name[0], $name[1], null, $phone, false);

        try {
            $new_user = $this->usersRepository->addUser($user);
        } catch (EmailExistsException $e) {
            $response->addErrorField('register-email', $e->getMessage(), 409);
            $response->send();
        } catch (PhoneExistsException $e) {
            $response->addErrorField('register-phone', $e->getMessage(), 409);
            $response->send();
        }

        if ($new_user) {
            $this->session->setUserID($user->getId());
        } else {
            $response->setError('Wystąpił wewnętrzny błąd, spróbuj ponownie', 500);
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
