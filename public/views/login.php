<?php
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
    <link rel="stylesheet" href="/public/css/login.css">
    <link rel="stylesheet" href="/public/css/forms.css">
    <script type="module" src="/public/js/login-form.js" defer></script>
    <?php
    ResourceManager::appendResources();
    ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Logowanie</title>
</head>

<body>
    <?php
    (new HeaderComponent($user))->render();
    ?>
    <main>
        <?php
        if (isset($_GET['required'])) {
            echo '<span class="login-required">Aby przejść dalej musisz się zalogować lub utworzyć konto</span>';
        }
        ?>
        <section class="welcome-image"></section>
        <section class="froms-frame">
            <section class="forms-container">
                <section class="login-section">
                    <div>
                        <span>Witaj ponownie</span>
                        <span>Zaloguj się lub utwórz konto za darmo</span>
                        <hr>
                        <form id="login-form" class="with-loader">
                            <div class="inputs">
                                <div>
                                    <label for="login-email"><span>Wprowadź adres e-mail</span></label>
                                    <input type="email" class="main-input" id="login-email" name="login-email">
                                </div>
                                <div>
                                    <label for="login-email"><span>Wprowadź hasło</span></label>
                                    <input type="password" class="main-input" id="login-password" name="login-password">
                                    <span class="switch-form forgot-password">Zapomniałem/am hasła</span>
                                </div>
                            </div>
                            <span class="output"></span>
                            <input type="submit" value="Zaloguj" class="main-button">
                            <span class="incentive switch-form">Zarejestruj się</span>
                        </form>
                    </div>
                </section>
                <section class="register-section">
                    <div>
                        <span>Rejestracja</span>
                        <span>Utwórz konto za darmo</span>
                        <hr>
                        <form id="register-form" autocomplete="off" class="with-loader">
                            <div class="inputs">
                                <div>
                                    <label for="register-names"><span>Imię i nazwisko</span></label>
                                    <input type="text" autocomplete="off" class="main-input" id="register-names" name="register-names">
                                </div>
                                <div>
                                    <label for="register-email"><span>Wprowadź adres e-mail</span></label>
                                    <input type="email" autocomplete="off" class="main-input" id="register-email" name="register-email">
                                </div>
                                <div>
                                    <label for="register-phone"><span>Wprowadź numer telefonu</span></label>
                                    <input type="text" autocomplete="new-password" class="main-input" id="register-phone" name="register-phone">
                                </div>
                                <div>
                                    <label for="register-password"><span>Wprowadź hasło</span></label>
                                    <input type="password" autocomplete="new-password" class="main-input" id="register-password" name="register-password">
                                </div>
                                <div>
                                    <label for="register-repassword"><span>Powtórz hasło</span></label>
                                    <input type="password" autocomplete="new-password" class="main-input" id="register-repassword" name="register-repassword">
                                </div>
                            </div>
                            <span class="output"></span>
                            <input type="submit" value="Zarejestruj się" class="main-button">
                            <span class="incentive switch-form">Posiadasz konto? Zaloguj się</span>
                        </form>
                    </div>
                </section>
                <section class="forgot-password-section">
                    <div>
                        <span>Przypomnij hasło</span>
                        <span>Na podany adres e-mail otrzymasz link do zresetowania hasła</span>
                        <hr>
                        <form id="forgot-password-form" class="with-loader">
                            <div class="inputs">
                                <div>
                                    <label for="forgot-password-email">Wprowadź adres e-mail</label>
                                    <input type="text" class="main-input" id="forgot-password-email" name="forgot-password-email">
                                </div>
                            </div>
                            <span class="output"></span>
                            <input type="submit" value="Wyślij link" class="main-button">
                            <span class="incentive switch-form forgot-password">Wróć do logowania</span>
                        </form>
                    </div>
                </section>
            </section>
            <?php 
                (new CustomContentLoader())->render();
            ?>
        </section>
    </main>
    <footer></footer>
</body>

</html>