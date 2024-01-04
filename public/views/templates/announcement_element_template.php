<?php

/**
 * @var AnnouncementElement $this
 */

$showAwaiting = !$this->announcement->isAccepted() && !$this->withUserInfo;
?>

<a class="announcement <?= $showAwaiting ? 'awaiting' : null; ?>" href="/announcement/<?= $this->announcement->getId(); ?>">
    <div class="announcement-image" style='background-image: url(<?= $this->announcement->getDetails()->getAvatarUrl(); ?>);'></div>
    <?= $showAwaiting ? '<div class="awaiting"><span>Oczekuje weryfikacji</span></div>' : null; ?>
    <div class="announcement-data">
        <?php if ($this->withUserInfo) : ?>
            <div class="announcement-user">
                <div class="flex-center gap-10">
                    <div class="avatar" <?php
                                        if ($this->announcement->getUser()->getAvatarUrl()) {
                                            echo 'style="background-image: url(' . $this->announcement->getUser()->getAvatarUrl() . ');"';
                                        }
                                        ?>></div>
                    <div class="announcement-name">
                        <div><?= $this->announcement->getUser()->getFullName(); ?></div>
                        <div><?= $this->announcement->getUser()->getEmail(); ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="announcement-detail">
            <div class="flex-center gap-10">
                <div class="announcement-avatar" style='background-image: url(<?= $this->announcement->getDetails()->getAvatarUrl(); ?>);'></div>
                <div class="announcement-name">
                    <div><?= $this->announcement->getDetails()->getName(); ?></div>
                    <div><?= $this->announcement->getType()->getName(); ?></div>
                </div>
            </div>
            <div class="flex-center gap-5">
                <i class="material-icons">location_on</i>
                <span><?= $this->announcement->getDetails()->getLocality(); ?></span>
            </div>
        </div>
        <hr>
        <div class="announcement-price">
            <div class="flex-center gap-5">
                <i class="material-icons">date_range</i>
                <span><?= $this->announcement->getDetails()->getFormattedAge(); ?></span>
            </div>
            <div class="flex-center gap-5">
                <i class="material-icons">account_balance_wallet</i>
                <span><?= $this->announcement->getDetails()->getFormattedPrice(); ?></span>
            </div>
        </div>
    </div>
</a>