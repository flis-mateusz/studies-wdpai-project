<?php

require 'Routing.php';

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

Router::get('', 'DefaultController');
Router::get('index', 'DefaultController');
Router::get('dashboard', 'DefaultController');
Router::get('login', 'DefaultController');
Router::get('help', 'DefaultController');

Router::post('api_login', 'SecurityController');
Router::post('api_register', 'SecurityController');
Router::post('api_forgot_password', 'SecurityController');
Router::get('signout', 'SecurityController');

Router::get('profile', 'ProfileController');
Router::post('my_announcements', 'ProfileController');
Router::post('api_profile_edit', 'ProfileController');
Router::post('api_profile_avatar_delete', 'ProfileController');

Router::get('add', 'AnnouncementController');
Router::get('edit', 'AnnouncementController');
Router::get('announcement', 'AnnouncementController');
Router::get('announcements', 'AnnouncementController');
Router::post('api_add', 'AnnouncementController');
Router::post('api_announcement_like', 'AnnouncementController');
Router::post('api_announcement_delete', 'AnnouncementController');
Router::post('api_announcement_report', 'AnnouncementController');

Router::get('query_animal_types', 'SearchController');

Router::get('support', 'SupportController');

Router::get('admin_approval', 'AdminPanelController');
Router::get('admin_reports', 'AdminPanelController');
Router::get('admin_users', 'AdminPanelController');
Router::get('admin_pet_types', 'AdminPanelController');
Router::get('admin_pet_features', 'AdminPanelController');
Router::post('api_announcement_approve', 'AdminPanelController');

Router::run($path);
