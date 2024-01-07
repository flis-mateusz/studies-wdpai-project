<?php

/**
 * @var User $user
 * @var User[] $users
 */

require_once __DIR__ . '/../components/CustomContentLoader.php';
require_once __DIR__ . '/../components/HeaderComponent.php';
require_once __DIR__ . '/../components/PanelSidenav.php';
require_once __DIR__ . '/../components/UserElement.php';

CustomContentLoader::initialize();
HeaderComponent::initialize();
PanelSidenav::initialize();
UserElement::initialize();
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/common.css">
    <script type="module" src="/public/js/controllers/admin/users.js" defer></script>
    <?php
    ResourceManager::appendResources();
    ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Użytkownicy</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
</head>

<body>
    <?php
    (new HeaderComponent($user))->render();
    ?>
    <main>
        <?php (new CustomContentLoader())->render(); ?>
        <?php (new PanelSidenav($user))->render(); ?>
        <section class="panel">
            <?php if (isset($users) && !empty($users)) : ?>
                <section class="panel-elements fit">
                    <?php
                    foreach ($users as $o_user) {
                        (new UserElement($o_user))->render();
                    }
                    ?>
                </section>
            <?php else : ?>
                <span>Brak użytkowników</span>
            <?php endif; ?>
        </section>
    </main>
    <footer>
    </footer>
</body>

</html>