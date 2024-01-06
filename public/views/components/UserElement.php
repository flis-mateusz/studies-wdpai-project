<?php

require_once __DIR__ . '/ResourceManager.php';
require_once __DIR__ . '/Component.php';

class UserElement extends Component
{
    private User $user;
    private bool $attached;

    public function __construct($user, $attached=false)
    {
        $this->user = $user;
        $this->attached = $attached;
    }

    public static function initialize()
    {
        ResourceManager::addStyle('/public/css/components/user-element.css');
    }

    public function render()
    {
        ob_start();
        include __DIR__ . '/../templates/user_element_template.php';
        echo ob_get_clean();
    }
}
