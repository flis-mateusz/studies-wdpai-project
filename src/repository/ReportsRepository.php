<?php

require_once 'Repository.php';

class ReportsRepository extends Repository
{
    public function getAnnouncementReportsCount(int $announcementId)
    {
        $connection = $this->database->connect();
        try {
            $stmt = $connection->prepare('
            SELECT COUNT(*) AS report_count
            FROM announcement_report
            WHERE announcement_id = ? AND accepted IS NULL;');
            $stmt->execute([$announcementId]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }
    }

    public function report(int $userId, int $announcementId)
    {
        $connection = $this->database->connect();
        try {
            $stmt = $connection->prepare('
                INSERT INTO announcement_report (user_id, announcement_id) VALUES (?,?) RETURNING report_id
            ');
            $stmt->execute([$userId, $announcementId]);
            return true;
        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }
    }

    public function rejectReports(int $announcementId) {
        $connection = $this->database->connect();
        try {
            $stmt = $connection->prepare('
                UPDATE announcement_report
                SET accepted = false
                WHERE announcement_id = ? AND accepted IS NULL;
            ');
            $stmt->execute([$announcementId]);
            return true;
        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }
    }
}
