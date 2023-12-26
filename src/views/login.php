<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="/public/css/header.css">
    <link rel="stylesheet" href="/public/css/login.css">
    <link rel="stylesheet" href="/public/css/custom_loader.css">
    <script type="module" src="/public/js/login_form.js" defer></script>
    <title>Logowanie</title>
</head>

<body>
    <header>
        <nav>
            <div class="submenu">
                <div>
                    <span class="logo">ZwierzakSzukaDomu</span>
                </div>
                <div>
                    <a>O nas</a>
                    <a>Kontakt</a>
                </div>
            </div>
            <div class="menu-dropdown">
                <input class="menu-button" type="checkbox" id="menu-button" />
                <label class="menu-icon" for="menu-button"><span class="navicon"></span></label>
                <a href="/">Strona główna</a>
            </div>
        </nav>
    </header>
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
                        <form id="login-form">
                            <div class="inputs">
                                <div>
                                    <label for="login-email">Wprowadź adres e-mail</label>
                                    <input type="email" class="main-input" id="login-email" name="login-email">
                                </div>
                                <div>
                                    <label for="login-email">Wprowadź hasło</label>
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
                        <form id="register-form">
                            <div class="inputs">
                                <div>
                                    <label for="register-names">Imię i nazwisko</label>
                                    <input type="text" class="main-input" id="register-names" name="register-names">
                                </div>
                                <div>
                                    <label for="register-email">Wprowadź adres e-mail</label>
                                    <input type="email" class="main-input" id="register-email" name="register-email">
                                </div>
                                <div>
                                    <label for="register-password">Wprowadź hasło</label>
                                    <input type="password" class="main-input" id="register-password" name="register-password">
                                </div>
                                <div>
                                    <label for="register-repassword">Powtórz hasło</label>
                                    <input type="password" class="main-input" id="register-repassword" name="register-repassword">
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
                        <form id="forgot-password-form">
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
            <div class="custom-loader-container">
                <div class="custom-loader">
                    <div class="track">
                        <div class="mouse"></div>
                    </div>
                    <div class="face">
                        <div class="ears-container"></div>
                        <div class="eyes-container">
                            <div class="eye"></div>
                            <div class="eye"></div>
                        </div>
                        <div class="phiz">
                            <div class="nose"></div>
                            <div class="lip"></div>
                            <div class="mouth"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer></footer>
</body>

</html>