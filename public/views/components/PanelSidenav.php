<?php

require_once __DIR__ . '/ResourceManager.php';
require_once __DIR__ . '/Component.php';

class PanelSidenav extends Component
{
    private User $user;
    private string $uri;
    
    public  function __construct($user)
    {
        $this->user = $user;
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public static function initialize()
    {
        ResourceManager::addStyle('/public/css/components/panel-sidenav.css');
        ResourceManager::addStyle('/public/css/components/panel-elements.css');
    }

    public function URIEquals($value) {
        return $this->uri === $value;
    }

    private function generateOption($uri, $text, $icon = null) {
        $activeClass = $this->URIEquals($uri) ? 'class="active"' : '';
        $iconHtml = $icon ? "<i class='material-icons'>$icon</i>" : '';

        return "<a href='$uri' $activeClass>$iconHtml<span>$text</span></a>";
    }

    public function render()
    {
        ob_start();
        include __DIR__ . '/../templates/panel_sidenav_template.php';
        echo ob_get_clean();
    }
}
