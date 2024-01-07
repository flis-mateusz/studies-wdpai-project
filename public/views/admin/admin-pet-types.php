<?php

/**
 * @var User $user
 * @var AnimalType[] $animalTypes
 */

require_once __DIR__ . '/../components/CustomContentLoader.php';
require_once __DIR__ . '/../components/HeaderComponent.php';
require_once __DIR__ . '/../components/PanelSidenav.php';

CustomContentLoader::initialize();
HeaderComponent::initialize();
PanelSidenav::initialize();
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/common.css">
    <link rel="stylesheet" href="/public/css/components/admin-elements-list.css">
    <script type="module" src="/public/js/controllers/admin/types-list.js" defer></script>
    <?php
    ResourceManager::appendResources();
    ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Typy zwierząt</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
</head>

<body>
    <?php
    (new HeaderComponent($user))->render();
    ?>
    <main>
        <?php (new PanelSidenav($user))->render(); ?>
        <section class="panel">
            <?php (new CustomContentLoader())->render(); ?>
            <label class="icon-input right">
                <input type="text" class="main-input" placeholder="Dodaj typ">
                <i class="material-icons action-add">add_circle_outline</i>
            </label>
            <span class="input-error"></span>
            <div class="list types">
                <div>
                    <div class="action">
                        <span>Nazwa</span>
                    </div>
                    <div>
                        <span>Liczba użyć</span>
                    </div>
                </div>
                <?php
                foreach ($animalTypes as $type) {
                    echo <<<HTML
                        <div>
                            <div class="action">
                                <i class="material-icons action-delete" data-id="{$type->getId()}">delete_forever</i>
                                <span>{$type->getName()}</span>
                            </div>
                            <div>
                                <span>{$type->getUsageCount()}</span>
                            </div>
                        </div>
                    HTML;
                }
                ?>
            </div>
        </section>
    </main>
    <footer>
    </footer>
</body>

</html>