<?php

require 'Routing.php';

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url( $path, PHP_URL_PATH);

Router::get('', 'DefaultController');
Router::get('index', 'DefaultController');
Router::get('dashboard', 'DefaultController');
Router::get('login', 'DefaultController');

Router::post('signin', 'SecurityController');
Router::post('signup', 'SecurityController');
Router::get('signout', 'SecurityController');

Router::get('profile', 'ProfileController');

Router::get('add_announcement', 'AnnouncementController');
Router::get('announcement', 'AnnouncementController');

Router::run($path);
