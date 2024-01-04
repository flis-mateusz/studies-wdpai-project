<?php

require_once __DIR__ . '/ResourceManager.php';
require_once __DIR__ . '/Component.php';

class AnnouncementElement extends Component
{
    private Announcement $announcement;
    private bool $withUserInfo;

    public function __construct($announcement, $withUserInfo = false)
    {
        $this->announcement = $announcement;
        $this->withUserInfo = $withUserInfo;
    }

    public static function initialize()
    {
        ResourceManager::addStyle('/public/css/announcement/announcement_element.css');
    }

    public function render()
    {
        ob_start();
        include __DIR__ . '/../templates/announcement_element_template.php';
        echo ob_get_clean();
    }
}
