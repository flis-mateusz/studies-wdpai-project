<?php

require_once 'AppController.php';
require_once 'SecurityController.php';
require_once 'AttachmentController.php';
require_once __DIR__ . '/../models/ValidationErrorResponse.php';
require_once __DIR__ . '/../repository/UsersRepository.php';
require_once __DIR__ . '/../utils/logger.php';
require_once __DIR__ . '/../utils/validator.php';

// echo $_SERVER['DOCUMENT_ROOT'];

class ProfileController extends AppController
{
    private $usersRepository;
    private $user;

    public function __construct()
    {
        parent::__construct();
        $security = new SecurityController();
        $this->user = $security->login_required();

        $this->usersRepository = new UsersRepository();
    }

    public function profile()
    {
        $this->render("profile", ['user' => $this->user]);
    }

    public function profile_edit()
    {
        $response = new ValidationErrorResponse();

        $validator = new PostDataValidator();
        try {
            $validator->sanitizeData($_POST, [
                'edit-names', 'edit-email', 'edit-phone', 'edit-password', 'edit-repassword'
            ]);
        } catch (Exception $e) {
            $response->setStatusCode(400);
            $response->setData($e->getMessage());
            $response->send();
        }

        $sanitizedData = $validator->getSanitizedData();

        $name = explode(' ', $sanitizedData['edit-names']);
        $email = $sanitizedData['edit-email'];
        $phone = $sanitizedData['edit-phone'];
        $password = $sanitizedData['edit-password'];
        $repassword = $sanitizedData['edit-repassword'];

        $new_user = clone $this->user;

        $avatar = new AttachmentController($_FILES['edit-avatar']);
        if ($avatar->is_uploaded()) {
            try {
                $avatar_url = $avatar->save();
            } catch (Exception $e) {
                $response->setData($e->getMessage());
                $response->send();
            }
            $new_user->setAvatarName($avatar_url);
        }

        if (!empty($password) || !empty($repassword)) {
            if ($password != $repassword) {
                $response->addErrorField('repassword', 'Hasła różnią się od siebie');
                $response->send();
            } else {
                $new_user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            }
        }

        $new_user->setEmail($email);
        $new_user->setName($name[0]);
        $new_user->setSurname($name[1]);
        $new_user->setPhone($phone);

        try {
            $this->usersRepository->updateUser($new_user);
            $response->setStatusCode(200);
        } catch (EmailExistsException $e) {
            $response->addErrorField('edit-email', $e->getMessage());
        } catch (PhoneExistsException $e) {
            $response->addErrorField('edit-phone', $e->getMessage());
        } catch (Exception $e) {
            $response->setStatusCode(400);
            $response->setError($e->getMessage());
        }

        $response->send();
    }
}
