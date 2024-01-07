<?php

require_once 'src/controllers/DefaultController.php';
require_once 'src/controllers/ProfileController.php';
require_once 'src/controllers/AnnouncementController.php';
require_once 'src/controllers/ReportsController.php';
require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/SearchController.php';
require_once 'src/controllers/SupportController.php';
require_once 'src/controllers/AdminPanelController.php';

class Router
{

    public static $routes;

    public static function get($url, $view)
    {
        self::$routes[$url] = $view;
    }

    public static function post($url, $view)
    {
        self::$routes[$url] = $view;
    }

    public static function run($url)
    {
        Logger::debug($url);
        if ($url) {
            $urlParts = explode("/", $url);
            $action = $urlParts[0];
        } else {
            $action = '';
        }
        
        if (!array_key_exists($action, self::$routes)) {
            $action = 'e404';
        }

        $controller = self::$routes[$action];
        $object = new $controller;
        $action = $action ?: 'index';

        $id = $urlParts[1] ?? '';
        $object->$action($id);
    }
}
