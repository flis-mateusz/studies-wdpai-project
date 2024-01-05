<?php

/**
 * @var ?User $user
 * @var ?Announcement[] $announcements
 */

require_once __DIR__ . '/components/CustomContentLoader.php';
require_once __DIR__ . '/components/HeaderComponent.php';
require_once __DIR__ . '/components/AnnouncementElement.php';

CustomContentLoader::initialize();
HeaderComponent::initialize();
AnnouncementElement::initialize();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/common.css">
    <link rel="stylesheet" href="/public/css/components/forms.css">
    <link rel="stylesheet" href="/public/css/components/panel-sidenav.css">
    <link rel="stylesheet" href="/public/css/announcements-filters.css">
    <script type="module" src="/public/js/announcements.js" defer></script>
    <?php
    ResourceManager::appendResources();
    ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Ogłoszenia</title>
</head>

<body>
    <?php
    (new HeaderComponent($user))->render();
    ?>
    <main>
        <section class="sidenav">
            <div class="nav-expander">
                <input class="menu-button" type="checkbox" id="sidenav-button" />
                <label class="menu-icon" for="sidenav-button">
                    <i class="material-icons">keyboard_arrow_right</i>
                </label>
            </div>
            <div>

            </div>
            <div>
                <span class="main-button">Szukaj</span>
            </div>
        </section>
        <section class="announcements">
            <div id="search">
                <label class="icon-input two">
                    <i class="material-icons">search</i>
                    <input type="text" class="main-input search-input" placeholder="Wyszukaj">
                    <i class="material-icons">clear</i>
                </label>
            </div>
            <div>
                <?php if (isset($announcements) && !isEmpty($announcements)) : ?>
                    <section class="announcements-list fit">
                        <?php
                        foreach ($announcements as $announcement) {
                            (new AnnouncementElement($announcement))->render();
                        }
                        ?>
                    </section>
                <?php else : ?>
                    <span>Nie znaleziono ogłoszeń</span>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <footer>
    </footer>
</body>

</html>