<?php

/**
 * @var ?User $user
 * @var ?Announcement[] $announcements
 * @var ?Announcement[] $observedAnnouncements
 */

require_once __DIR__ . '/components/HeaderComponent.php';
require_once __DIR__ . '/components/AnnouncementElement.php';

HeaderComponent::initialize();
AnnouncementElement::initialize();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="/public/css/dashboard.css">
    <script type="module" src="/public/js/announcement.js" defer></script>
    <?php
    ResourceManager::appendResources();
    ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Zwierzak Szuka Domu</title>
</head>

<body>
    <?php
    (new HeaderComponent($user))->render();
    ?>
    <main>
        <section class="welcome">
            <div>
                <span>
                    Zwierzak Szuka Domu
                </span>
                <div>
                    <div class="highlight">
                        Cieszymy się, że tu jesteś
                    </div>
                    <span>
                        Serwis poświęcony ogłoszeniom z adopcją zwierząt. Znajdziesz tutaj serię ogłoszeń od zwierząt,
                        które szukają nowych, kochających domów. Każde zwierzę ma swoją własną historię i potrzebuje
                        czułej opieki, dlatego jesteśmy pewni, że znajdziesz tutaj przyjaciela na całe życie.
                    </span>
                </div>
            </div>
            <div class="image"></div>
        </section>
        <section class="search">
            <label class="search-label">
                <i class="material-icons">search</i>
                <input type="text" class="main-input" placeholder="Szukaj według rasy, gatunku, miasta">
            </label>
            <div class="search-predefined">
                <span class="main-button">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="33" height="31" viewBox="0 0 33 31" fill="none">
                            <path d="M18.6619 10.5C17.4006 10.5 11.9856 10.6238 8.5 15.8719V10.5C8.5 7.19125 5.80875 4.5 2.5 4.5C1.39562 4.5 0.5 5.39563 0.5 6.5C0.5 7.60437 1.39562 8.5 2.5 8.5C3.6025 8.5 4.5 9.3975 4.5 10.5V26.5C4.5 28.7062 6.29375 30.5 8.5 30.5H19.5C20.0525 30.5 20.5 30.0525 20.5 29.5V28.5C20.5 27.3956 19.6044 26.5 18.5 26.5H16.5L24.5 20.5V29.5C24.5 30.0525 24.9475 30.5 25.5 30.5H27.5C28.0525 30.5 28.5 30.0525 28.5 29.5V16.6162C27.8569 16.7831 27.1944 16.9 26.5 16.9C22.6369 16.9 19.405 14.1469 18.6619 10.5ZM28.5 4.5H24.5L20.5 0.5V8.9C20.5 12.2137 23.1863 14.9 26.5 14.9C29.8137 14.9 32.5 12.2137 32.5 8.9V0.5L28.5 4.5ZM24 9.5C23.4475 9.5 23 9.0525 23 8.5C23 7.9475 23.4475 7.5 24 7.5C24.5525 7.5 25 7.9475 25 8.5C25 9.0525 24.5525 9.5 24 9.5ZM29 9.5C28.4475 9.5 28 9.0525 28 8.5C28 7.9475 28.4475 7.5 29 7.5C29.5525 7.5 30 7.9475 30 8.5C30 9.0525 29.5525 9.5 29 9.5Z" fill="#DB8484" />
                        </svg>
                    </div>
                    <span>Koty</span>
                </span>
                <span class="main-button">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="31" viewBox="0 0 32 31" fill="none">
                            <path d="M16.6287 12.5009L26 15.8478V29.5009C26 29.7661 25.8946 30.0205 25.7071 30.208C25.5196 30.3956 25.2652 30.5009 25 30.5009H21C20.7348 30.5009 20.4804 30.3956 20.2929 30.208C20.1054 30.0205 20 29.7661 20 29.5009V22.5009H10V29.5009C10 29.7661 9.89464 30.0205 9.70711 30.208C9.51957 30.3956 9.26522 30.5009 9 30.5009H5C4.73478 30.5009 4.48043 30.3956 4.29289 30.208C4.10536 30.0205 4 29.7661 4 29.5009V16.1316C1.6775 15.3034 0 13.1047 0 10.5009C0 9.9705 0.210714 9.46179 0.585786 9.08672C0.960859 8.71165 1.46957 8.50093 2 8.50093C2.53043 8.50093 3.03914 8.71165 3.41421 9.08672C3.78929 9.46179 4 9.9705 4 10.5009C4.00099 11.0311 4.21202 11.5392 4.58688 11.914C4.96174 12.2889 5.46987 12.4999 6 12.5009H16.6287ZM32 5.50093V7.50093C32 8.5618 31.5786 9.57921 30.8284 10.3294C30.0783 11.0795 29.0609 11.5009 28 11.5009H26V13.7247L18 10.8678V1.50093C18 0.610307 19.0763 0.164057 19.7069 0.794057L21.4119 2.50093H24.7638C25.4456 2.50093 26.2481 2.99593 26.5525 3.60656L27 4.50093H31C31.2652 4.50093 31.5196 4.60629 31.7071 4.79382C31.8946 4.98136 32 5.23571 32 5.50093ZM25 5.50093C25 5.30315 24.9414 5.10981 24.8315 4.94536C24.7216 4.78091 24.5654 4.65274 24.3827 4.57705C24.2 4.50136 23.9989 4.48156 23.8049 4.52015C23.6109 4.55873 23.4327 4.65397 23.2929 4.79382C23.153 4.93368 23.0578 5.11186 23.0192 5.30584C22.9806 5.49982 23.0004 5.70089 23.0761 5.88361C23.1518 6.06634 23.28 6.22252 23.4444 6.3324C23.6089 6.44228 23.8022 6.50093 24 6.50093C24.2652 6.50093 24.5196 6.39557 24.7071 6.20804C24.8946 6.0205 25 5.76615 25 5.50093Z" fill="#963131" />
                        </svg>
                    </div>
                    <span>Psy</span>
                </span>
                <span class="main-button">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="29" viewBox="0 0 32 29" fill="none">
                            <path d="M16.0003 12.5002C11.0372 12.5002 4.00044 20.1726 4.00044 25.0157C4.00044 27.1969 5.67605 28.5 8.48414 28.5C11.5366 28.5 13.5522 26.9325 16.0003 26.9325C18.4697 26.9325 20.4909 28.5 23.5165 28.5C26.3246 28.5 28.0002 27.1969 28.0002 25.0157C28.0002 20.1726 20.9634 12.5002 16.0003 12.5002ZM6.79541 11.7121C6.14542 9.54647 4.14294 8.14399 2.32296 8.57898C0.502974 9.01398 -0.445141 11.1221 0.204852 13.2877C0.854846 15.4533 2.85732 16.8558 4.67731 16.4208C6.49729 15.9858 7.4454 13.8777 6.79541 11.7121ZM12.0904 10.4133C14.0241 9.90459 14.9916 7.29212 14.2516 4.5784C13.5116 1.86468 11.3441 0.077823 9.41038 0.586568C7.47665 1.09531 6.50916 3.70778 7.24915 6.42151C7.98915 9.13523 10.1572 10.9227 12.0904 10.4133ZM29.677 8.57961C27.8571 8.14461 25.8552 9.5471 25.2046 11.7127C24.5546 13.8783 25.5027 15.9864 27.3227 16.4214C29.1427 16.8564 31.1445 15.4539 31.7951 13.2883C32.4451 11.1227 31.497 9.0146 29.677 8.57961ZM19.9103 10.4133C21.844 10.9221 24.0115 9.13523 24.7515 6.42151C25.4915 3.70778 24.524 1.09594 22.5902 0.586568C20.6565 0.077198 18.489 1.86468 17.749 4.5784C17.0091 7.29212 17.9765 9.90459 19.9103 10.4133Z" fill="#5033A4" />
                        </svg>
                    </div>
                    <span>Wszystkie</span>
                </span>
            </div>
        </section>
        <section class="announcements">
            <section>
                <?php if (isset($announcements) && !isEmpty($announcements)) : ?>
                    <span>Ostatnie dodane ogłoszenia</span>
                    <section class="announcements-list">
                        <?php
                        foreach ($announcements as $announcement) {
                            (new AnnouncementElement($announcement))->render();
                        }
                        ?>
                    </section>
                <?php endif; ?>
            </section>
            <section>
                <?php if (isset($observedAnnouncements) && !isEmpty($observedAnnouncements)) : ?>
                    <span>Obserwowane ogłoszenia</span>
                    <section class="announcements-list">
                        <?php
                        foreach ($observedAnnouncements as $announcement) {
                            (new AnnouncementElement($announcement))->render();
                        }
                        ?>
                    </section>
                <?php endif; ?>
            </section>
        </section>
    </main>
    <footer>

    </footer>
</body>

</html>