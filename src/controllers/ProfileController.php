<?php

require_once 'AppController.php';
require_once __DIR__ . '/../models/announcement/Announcement.php';
require_once __DIR__ . '/../managers/AttachmentManager.php';
require_once __DIR__ . '/../repository/UsersRepository.php';
require_once __DIR__ . '/../responses/PostFormResponse.php';
require_once __DIR__ . '/../utils/logger.php';
require_once __DIR__ . '/../validation/PostDataValidator.php';

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
        Logger::debug($this->getLoggedUser());
        $this->render("profile", ['user' => $this->getLoggedUser()]);
    }

    public function profile_edit()
    {
        // VALIDATION
        $validator = new PostDataValidator($_POST);
        $validator->addField('edit-names', new TwoOrMoreWordsValidation('Wprowadź imię i nazwisko'));
        $validator->addField('edit-email', new EmailValidation('Wprowadź adres e-mail we właściwym formacie'));
        $validator->addField('edit-phone', new PhoneNumberValidation('Wprowadź numer telefonu'));
        $validator->addField('edit-password', (new PasswordValidation())->setCanValueBeEmpty(true)->setSantization(false));
        $validator->addField('edit-repassword', (new AreValuesSameValidation('Hasła różnią się', 'edit-password')));
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
                $avatarName = $avatar->save();
            } catch (Exception $e) {
                Logger::debug('Upload exception: ' . $e->getMessage());
                $response->setError($e->getMessage(), 500);
                $response->send();
            }
            if ($user->getAvatarName()) {
                AttachmentManager::delete($user->getAvatarName());
            }
            $user->setAvatarName($avatarName);
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
        } catch (Exception $e) {
            $response->setError('Wystąpił wewnętrzny błąd, spróbuj ponownie później', 500);
            $response->send();
        }

        $response->send();
    }

    public function api_profile_avatar_delete()
    {
        $user = $this->getLoggedUser();
        $response = new JsonResponse();

        if (!$user->getAvatarName()) {
            $response->setError('Nie posiadasz awataru', 400);
            $response->send();
        }

        if (!$this->usersRepository->removeAvatar($user->getId())) {
            $response->setError('Wystąpił błąd podczas usuwania awataru', 500);
        }

        AttachmentManager::delete($user->getAvatarName());

        $response->send();
    }
}
