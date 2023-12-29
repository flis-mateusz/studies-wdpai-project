<?php

require_once 'AppController.php';
require_once __DIR__ . '/../managers/AttachmentManager.php';
require_once __DIR__ . '/../repository/UsersRepository.php';
require_once __DIR__ . '/../responses/PostFormResponse.php';
require_once __DIR__ . '/../utils/logger.php';
require_once __DIR__ . '/../validation/PostFormValidator.php';

class ProfileController extends AppController
{
    private $usersRepository;

    public function __construct()
    {
        parent::__construct();

        $this->loginRequired();
        $this->usersRepository = new UsersRepository();
    }

    public function profile()
    {
        $this->render("profile", ['user' => $this->getLoggedUser()]);
    }

    public function profile_edit()
    {
        // VALIDATION
        $validator = new PostFormValidator($_POST);
        $validator->addField('edit-names', new TwoOrMoreWordsValidation('Wprowadź imię i nazwisko'));
        $validator->addField('edit-email', new EmailValidation('Wprowadź adres e-mail we właściwym formacie'));
        $validator->addField('edit-phone', new NotEmptyValidation('Wprowadź numer telefonu'));
        $validator->addField('edit-password', new PasswordValidation(true));
        $validator->addField('edit-repassword', new AreValuesSameValidation('Wprowadź nie są takie same', 'edit-password', true));
        if (!$validator->validate()) {
            $errors = $validator->getErrors();
            (new PostFormResponse($errors))->send();
        }
        $data = $validator->getSanitizedData();
        // END VALIDATION

        // PROFILE EDIT
        $user = $this->getLoggedUser();
        $response = new PostFormResponse();

        $name = explode(' ', $data['edit-names']);
        $email = $data['edit-email'];
        $phone = $data['edit-phone'];
        $password = $data['edit-password'];

        $avatar = new AttachmentManager($_FILES['edit-avatar']);
        if ($avatar->is_uploaded()) {
            try {
                $avatar_url = $avatar->save();
            } catch (Exception $e) {
                Logger::debug('Upload exception: ' . $e->getMessage());
                $response->setError($e->getMessage());
                $response->send();
            }
            $user->setAvatarName($avatar_url);
        }

        if (!empty($password)) {
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        }

        $user->setEmail($email);
        $user->setName($name[0]);
        $user->setSurname($name[1]);
        $user->setPhone($phone);

        try {
            $this->usersRepository->updateUser($user);
        } catch (EmailExistsException $e) {
            $response->addErrorField('edit-email', $e->getMessage(), 409);
        } catch (PhoneExistsException $e) {
            $response->addErrorField('edit-phone', $e->getMessage(), 409);
        }

        $response->send();
    }
}
