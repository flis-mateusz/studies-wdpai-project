<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="/public/css/header.css">
    <link rel="stylesheet" href="/public/css/announcement_add.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Dodaj ogłoszenie</title>
</head>

<body>
    <header>
        <nav>
            <div class="submenu">
                <div>
                    <span>Dodaj ogłoszenie</span>
                </div>
                <div>
                    <a href="/">Strona główna</a>
                    <a>O nas</a>
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
                        <a href="#">
                            <i class="material-icons">exit_to_app</i>
                            <span>Wyloguj</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main class="static-bgcolor">
        <form>
            <section>
                <div class="field">
                    <div>Imię*</div>
                    <div class="info">Jeśli Twój zwierzak reaguje na konkretne imię, podaj je. W przeciwnym wypadku to
                        odpowiedni
                        moment na nadanie imienia</div>
                    <div>
                        <input type="text" class="main-input" id="pet-name">
                    </div>
                </div>
                <div class="field">
                    <div>Wiek</div>
                    <div class="info">Postaraj się oszacować wiek zwierzęcia. Jeśli nie jesteś w stanie tego zrobić,
                        pozostaw to pole
                        puste</div>
                    <div>
                        <input type="text" class="main-input" id="pet-name">
                    </div>
                </div>
            </section>
            <section>
                <div class="field">
                    <div>Zdjęcie*</div>
                    <div class="info">Dodaj zdjęcie w formacie jpg, jped, png lub gif</div>
                    <div>Przeciągnij lub kliknij aby dodać zdjęcie</div>
                    <div class="tip">
                        <i class="material-icons">lightbulb_outline</i>
                        <span>Jakość zdjęcia wpływa na atrakcyjność ogłoszenia</span>
                    </div>
                </div>
            </section>
            <section>
                <div class="field">
                    <div>Typ zwierzęcia*</div>
                    <div class="info">Przykładowo kot, pies, chomik, papuga</div>
                    <div>
                        <input type="text" class="main-input" id="pet-kind">
                    </div>
                </div>
                <div class="field">
                    <div>Gatunek</div>
                    <div class="info">Jeśli nie znasz gatunku, pozostaw to pole puste</div>
                    <div>
                        <input type="text" class="main-input" id="pet-kind">
                    </div>
                </div>
            </section>
            <section>
                <div class="field">
                    <div>Cechy*</div>
                    <div class="info">Zaznacz tylko te pola, co do których masz absolutną pewność i uniknij
                        nieporozumień, lepsza
                        gorzka prawda niż słodkie kłamstwo</div>
                </div>
                <div class="characteristics">
                    <div>
                        <div>Odpchlony</div>
                        <div class="checkboxes">
                            <input type="radio" id="charac-1-yes" name="charac-1" value="1" />
                            <label class="yes" for="charac-1-yes">Tak</label>
                            <input type="radio" id="charac-1-no" name="charac-1" value="-1" />
                            <label class="no" for="charac-1-no">Nie</label>
                            <input type="radio" id="charac-1-not-sure" name="charac-1" value="0" checked />
                            <label class="not-sure" for="charac-1-not-sure">Nie wiem</label>
                        </div>
                    </div>
                    <div>
                        <div>Szczepiony</div>
                        <div class="checkboxes">
                            <input type="radio" id="charac-2-yes" name="charac-2" value="1" />
                            <label class="yes" for="charac-2-yes">Tak</label>
                            <input type="radio" id="charac-2-no" name="charac-2" value="-1" />
                            <label class="no" for="charac-2-no">Nie</label>
                            <input type="radio" id="charac-2-not-sure" name="charac-2" value="0" checked />
                            <label class="not-sure" for="charac-2-not-sure">Nie wiem</label>
                        </div>
                    </div>
                    <div>
                        <div>Przyjazny dzieciom</div>
                        <div class="checkboxes">
                            <input type="radio" id="charac-3-yes" name="charac-3" value="1" />
                            <label class="yes" for="charac-3-yes">Tak</label>
                            <input type="radio" id="charac-3-no" name="charac-3" value="-1" />
                            <label class="no" for="charac-3-no">Nie</label>
                            <input type="radio" id="charac-3-not-sure" name="charac-3" value="0" checked />
                            <label class="not-sure" for="charac-3-not-sure">Nie wiem</label>
                        </div>
                    </div>
                    <div>
                        <div>Uwielbia zabawę</div>
                        <div class="checkboxes">
                            <input type="radio" id="charac-4-yes" name="charac-4" value="1" />
                            <label class="yes" for="charac-4-yes">Tak</label>
                            <input type="radio" id="charac-4-no" name="charac-4" value="-1" />
                            <label class="no" for="charac-4-no">Nie</label>
                            <input type="radio" id="charac-4-not-sure" name="charac-4" value="0" checked />
                            <label class="not-sure" for="charac-4-not-sure">Nie wiem</label>
                        </div>
                    </div>
                </div>
            </section>
            <section>
                <div class="field">
                    <div>Opis</div>
                    <div class="info">Podaj szczegółowe informacje o zwierzaku, a unikniesz pytań od zainteresowanych
                    </div>
                    <div class="tip">
                        <i class="material-icons">lightbulb_outline</i>
                        <span>Dokładny i rzetelny opis statystycznie zwiększa szansę na adopcję zwierzaka</span>
                    </div>
                    <div>

                    </div>
                </div>

            </section>
        </form>
    </main>
    <footer>
    </footer>
</body>

</html>