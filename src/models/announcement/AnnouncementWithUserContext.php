<?php

require_once 'Announcement.php';

class AnnouncementWithUserContext extends Announcement {
    private $reported;
    private $favourite;

    public function isReporedByUser(): ?bool
    {
        return $this->reported;
    }

    public function setReportedByUser(bool $reported): void
    {
        $this->reported = $reported;
    }

    public function setUsersFavourite(bool $favourite): void
    {
        $this->favourite = $favourite;
    }

    public function isUserFavourite(): ?bool
    {
        return $this->favourite;
    }

    public static function createFromAnnouncement(Announcement $announcement) {
        $extended = new self(
            $announcement->getId(),
            $announcement->getType(),
            $announcement->getUser(),
            $announcement->getDetails(),
            $announcement->isAccepted(),
            $announcement->getCreatedAt()
        );
        return $extended;
    }
}