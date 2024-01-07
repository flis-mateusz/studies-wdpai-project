<?php

/**
 * @var ?User $user
 */

require_once __DIR__ . '/components/CustomContentLoader.php';
require_once __DIR__ . '/components/HeaderComponent.php';
require_once __DIR__ . '/components/ComingSoon.php';

CustomContentLoader::initialize();
HeaderComponent::initialize();
ComingSoon::initialize();
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
    <title>Wsparcie techniczne</title>
    <link rel="icon" type="image/x-icon" href="/public/images/favicon.ico">
</head>

<body>
    <?php
    (new HeaderComponent($user))->render();
    ?>
    <main>
        <?php (new ComingSoon())->render(); ?>
    </main>
    <footer>
    </footer>
</body>

</html>