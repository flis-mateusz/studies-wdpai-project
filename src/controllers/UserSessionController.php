<?php

require_once __DIR__ . '/../models/User.php';

class UserSessionController
{

    public function __construct()
    {
        session_start();
    }

    public function is_logged_in()
    {
        if ($this->get_user()) {
            return true;
        }
        return false;
    }

    public function set_user(User $user)
    {
        $_SESSION['user'] = serialize($user);
    }

    public function get_user(): ?User
    {
        if (isset($_SESSION['user'])) {
            return unserialize($_SESSION['user']);
        }
        return null;
    }

    public function destroy()
    {
        session_destroy();
    }
}
