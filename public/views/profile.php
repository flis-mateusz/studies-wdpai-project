<?php

/**
 * @var ?User $user
 */

require_once __DIR__ . '/components/CustomContentLoader.php';
require_once __DIR__ . '/components/HeaderComponent.php';

CustomContentLoader::initialize();
HeaderComponent::initialize();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="/public/css/profile/profile-edit.css">
    <link rel="stylesheet" href="/public/css/components/forms.css">
    <script type="module" src="/public/js/profile-edit.js" defer></script>
    <?php
    ResourceManager::appendResources();
    ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Mój profil</title>
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
                <div>
                    <i class="material-icons">folder_open</i>
                    <span>Moje ogłoszenia</span>
                </div>
                <div class="active">
                    <i class="material-icons">mode_edit</i>
                    <span>Edycja profilu</span>
                </div>
                <div>
                    <i class="material-icons">help_outline</i>
                    <span>Pomoc</span>
                </div>
                <span>Panel administratora</span>
                <div>
                    <i class="material-icons">streetview</i>
                    <span>Ogłoszenia do akceptacji</span>
                </div>
                <div>
                    <i class="material-icons">verified_user</i>
                    <span>Zgłoszenia</span>
                </div>
                <div>
                    <i class="material-icons">supervisor_account</i>
                    <span>Użytkownicy</span>
                </div>
                <div>
                    <i class="material-icons">toc</i>
                    <span>Rasy</span>
                </div>
                <div>
                    <i class="material-icons">toc</i>
                    <span>Cechy zwierząt</span>
                </div>
            </div>
            <div>
                <a class="main-button">
                    <i class="material-icons">exit_to_app</i>
                    <span>Wyloguj</span>
                </a>
            </div>
        </section>
        <section class="panel">
            <form id="profile-edit-form" class="with-absolute-loader">
                <div>
                    <section>
                        <div>
                            <label for="edit-names"><span>Twoje imię i nazwisko</span></label>
                            <input type="text" class="main-input" id="edit-names" name="edit-names" value="<?php echo $user->getFullName(); ?>">
                        </div>
                        <div>
                            <label for="edit-email"><span>Twój adres e-mail</span></label>
                            <input type="email" class="main-input" id="edit-email" name="edit-email" value="<?php echo $user->getEmail(); ?>">
                        </div>
                        <div>
                            <label for="edit-phone"><span>Twój numer telefonu</span></label>
                            <input type="text" autocomplete="new-password" class="main-input" id="edit-phone" name="edit-phone" value="<?php echo $user->getPhone(); ?>">
                        </div>
                        <div>
                            <label for="edit-password"><span>Ustaw nowe hasło</span></label>
                            <input type="password" autocomplete="new-password" class="main-input" id="edit-password" name="edit-password">
                        </div>
                        <div>
                            <label for="edit-email"><span>Powtórz nowe hasło</span></label>
                            <input type="password" autocomplete="new-password" class="main-input" id="edit-repassword" name="edit-repassword">
                        </div>
                    </section>
                    <section class="align-end">
                        <div class="avatar-form">
                            <div class="avatar-container">
                                <input class="mobile-avatar-checkbox" type="checkbox" id="mobile-avatar-checkbox" />
                                <label class="mobile-avatar-checkbox-overlay" for="mobile-avatar-checkbox"> </label>
                                <div class="avatar resp" <?php
                                                            if ($user->getAvatarUrl()) {
                                                                echo 'style="background-image: url(' . $user->getAvatarUrl() . ');"';
                                                            }
                                                            ?>>
                                    <input type="file" class="main-input" id="edit-avatar" name="edit-avatar">
                                </div>
                                <label for="edit-avatar" class="avatar-action upload">
                                    <i class="material-icons">file_upload</i>
                                </label>
                                <label class="avatar-action remove <?php echo $user->getAvatarUrl() ? '' : 'hidden' ?>">
                                    <i class="material-icons">delete_forever</i>
                                </label>
                            </div>
                            <div class="tip avatar-tip hidden">
                                <span>Zmiany nie są jeszcze zapisane</span>
                            </div>
                        </div>
                    </section>
                </div>
                <span class="form-output"></span>
                <div class="submit-container">
                    <input type="submit" value="Zapisz" class="main-button normal-text">
                </div>
                <?php
                (new CustomContentLoader())->render();
                ?>
            </form>
        </section>
    </main>
    <footer>
    </footer>
</body>

</html>