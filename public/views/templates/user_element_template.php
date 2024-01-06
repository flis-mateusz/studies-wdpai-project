<?php

/**
 * @var UserElement $this
 */

?>

<div class="user <?= $this->attached ? 'attached' : ''; ?>">
    <div class="user-info">
        <div class="flex-center gap-10">
            <div class="avatar" <?php
                                if ($this->user->getAvatarUrl()) {
                                    echo 'style="background-image: url(' . $this->user->getAvatarUrl() . ');"';
                                }
                                ?>></div>
            <div class="user-data">
                <div><?= $this->user->getFullName(); ?></div>
                <div><?= $this->user->getEmail() ?></div>
                <div>tel. <?= $this->user->getPhone() ?></div>
            </div>
        </div>
    </div>
</div>