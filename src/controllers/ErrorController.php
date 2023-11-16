<?php

require_once 'AppController.php';

class ErrorController extends AppController {
    public function FileNotFound() {
        $this->render('error');
    }
}