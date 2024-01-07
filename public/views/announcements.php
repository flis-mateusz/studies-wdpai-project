<?php

/**
 * @var ?User $user
 * @var ?Announcement[] $announcements
 * @var ?AnimalFeature[] $animalFeatures
 * @var ?AnimalType[] $animalTypes
 */

require_once __DIR__ . '/components/CustomContentLoader.php';
require_once __DIR__ . '/components/HeaderComponent.php';
require_once __DIR__ . '/components/AnnouncementElement.php';
require_once __DIR__ . '/components/FilterSelect.php';

CustomContentLoader::initialize();
HeaderComponent::initialize();
AnnouncementElement::initialize();
FilterSelect::initialize();
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/common.css">
    <link rel="stylesheet" href="/public/css/components/forms.css">
    <link rel="stylesheet" href="/public/css/components/panel-sidenav.css">
    <link rel="stylesheet" href="/public/css/announcements-filters.css">
    <link rel="stylesheet" href="/public/css/announcements.css">
    <script type="module" src="/public/js/announcements.js" defer></script>
    <?php
    ResourceManager::appendResources();
    ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Ogłoszenia</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
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
                <?php
                (new FilterSelect([$this->user ? ['id' => 'favourite', 'name' => 'Obserwowane'] : null, ['id' => 'price', 'name' => 'Oddam za darmo']], null, 'other'))->render();
                (new FilterSelect($animalTypes, 'Typy zwierząt', 'animal-types'))->render();
                (new FilterSelect($animalFeatures, 'Cechy szczególne', 'animal-features'))->render();
                ?>
            </div>
            <div>
                <span class="main-button action-search">Szukaj</span>
            </div>
        </section>
        <section class="panel announcements">
            <?php
            (new CustomContentLoader())->render();
            ?>
            <div id="search">
                <label class="icon-input left right">
                    <i class="material-icons">search</i>
                    <input type="text" class="main-input search-input" placeholder="Wyszukaj">
                    <i class="material-icons action-clear-search">clear</i>
                </label>
            </div>
            <div class="announcements-container">
                <span class="api-output">Nie znaleziono ogłoszeń</span>
                <section class="panel-elements fit">
                </section>
        </section>
    </main>
    <footer>
    </footer>
</body>

</html>