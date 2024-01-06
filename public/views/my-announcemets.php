<?php

/**
 * @var ?Announcement[] $announcements
 */

require_once __DIR__ . '/components/CustomContentLoader.php';
require_once __DIR__ . '/components/HeaderComponent.php';
require_once __DIR__ . '/components/PanelSidenav.php';
require_once __DIR__ . '/components/AnnouncementElement.php';

CustomContentLoader::initialize();
HeaderComponent::initialize();
PanelSidenav::initialize();
AnnouncementElement::initialize();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/common.css">
    <link rel="stylesheet" href="/public/css/components/forms.css">
    <?php
    ResourceManager::appendResources();
    ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Moje ogłoszenia</title>
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
                        (new AnnouncementElement($announcement))->render();
                    }
                    ?>
                </section>
            <?php else : ?>
                <span>Nie masz żadnych ogłoszeń</span>
            <?php endif; ?>
        </section>
    </main>
    <footer>
    </footer>
</body>

</html>