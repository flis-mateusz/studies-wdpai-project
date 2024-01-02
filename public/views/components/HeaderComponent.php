<?php

require_once __DIR__ . '/ResourceManager.php';
require_once __DIR__ . '/Component.php';

class HeaderComponent extends Component
{
    private ?User $user;
    private string $uri;

    public function __construct(?User $user)
    {
        $this->user = $user;
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public static function initialize()
    {
        ResourceManager::addStyle('/public/css/components/header.css');
    }

    public function render()
    {
        ob_start();
        include __DIR__ . '/../templates/header_template.php';
        echo ob_get_clean();
    }

    public function showLogo()
    {
        return in_array($this->uri, ['/login', '/add']);
    }

    public function showBackLink()
    {
        return str_contains($this->uri, 'announcement/') !== false;
    }

    public function showHomePageLink()
    {
        return !in_array($this->uri, ['/login', '/']);
    }

    public function showHomePageLinkFirstPlace()
    {
        return in_array($this->uri, ['/login']);
    }

    public function isLoggedIn()
    {
        return $this->user !== null;
    }

    public function URIEquals($value) {
        return $this->uri === $value;
    }

    private function renderNavLink($uri, $text, $icon = null) {
        $activeClass = $this->URIEquals($uri) ? 'class="active"' : '';
        $iconHtml = $icon ? "<i class='material-icons'>$icon</i>" : '';

        return "<a href='$uri' $activeClass>$iconHtml<span>$text</span></a>";
    }
}
