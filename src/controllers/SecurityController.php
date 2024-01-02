<?php

require_once 'AppController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../repository/UsersRepository.php';
require_once __DIR__ . '/../responses/JsonResponse.php';
require_once __DIR__ . '/../responses/PostFormResponse.php';
require_once __DIR__ . '/../validation/PostDataValidator.php';
require_once __DIR__ . '/../utils/utils.php';

class SecurityController extends AppController
{
    private $usersRepository;

    public function __construct()
    {
        parent::__construct();

        $this->usersRepository = new UsersRepository();
    }

    public function signin()
    {
        $response = new PostFormResponse();

        // VALIDATION
        $validator = new PostDataValidator($_POST);
        $validator->addField('login-email', new EmailValidation('Wprowadź adres e-mail'));
        $validator->addField('login-password', (new NotEmptyValidation('Wprowadź hasło'))->setSantization(false));
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
            $response->setError('Nie znaleziono użytkownika', 401);
            $response->send();
        }

        $this->getSession()->setUserID($user->getId());
        $response->setData(['userName' => $user->getFullName()]);
        $response->send();
    }

    public function signup()
    {
        $response = new PostFormResponse();

        // VALIDATION
        $validator = new PostDataValidator($_POST);
        $validator->addField('register-names', new TwoOrMoreWordsValidation('Wprowadź imię i nazwisko'));
        $validator->addField('register-email', new EmailValidation('Wprowadź adres e-mail'));
        $validator->addField('register-phone', new PhoneNumberValidation('Wprowadź prawidłowy numer telefonu'));
        $validator->addField('register-password', (new PasswordValidation())->setSantization(false));
        $validator->addField('register-repassword', (new AreValuesSameValidation('Hasła nie są takie same', 'register-password'))->setSantization(false));
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
        } catch (Exception $e) {
            $response->setError('Wystąpił wewnętrzny błąd, spróbuj ponownie później', 500);
            $response->send();
        }

        if ($new_user) {
            Logger::debug($new_user);
            Logger::debug($user);
            $this->getSession()->setUserID($user->getId());
        }
        $response->send();
    }

    public function signout()
    {
        $this->getSession()->destroy();
        if (isset($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect('/login');
        }
    }

    public function forgot_password()
    {
        $response = new JsonResponse();
        $response->setError('Ta opcja nie jest jeszcze dostępna', 501);
        $response->send();
    }
}
