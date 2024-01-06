<?php

/**
 * @var ?Announcement[] $announcements
 */

require_once __DIR__ . '/../components/AnnouncementElement.php';

if (isset($announcements) && !empty($announcements)) {
    foreach ($announcements as $announcement) {
        (new AnnouncementElement($announcement))->render();
    }
}
