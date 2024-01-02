<?php

class AnnouncementReport
{
    private int $reportId;
    private int $userId;
    private int $announcementId;
    private string $details;
    private DateTime $given;

    public function __construct($report_id, int $user_id, int $announcement_id, ?string $details, ?string $given)
    {
        $this->reportId = $report_id;
        $this->userId = $user_id;
        $this->announcementId = $announcement_id;
        $this->details = $details;
        $this->given = $given ? new DateTime($given, new DateTimeZone('Europe/Warsaw')) : null;
    }

    public function getReportId(): int
    {
        return $this->reportId;
    }

    public  function getUserId(): int
    {
        return $this->userId;
    }

    public function getAnnouncementId(): int
    {
        return $this->announcementId;
    }

    public function getDetails(): string
    {
        return $this->details;
    }
    
    public function getGiven(): ?DateTime
    {
        return $this->given;
    }
}
