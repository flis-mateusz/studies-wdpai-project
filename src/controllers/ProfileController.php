<?php

require_once 'AppController.php';
require_once 'SecurityController.php';
require_once 'AttachmentController.php';
require_once __DIR__ . '/../models/PostFormResponse.php';
require_once __DIR__ . '/../repository/UsersRepository.php';
require_once __DIR__ . '/../utils/logger.php';
//require_once __DIR__ . '/../utils/validator.php';
require_once __DIR__ . '/../utils/forms/PostFormValidator.php';

// echo $_SERVER['DOCUMENT_ROOT'];

class ProfileController extends AppController
{
    private $usersRepository;
    private $securityController;
    private User $user;

    public function __construct()
    {
        parent::__construct();

        $this->securityController = new SecurityController();
        $userID = $this->securityController->loginRequired('/profile');
        
        $this->usersRepository = new UsersRepository();
        $this->user = $this->usersRepository->getUser(null, $userID);
    }

    public function profile()
    {
        $this->render("profile", ['user' => $this->user]);
    }

    public function profile_edit()
    {
        // VALIDATION
        $validator = new PostFormValidator($_POST);
        $validator->addField('edit-names', new TwoOrMoreWordsValidation('Podaj imię i nazwisko'));
        $validator->addField('edit-email', new EmailValidation('Podaj adres e-mail we właściwym formacie'));
        $validator->addField('edit-phone', new NotEmptyValidation('Podaj numer telefonu'));
        $validator->addField('edit-password', new PasswordValidation(true));
        $validator->addField('edit-repassword', new AreValuesSameValidation('Hasła nie są takie same', 'edit-password', true));
        if (!$validator->validate()) {
            $errors = $validator->getErrors();
            (new PostFormResponse($errors))->send();
        }
        $data = $validator->getSanitizedData();
        // END VALIDATION

        // PROFILE EDIT
        $response = new PostFormResponse();

        $name = explode(' ', $data['edit-names']);
        $email = $data['edit-email'];
        $phone = $data['edit-phone'];
        $password = $data['edit-password'];

        $avatar = new AttachmentController($_FILES['edit-avatar']);
        if ($avatar->is_uploaded()) {
            try {
                $avatar_url = $avatar->save();
            } catch (Exception $e) {
                $response->setError($e->getMessage());
                $response->send();
            }
            $this->user->setAvatarName($avatar_url);
        }

        if (!empty($password)) {
            $this->user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        }

        $this->user->setEmail($email);
        $this->user->setName($name[0]);
        $this->user->setSurname($name[1]);
        $this->user->setPhone($phone);

        try {
            $this->usersRepository->updateUser($this->user);
        } catch (EmailExistsException $e) {
            $response->addErrorField('edit-email', $e->getMessage(), 409);
        } catch (PhoneExistsException $e) {
            $response->addErrorField('edit-phone', $e->getMessage(), 409);
        }

        $response->send();
    }
}
