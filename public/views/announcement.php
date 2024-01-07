<?php

/**
 * @var ?User $viewer
 * @var AnnouncementWithUserContext $announcement
 */

require_once __DIR__ . '/components/HeaderComponent.php';
require_once __DIR__ . '/components/CustomContentLoader.php';

HeaderComponent::initialize();
CustomContentLoader::initialize();

$owner = $announcement->getUser();
$isOwner = $viewer && $viewer->getId() === $owner->getId();
$isViewerAdmin = $viewer && $viewer->isAdmin();
$deleted = $announcement->getDeleted();
$requiresApproval = !$announcement->isAccepted();
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/common.css">
    <link rel="stylesheet" href="/public/css/announcement/announcement.css">
    <?php
    ResourceManager::appendResources();
    ?>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Ogłoszenie</title>
</head>

<body>
    <?php
    (new HeaderComponent($viewer))->render();
    ?>
    <main>
        <?php if ($deleted) : ?>
            <!-- DELETED ANNOUNCEMENT -->
            <?php if ($deleted->getReason() == DeletedAnnouncement::VIOLATION) : ?>
                <section class="tip normal center">
                    <span>Wybrane ogłoszenie zostało usunięte przez administratora </span>
                    <span class="bold"><?= $deleted->getDeletedAt()->format('Y-m-d'); ?></span>
                    <span>ponieważ naruszało nasz regulamin</span>
                </section>
            <?php else : ?>
                <section class="tip normal center"><span>Wybrane ogłoszenie zostało zakończone</span></section>
            <?php endif; ?>
        <?php elseif ($requiresApproval && !$isOwner && !$isViewerAdmin) : ?>
            <!-- INVISIBLE TO OTHERS UNLESS ACCEPTED -->
            <section class="tip normal center"><span>Wybrane ogłoszenie oczekuje na zatwierdzenie, spróbuj ponownie później</span></section>
        <?php else : ?>
            <?php if ($requiresApproval && !$isViewerAdmin) : ?>
                <section class="tip normal center"><span>Twoje ogłoszenie oczekuje na zatwierdzenie, nie jest jeszcze widoczne publicznie</span></section>
            <?php endif; ?>
            <?php if ($isViewerAdmin && $announcement->getDetails()->getReportsCount()) : ?>
                <section class="tip normal center tip-reports">
                    <span>Liczba zgłoszeń ogłoszenia: <?= $announcement->getDetails()->getReportsCount(); ?></span>
                    <span>Kliknij <i class="material-icons">gavel</i> jeśli ogłoszenie nie narusza regulaminu</span>
                </section>
            <?php endif; ?>

            <?php (new CustomContentLoader())->render(); ?>

            <section class="hidden api-output"><span class="input-error not-hidden"></span></section>
            <section class="announcement-header">
                <div>
                    <span><?= $announcement->getDetails()->getName(); ?></span>
                    <span>dodano <?= $announcement->getCreatedAt()->format('Y-m-d H:i:s'); ?></span>
                </div>
                <?php if (!$viewer || !$isViewerAdmin && !$isOwner) : ?>
                    <?php if (!$announcement->isReporedByUser()) : ?>
                        <div class="announcement-report action-report">
                            <i class="material-icons">flag</i>
                            <span>Zgłoś ogłoszenie</span>
                        </div>
                        <div class="announcement-report">
                            <i class="material-icons">assignment_turned_in</i>
                            <span>Zgłoszono ogłoszenie</span>
                        </div>
                    <?php else : ?>
                        <div class="announcement-report">
                            <i class="material-icons">assignment_turned_in</i>
                            <span>Zgłoszono ogłoszenie</span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (($isViewerAdmin || $isOwner)) : ?>
                    <div>
                        <?php if ($isOwner) : ?>
                            <!-- OWNER CAN EDIT -->
                            <i class="material-icons icon-button action-edit">edit</i>
                        <?php endif; ?>
                        <?php if ($requiresApproval && $isViewerAdmin) : ?>
                            <!-- ADMIN CAN APPROVE -->
                            <i class="material-icons icon-button admin action-approve">assignment_turned_in</i>
                        <?php endif; ?>
                        <?php if ($isViewerAdmin && $announcement->getDetails()->getReportsCount()) : ?>
                            <!-- ADMIN CAN APPROVE -->
                            <i class="material-icons icon-button admin action-reject-reports">gavel</i>
                        <?php endif; ?>
                        <?php if ($isViewerAdmin && !$isOwner) : ?>
                            <!-- ADMIN CAN DELETE BUT IF HES NOT THE OWNER -->
                            <i class="material-icons icon-button admin action-delete">delete_forever</i>
                        <?php else : ?>
                            <!-- OWNER CAN DELETE -->
                            <i class="material-icons icon-button action-delete">delete_forever</i>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>

            <section class="announcement <?= $requiresApproval && !$viewer->isAdmin() ? ' blur' : null ?>">
                <div class="about-pet">
                    <?php if (!$isOwner) : ?>
                        <div class="like"><i class="material-icons icon-button action-like <?= $announcement->isUserFavourite() ? 'liked' : null ?><?= $requiresApproval ? ' hidden' : null ?>"></i></div>
                    <?php endif; ?>

                    <div class="photo" style="background-image: url('<?= $announcement->getDetails()->getAvatarUrl(); ?>');"></div>
                    <div class="info">
                        <div>
                            <div>
                                <span>Typ</span>
                                <span class="capitalize"><?= $announcement->getType()->getName() ? $announcement->getType()->getName() : '<span class="italic">Typ usunięty</span>'; ?></span>
                            </div>
                            <div>
                                <span>Gatunek</span>
                                <span class="capitalize"><?= $announcement->getDetails()->getFormattedKind(); ?></span>
                            </div>
                            <div>
                                <span>Płeć</span>
                                <span><?= $announcement->getDetails()->getFormattedGender(); ?></span>
                            </div>
                            <div>
                                <span>Wiek</span>
                                <span><?= $announcement->getDetails()->getFormattedAge(); ?></span>
                            </div>
                        </div>
                        <hr>
                        <div class="badges">
                            <?php
                            if (!isEmpty($announcement->getDetails()->getFeatures())) {
                                foreach ($announcement->getDetails()->getFeatures() as $feature) {
                                    if ($feature->getValue()) {
                                        echo '<div><i class="material-icons">check_circle</i><span>' . $feature->getName() . '</span></div>';
                                    } else {
                                        echo '<div class="not-ok"><i class="material-icons">remove_circle</i><span>' . $feature->getName() . '</span></div>';
                                    }
                                }
                            } else {
                                echo 'Brak cech szczególnych';
                            }
                            ?>
                        </div>
                        <hr class="resp-only">
                    </div>
                    <div class="description">
                        <?= $announcement->getDetails()->getDescription(); ?>
                    </div>
                </div>
                <div class="about-user">
                    <div class="user-basic">
                        <div class="avatar resp" <?php
                                                    if ($announcement->getUser()->getAvatarUrl()) {
                                                        echo 'style="background-image: url(' . $announcement->getUser()->getAvatarUrl() . ');"';
                                                    }
                                                    ?>>
                        </div>
                        <div>
                            <div class="name"><?= $announcement->getUser()->getFullName(); ?></div>
                            <div>
                                <i class="material-icons">location_on</i>
                                <span><?= $announcement->getDetails()->getLocality(); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="inline">
                        <span>Cena</span>
                        <span><?= $announcement->getDetails()->getFormattedPrice(); ?></span>
                    </div>
                    <div class="inline">
                        <span>Numer telefonu</span>
                        <span><?= $announcement->getUser()->getPhone(); ?></span>
                    </div>
                </div>
            </section>
    </main>
    <footer>

    </footer>
    <script type="module">
        import Announcement from '/public/js/announcement.js';
        new Announcement(<?= $announcement->getId(); ?>);
    </script>
<?php endif; ?>
</body>

</html>