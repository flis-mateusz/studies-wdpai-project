<?php

/**
 * @var ?User $user
 * @var ?AnimalFeature[] $animalFeatures
 * @var ?AnimalType[] $animalTypes
 */

require_once __DIR__ . '/components/HeaderComponent.php';
require_once __DIR__ . '/components/AttachmentDragDrop.php';
require_once __DIR__ . '/components/CustomContentLoader.php';
require_once __DIR__ . '/components/AnimalFeatures.php';
require_once __DIR__ . '/components/DebounceSearch.php';

HeaderComponent::initialize();
AttachmentDragDrop::initialize();
CustomContentLoader::initialize();
DebounceSearchComponent::initialize();

$petTypeSearch = new DebounceSearchComponent('pet-type', null, null, json_encode($animalTypes));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="/public/css/components/forms.css">
    <link rel="stylesheet" href="/public/css/components/custom-radio.css">
    <link rel="stylesheet" href="/public/css/announcement/announcement_add.css">
    <script type="module" src="/public/js/announcement_add.js" defer></script>
    <?php
    ResourceManager::appendResources();
    ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Dodaj ogłoszenie</title>
</head>

<body>
    <?php
    (new HeaderComponent($user))->render();
    ?>
    <main class="static-bgcolor">
        <?php
        (new CustomContentLoader())->render();
        ?>
        <form id="announcement-add-form">
            <fieldset class="scroll-to">
                <div class="field">
                    <div>Imię*</div>
                    <div class="info">Jeśli Twój zwierzak reaguje na konkretne imię, podaj je. W przeciwnym wypadku to
                        odpowiedni
                        moment na nadanie imienia</div>
                    <div>
                        <input type="text" class="main-input" name="pet-name">
                    </div>
                </div>
                <div class="field">
                    <div>Wiek</div>
                    <div class="info">Postaraj się oszacować wiek zwierzęcia. Jeśli nie jesteś w stanie tego zrobić,
                        pozostaw to pole
                        puste</div>
                    <div>
                        <div class="input-with-select input-related">
                            <input type="number" min="1" class="main-input" name="pet-age">
                            <select name="pet-age-type">
                                <option value="day">dni</option>
                                <option value="month" selected>miesięcy</option>
                                <option value="year">lat</option>
                            </select>
                        </div>
                        <span class="input-error"></span>
                    </div>
                </div>
                <div class="field">
                    <div>Płeć</div>
                    <div class="toggle row">
                        <input type="radio" name="pet-gender" value="male" id="pet-gender-male" checked="checked" />
                        <label for="pet-gender-male">On</label>
                        <input type="radio" name="pet-gender" value="female" id="pet-gender-female" />
                        <label for="pet-gender-female">Ona</label>
                    </div>
                    <span class="input-error"></span>
                </div>
            </fieldset>
            <fieldset class="scroll-to">
                <div class="field">
                    <div>Zdjęcie*</div>
                    <div class="info">Dodaj zdjęcie w formacie jpg, jped, png lub gif</div>
                    <?php
                    (new AttachmentDragDrop('pet-avatar'))->render();
                    ?>
                    <span class="input-error"></span>
                    <div class="tip">
                        <span>Jakość zdjęcia wpływa na atrakcyjność ogłoszenia</span>
                    </div>
                </div>
            </fieldset>
            <fieldset class="scroll-to">
                <div class="field">
                    <div>Typ zwierzęcia*</div>
                    <div class="info">Przykładowo kot, pies, chomik, papuga</div>
                    <div class="input-related">
                        <?php
                        $petTypeSearch->render();
                        ?>
                    </div>
                </div>
                </div>
                <div class="field">
                    <div>Gatunek</div>
                    <div class="info">Jeśli nie znasz gatunku, pozostaw to pole puste</div>
                    <div>
                        <input type="text" class="main-input" id="pet-kind" name="pet-kind">
                    </div>
                </div>
            </fieldset>
            <fieldset class="scroll-to">
                <div class="field">
                    <div>Cechy*</div>
                    <div class="info">Zaznacz tylko te pola, które dotyczą zwierzaka oraz co do których masz absolutną pewność</div>
                </div>
                <?php
                (new AnimalFeatures($animalFeatures))->render();
                ?>
                <span class="input-error pet-characteristics"></span>
            </fieldset>
            <fieldset class="scroll-to">
                <div class="field">
                    <div>Opis</div>
                    <div class="info">Podaj szczegółowe informacje o zwierzaku, a unikniesz pytań od zainteresowanych
                    </div>
                    <div>
                        <textarea name="pet-description" id="pet-description" cols="30" rows="5" class="main-input" maxlength="2000" minlength="1"></textarea>
                        <div class="counter">
                            <span class="input-error font-inherit"></span>
                            <div>
                                <span class="current">0</span><span>/ 2000</span>
                            </div>
                        </div>

                    </div>
                    <div class="tip">
                        <span>Dokładny i rzetelny opis statystycznie zwiększa szansę na adopcję zwierzaka</span>
                    </div>
                </div>
            </fieldset>
            <fieldset class="scroll-to">
                <div class="field">
                    <div>Lokalizacja*</div>
                    <div class="info">Wpisz miasto, w którym zwykle zwierzak przebywa</div>
                    <div>
                        <input type="text" class="main-input" name="pet-location">
                    </div>
                </div>
                <div class="field">
                    <div>Cena</div>
                    <div class="info">Jeśli chcesz oddać zwierzaka za darmo pozostaw to pole puste</div>
                    <div>
                        <input type="number" class="main-input" min='0' name="pet-price">
                    </div>
                </div>
            </fieldset>
            <span class="form-output"></span>
            <div>
                <input type="submit" value="Dodaj ogłoszenie" class="main-button">
            </div>
        </form>
    </main>
    <footer>
    </footer>

    <?php
    $petTypeSearch->renderScript();
    ?>
</body>

</html>