<?php

/**
 * @var User $user
 * @var Announcement[] $announcements
 */

require_once __DIR__ . '/../components/HeaderComponent.php';
require_once __DIR__ . '/../components/PanelSidenav.php';
require_once __DIR__ . '/../components/AnnouncementElement.php';

HeaderComponent::initialize();
PanelSidenav::initialize();
AnnouncementElement::initialize();
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/common.css">
    <?php
    ResourceManager::appendResources();
    ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Zgłoszenia</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
</head>

<body>
    <?php
    (new HeaderComponent($user))->render();
    ?>
    <main>
        <?php (new PanelSidenav($user))->render(); ?>
        <section class="panel">
            <?php if (isset($announcements) && !isEmpty($announcements)) : ?>
                <section class="panel-elements fit">
                    <?php
                    foreach ($announcements as $announcement) {
                        (new AnnouncementElement($announcement, true))->render();
                    }
                    ?>
                </section>
            <?php else : ?>
                <span>Brak zgołoszonych ogłoszeń</span>
            <?php endif; ?>
        </section>
    </main>
    <footer>
    </footer>
</body>

</html>