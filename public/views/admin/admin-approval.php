<?php

/**
 * @var User $user
 * @var ?Announcement[] $announcements
 */

require_once __DIR__ . '/../components/CustomContentLoader.php';
require_once __DIR__ . '/../components/HeaderComponent.php';
require_once __DIR__ . '/../components/SideNavLayout.php';
require_once __DIR__ . '/../components/AnnouncementElement.php';

CustomContentLoader::initialize();
HeaderComponent::initialize();
SideNavLayout::initialize();
AnnouncementElement::initialize();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="/public/css/components/forms.css">
    <?php
    ResourceManager::appendResources();
    ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Ogłoszenia do akceptacji</title>
</head>

<body>
    <?php
    (new HeaderComponent($user))->render();
    ?>
    <main>
        <?php (new SideNavLayout($user))->render(); ?>
        <section class="panel">
        <?php if (isset($announcements) && !isEmpty($announcements)) : ?>
                <section class="announcements-list fit">
                    <?php
                    foreach ($announcements as $announcement) {
                        (new AnnouncementElement($announcement, true))->render();
                    }
                    ?>
                </section>
            <?php else : ?>
                <span>Brak ogłoszeń do akceptacji</span>
            <?php endif; ?>
        </section>
    </main>
    <footer>
    </footer>
</body>

</html>