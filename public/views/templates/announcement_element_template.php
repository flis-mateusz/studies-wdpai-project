<?php

/**
 * @var AnnouncementElement $this
 * @var UserElement
 */

$showAwaiting = !$this->announcement->isAccepted() && !$this->withUserInfo;
?>

<a class="announcement <?= $showAwaiting ? 'awaiting' : null; ?>" href="/announcement/<?= $this->announcement->getId(); ?>">
    <div class="announcement-image" style='background-image: url(<?= $this->announcement->getDetails()->getAvatarUrl(); ?>);'></div>
    <?= $showAwaiting ? '<div class="awaiting"><span>Oczekuje na weryfikację</span></div>' : null; ?>
    <?= $this->announcement->getDetails()->getReportsCount() ? '<div class="awaiting"><span>Liczba zgłoszeń: ' . $this->announcement->getDetails()->getReportsCount() . '</span></div>' : null; ?>
    <div class="announcement-data">
        <?php if ($this->withUserInfo) : ?>
            <?= (new UserElement($this->announcement->getUser(), true))->render(); ?>
        <?php endif; ?>
        <div class="announcement-detail">
            <div class="flex-center gap-10">
                <div class="announcement-avatar" style='background-image: url(<?= $this->announcement->getDetails()->getAvatarUrl(); ?>);'></div>
                <div class="announcement-name">
                    <div><?= $this->announcement->getDetails()->getName(); ?></div>
                    <div><?= $this->announcement->getType()->getName() ? $this->announcement->getType()->getName() : '<span class="italic">Typ usunięty</span>'; ?></div>
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