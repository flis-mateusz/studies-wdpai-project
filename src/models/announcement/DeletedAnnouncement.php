<?php

class DeletedAnnouncement
{
    const VIOLATION = 1;
    private $delete_id;
    private $deleted_at;
    private $reason;

    public function __construct(int $delete_id, string $deleted_at, bool $reason)
    {
        $this->delete_id = $delete_id;
        $this->deleted_at = new DateTime($deleted_at, new DateTimeZone('Europe/Warsaw'));
        $this->reason = $reason;
    }

    public function getDeleteId(): int
    {
        return $this->delete_id;
    }

    public function getDeletedAt(): DateTime
    {
        return $this->deleted_at;
    }

    public function getReason(): int
    {
        return $this->reason;
    }
}
