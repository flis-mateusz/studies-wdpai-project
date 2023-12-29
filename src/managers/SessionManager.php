<?php

require_once __DIR__ . '/../models/User.php';

class SessionManager
{

    public function __construct()
    {
        session_start();
    }

    public function isLoggedIn()
    {
        if (isset($_SESSION['userID']) && $_SESSION['userID'] !== null) {
            return true;
        }
        return false;
    }

    public function setUserID(int $userId)
    {
        $_SESSION['userID'] = $userId;
    }

    public function getUserID(): ?int
    {
        if (isset($_SESSION['userID'])) {
            return $_SESSION['userID'];
        }
        return null;
    }

    public function destroy()
    {
        session_destroy();
    }
}
