<?php

require 'Routing.php';

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

Router::get('', 'DefaultController');
Router::get('index', 'DefaultController');
Router::get('dashboard', 'DefaultController');
Router::get('login', 'DefaultController');

Router::post('api_login', 'SecurityController');
Router::post('api_register', 'SecurityController');
Router::post('api_forgot_password', 'SecurityController');
Router::get('signout', 'SecurityController');

Router::get('profile', 'ProfileController');
Router::post('profile_edit', 'ProfileController');
Router::post('api_profile_avatar_delete', 'ProfileController');

Router::get('add', 'AnnouncementController');
Router::get('announcement', 'AnnouncementController');
Router::post('api_add', 'AnnouncementController');
Router::post('api_announcement_like', 'AnnouncementController');
Router::post('api_announcement_delete', 'AnnouncementController');
Router::post('api_announcement_approve', 'AnnouncementController');
Router::post('api_announcement_report', 'AnnouncementController');


Router::get('query_animal_types', 'SearchController');

Router::run($path);
