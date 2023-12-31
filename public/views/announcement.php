<?php

/**
 * @var ?User $user
 */

require_once __DIR__ . '/components/HeaderComponent.php';

HeaderComponent::initialize();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="/public/css/announcement/announcement.css">
    <?php
    ResourceManager::appendResources();
    ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Ogłoszenie</title>
</head>

<body>
    <!-- <header>
        <nav>
            <div class="submenu">
                <div>
                    <a>
                        <i class="material-icons">keyboard_arrow_left</i>
                        <span class="link-text">Wróć</span>
                    </a>
                </div>
                <div>
                    <a href="/">Strona główna</a>
                    <a>Ogłoszenia</a>
                    <a>Kontakt</a>
                </div>
            </div>
            <div class="menu-dropdown">
                <input class="menu-button" type="checkbox" id="menu-button" />
                <label class="menu-icon" for="menu-button"><span class="navicon"></span></label>
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
                        <a href="#">
                            <i class="material-icons">favorite_border</i>
                            <span>Obserwowane</span>
                        </a>
                        <a href="#">
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
            </div>
        </nav>
    </header> -->
    <?php
    (new HeaderComponent($user))->render();
    ?>
    <main>
        <section class="announcement-header">
            <div>
                <span>Misziu</span>
                <span>dodano 22.10.2023</span>
            </div>
            <div class="announcement-report">
                <i class="material-icons">flag</i>
                <span>Zgłoś nadużycie</span>
            </div>
        </section>
        <section class="announcement">
            <div class="about-pet">
                <div class="like"><i class="material-icons">favorite_border</i></div>
                <div class="photo"></div>
                <div class="info">
                    <div>
                        <div>
                            <span>Imię</span>
                            <span>x</span>
                        </div>
                        <div>
                            <span>Gatunek</span>
                            <span>x</span>
                        </div>
                        <div>
                            <span>Płeć</span>
                            <span>Ona</span>
                        </div>
                    </div>
                    <hr>
                    <div class="badges">
                        <div class="not-ok"><i class="material-icons">remove_circle</i><span>Odpchlony</span></div>
                        <div><i class="material-icons">check_circle</i><span>Uwielbia spacery</span></div>
                        <div><i class="material-icons">check_circle</i><span>Szczepienia</span></div>
                        <div><i class="material-icons">check_circle</i><span>Uwielbia zabawę</span></div>
                        <div><i class="material-icons">check_circle</i><span>Przyjazny dzieciom</span></div>
                    </div>
                    <hr class="resp-only">
                </div>
                <div class="description">
                    Mysziu to przepiękny, mały kotek o kręconym futrze i delikatnych, jedwabistych futerku w odcieniach
                    szarości i białym krawacie na szyi. Jego oczy są błyszczące i niebieskie, co nadaje mu uroku i
                    wyjątkowej osobowości. Mysziu ma długie, wąskie uszy, które zawsze stoją na baczność, gotowe do
                    złapania każdego dźwięku w otoczeniu.
                    Ten mały felis catus jest nie tylko uroczy, ale także bardzo przyjazny i towarzyski. Lubuje się w
                    zabawie, szczególnie w łapaniu zabawek i porywaniu sznurków. Mysziu jest także znakomitym myśliwym i
                    często gania za małymi zabawkowymi myszkami, co sprawia mu mnóstwo frajdy.
                </div>
            </div>
            <div class="about-user">
                <div class="user-basic">
                    <div class="avatar resp"></div>
                    <div>
                        <div class="name">Anna Kowalska</div>
                        <div>
                            <i class="material-icons">location_on</i>
                            <span>Kraków</span>
                        </div>
                    </div>
                </div>
                <div class="inline">
                    <span>Cena</span>
                    <span>Oddam za darmo</span>
                </div>
                <div class="inline">
                    <span>Numer telefonu</span>
                    <span>500343211</span>
                </div>
            </div>
        </section>
    </main>
    <footer>

    </footer>
</body>

</html>