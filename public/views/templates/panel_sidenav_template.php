<?php

/**
 * @var PanelSidenav $this
 */
?>

<section class="sidenav">
    <div class="nav-expander">
        <input class="menu-button" type="checkbox" id="sidenav-button" />
        <label class="menu-icon" for="sidenav-button">
            <i class="material-icons">keyboard_arrow_right</i>
        </label>
    </div>
    <div>
        <?= $this->generateOption('/my_announcements', 'Moje ogłoszenia', 'folder_open'); ?>
        <?= $this->generateOption('/profile', 'Edycja profilu', 'mode_edit'); ?>
        <?= $this->generateOption('/support', 'Wsparcie', 'help_outline'); ?>
        <?php if ($this->user->isAdmin()) : ?>
            <span>Panel administratora</span>
            <?= $this->generateOption('/admin_approval', 'Ogłoszenia do akceptacji', 'streetview'); ?>
            <?= $this->generateOption('/admin_reports', 'Zgłoszenia', 'verified_user'); ?>
            <?= $this->generateOption('/admin_users', 'Użytkownicy', 'supervisor_account'); ?>
            <?= $this->generateOption('/admin_pet_types', 'Typy zwierząt', 'toc'); ?>
            <?= $this->generateOption('/admin_pet_features', 'Cechy zwierząt', 'toc'); ?>
        <?php endif; ?>
    </div>
    <div>
        <a href='/signout' class="main-button">
            <i class="material-icons">exit_to_app</i>
            <span>Wyloguj</span>
        </a>
    </div>
</section>