<?php

/**
 * @var User $user
 */

require_once __DIR__ . '/../components/CustomContentLoader.php';
require_once __DIR__ . '/../components/HeaderComponent.php';
require_once __DIR__ . '/../components/SideNavLayout.php';
require_once __DIR__ . '/../components/ComingSoon.php';

CustomContentLoader::initialize();
HeaderComponent::initialize();
SideNavLayout::initialize();
ComingSoon::initialize();
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
    <title>Cechy zwierząt</title>
</head>

<body>
    <?php
    (new HeaderComponent($user))->render();
    ?>
    <main>
        <?php (new SideNavLayout($user))->render(); ?>
        <section class="panel">
            <?php (new ComingSoon())->render(); ?>
        </section>
    </main>
    <footer>
    </footer>
</body>

</html>