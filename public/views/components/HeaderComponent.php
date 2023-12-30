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
        ResourceManager::addStyle('/public/css/header.css');
    }

    public function render()
    {
        ob_start();
        include __DIR__ . '/../templates/header_template.php';
        echo ob_get_clean();
    }

    public function showLogo()
    {
        return in_array($this->uri, ['/login', '/add_announcement']);
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

    public function render2()
    {
        ob_start();
?>
        <header>
            <nav>
                <div class="submenu">
                    <?php if ($this->uri == '/login' || $this->uri == '/add_announcement') : ?>
                        <div>
                            <span class="logo">ZwierzakSzukaDomu</span>
                        </div>
                    <?php endif; ?>
                    <?php if (strpos($this->uri, 'announcement/')) : ?>
                        <div>
                            <a href="<?php urlencode(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH)); ?>/">
                                <i class="material-icons">keyboard_arrow_left</i>
                                <span class="link-text">Wróć</span>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div>
                        <?php if ($this->uri != '/login' && $this->uri != '/') : ?>
                            <a href="/">Strona główna</a>
                        <?php endif; ?>
                        <a>Ogłoszenia</a>
                        <a>Kontakt</a>
                    </div>
                </div>
                <div class="menu-dropdown">
                    <input class="menu-button" type="checkbox" id="menu-button" />
                    <label class="menu-icon" for="menu-button"><span class="navicon"></span></label>

                    <?php if ($this->user) : ?>
                        <div class="avatar"></div>
                        <div class="menu-dropdown-content">
                            <div>
                                <a href="/add_announcement">
                                    <i class="material-icons">add_circle_outline</i>
                                    <span>Dodaj ogłoszenie</span>
                                </a>
                                <a href="/profile">
                                    <i class="material-icons">account_circle</i>
                                    <span>Mój profil</span>
                                </a>
                                <a href="/favorite">
                                    <i class="material-icons">favorite_border</i>
                                    <span>Obserwowane</span>
                                </a>
                                <a href="/help">
                                    <i class="material-icons">help_outline</i>
                                    <span>Pomoc</span>
                                </a>
                                <hr>
                                <a href="/signout">
                                    <i class="material-icons">exit_to_app</i>
                                    <span>Wyloguj</span>
                                </a>
                            </div>
                        </div>
                    <?php else : ?>
                        <?php if ($this->uri == '/login') : ?>
                            <a href="/">Strona główna</a>
                        <?php else : ?>
                            <a href="/login">Zaloguj się</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </nav>
        </header>
        <?php if ($this->uri) : ?>
        <?php endif; ?>
<?php
        echo ob_get_clean();
    }
}
