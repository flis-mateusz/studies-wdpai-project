<?php

require_once 'AppController.php';

class DefaultController extends AppController {
    function login() {
        include __DIR__."/../views/login.html";
    }
    public function index()
    {
        $this->render('login');
    }
    public function dashboard() {
        $title = "Śjema";
        $this->render('dashboard', ['title' => $title]);
    }
}