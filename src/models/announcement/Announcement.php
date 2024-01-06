<?php

require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/../animal/AnimalType.php';
require_once __DIR__ . '/../../utils/utils.php';
require_once 'AnnouncementDetail.php';

class Announcement
{
    private ?int $announcement_id;
    private AnimalType $type;
    private User $user;
    private AnnouncementDetail $details;
    private bool $accepted;
    private DateTime $created_at;
    private DeletedAnnouncement $deleted;

    public function __construct(
        ?int $announcement_id,
        AnimalType $type,
        User $user,
        AnnouncementDetail $details,
        bool $accepted,
        string|DateTime|null $created_at,
    ) {
        $this->announcement_id = $announcement_id;
        $this->type = $type;
        $this->user = $user;
        $this->details = $details;
        $this->accepted = $accepted;
        $this->created_at = $created_at instanceof DateTime ? $created_at : new DateTime($created_at ? $created_at : 'now', new DateTimeZone('Europe/Warsaw'));
    }

    public function getId(): ?int
    {
        return $this->announcement_id;
    }

    public function setId(int $id): void
    {
        $this->announcement_id = $id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getDetails(): AnnouncementDetail
    {
        return $this->details;
    }

    public function setDetails(AnnouncementDetail $details): void
    {
        $this->details = $details;
    }

    public function getType(): AnimalType
    {
        return $this->type;
    }

    public function setType(AnimalType $type): void
    {
        $this->type = $type;
    }

    public function isAccepted(): bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): void
    {
        $this->accepted = $accepted;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function isDeleted(): bool
    {
        return isset($this->deleted) && $this->deleted != null;
    }

    public function getDeleted(): ?DeletedAnnouncement
    {
        return isset($this->deleted) ?  $this->deleted  : null;
    }

    public function setDeleted(DeletedAnnouncement $deleted): void
    {
        $this->deleted = $deleted;
    }
}
