<header>
    <nav>
        <div class="submenu">
            <?php if ($this->showLogo()) : ?>
                <div><a class="logo" href="/">ZwierzakSzukaDomu</a></div>
            <?php endif; ?>

            <?php if ($this->showBackLink()) : ?>
                <div>
                    <a onclick="history.back()">
                        <i class="material-icons">keyboard_arrow_left</i>
                        <span class="link-text">Wróć</span>
                    </a>
                </div>
            <?php endif; ?>

            <div>
                <?php if ($this->showHomePageLink()) : ?>
                    <?php echo $this->renderNavLink('/', 'Strona główna'); ?>
                <?php endif; ?>
                <?php echo $this->renderNavLink('/announcements', 'Ogłoszenia'); ?>
                <?php echo $this->renderNavLink('/contact', 'Kontakt'); ?>
            </div>
        </div>

        <div class="menu-dropdown">
            <input class="menu-button" type="checkbox" id="menu-button" />
            <label class="menu-icon" for="menu-button"><span class="navicon"></span></label>

            <?php if ($this->isLoggedIn()) : ?>
                <div class="avatar" <?php
                                    if ($this->user->getAvatarUrl()) {
                                        echo 'style="background-image: url(' . $this->user->getAvatarUrl() . ');"';
                                    }
                                    ?>>
                </div>
                <div class="menu-dropdown-content">
                    <div>
                        <?php echo $this->renderNavLink('/add', 'Dodaj ogłoszenie', 'add_circle_outline'); ?>
                        <?php echo $this->renderNavLink('/profile', 'Mój profil', 'account_circle'); ?>
                        <?php echo $this->renderNavLink('/favorite', 'Obserwowane', 'favorite_border'); ?>
                        <?php echo $this->renderNavLink('/help', 'Pomoc', 'help_outline'); ?>
                        <hr>
                        <?php echo $this->renderNavLink('/signout', 'Wyloguj', 'exit_to_app'); ?>
                    </div>
                </div>
            <?php else : ?>
                <?php if ($this->showHomePageLinkFirstPlace()) : ?>
                    <a href="/">Strona główna</a>
                <?php else : ?>
                    <a href="/login">Zaloguj się</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </nav>
</header>
