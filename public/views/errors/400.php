<?php

/**
 * @var User $user
 * @var int $errorCode
 */

require_once __DIR__ . '/../components/HeaderComponent.php';

HeaderComponent::initialize();
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
    <title>Ogłoszenia do akceptacji</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
</head>

<body>
    <?php
    (new HeaderComponent($user))->render();
    ?>
    <main>
        <div class="error-message">
            <h1><?= $errorCode ?></h1>
            <?php switch ($errorCode):
                case 403: ?>
                    <h2>Nie masz dostępu do tego zasobu</h2>
                    <?php break; ?>
                <?php
                case 404: ?>
                    <h2>Wybrana strona nie istnieje</h2>
                    <?php break; ?>
                <?php
                case 500: ?>
                    <h2>Wystąpił wewnętrzny błąd</h2>
                    <p>Spróbuj ponownie</p>
                    <?php break; ?>
                <?php
                default: ?>
                    <h2>Wystąpił błąd</h2>
                    <p>Spróbuj ponownie</p>
                    <?php break; ?>
            <?php endswitch; ?>
        </div>
        <a href="/" class="main-button">Wróć do strony głównej</a>
    </main>
    <footer>
    </footer>
</body>

</html>