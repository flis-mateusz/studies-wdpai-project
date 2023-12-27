<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="/public/css/header.css">
    <link rel="stylesheet" href="/public/css/profile.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Mój profil</title>
</head>

<body>
    <header>
        <nav>
            <div class="submenu">
                <div>
                    <a href="/">Strona główna</a>
                    <a>Ogłoszenia</a>
                    <a>Kontakt</a>
                </div>
            </div>
            <div class="menu-dropdown">
                <input class="menu-button" type="checkbox" id="menu-button" />
                <label class="menu-icon" for="menu-button"><span class="navicon"></span></label>
                <div class="avatar"></div>
                <div class="menu-dropdown-content">
                    <div>
                        <a href="/add_announcement">
                            <i class="material-icons">add_circle_outline</i>
                            <span>Dodaj ogłoszenie</span>
                        </a>
                        <a href="/profile">
                            <i class="material-icons">account_circle</i>
                            <span>Mój profil</span>
                        </a>
                        <a href="#">
                            <i class="material-icons">favorite_border</i>
                            <span>Obserwowane</span>
                        </a>
                        <a href="#">
                            <i class="material-icons">help_outline</i>
                            <span>Pomoc</span>
                        </a>
                        <hr>
                        <a href="/signout">
                            <i class="material-icons">exit_to_app</i>
                            <span>Wyloguj</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
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
            <form>
                <div class="basic-info">
                    <div>
                        <div>
                            <label for="edit-name"><span>Imię i nazwisko</span></label>
                            <input type="text" class="main-input" id="edit-name">
                        </div>
                        <div>
                            <label for="edit-email"><span>Wprowadź adres e-mail</span></label>
                            <input type="text" class="main-input" id="edit-email">
                        </div>
                    </div>
                    <div class="avatar resp">
                        <div>
                            <i class="material-icons">file_upload</i>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="edit-phone"><span>Wprowadź numer telefonu</span></label>
                    <input type="password" autocomplete="new-password" class="main-input" id="edit-phone">
                </div>
                <div>
                    <label for="edit-password"><span>Wprowadź hasło</span></label>
                    <input type="password" autocomplete="new-password" class="main-input" id="edit-password">
                </div>
                <div>
                    <label for="edit-email"><span>Powtórz hasło</span></label>
                    <input type="password" autocomplete="new-password" class="main-input" id="edit-repassword">
                </div>
                <div>
                    <input type="submit" value="Zapisz" class="main-button normal-text">
                </div>
            </form>
        </section>
    </main>
    <footer>
    </footer>
</body>

</html>