<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/animal/AnimalFeature.php';
require_once __DIR__ . '/../models/animal/AnimalType.php';
require_once __DIR__ . '/../repository/AnnouncementsRepository.php';
require_once __DIR__ . '/../models/announcement/Announcement.php';
require_once __DIR__ . '/../models/announcement/AnnouncementReport.php';

class AdminRepository extends Repository
{
    public function getAnnouncementsToApprove($limit = 0)
    {
        $connection = $this->database->connect();
        try {
            $sql = '
            SELECT announcements.*, announcement_detail.*, animal_types.*, user_detail.avatar_name, user_detail.name, user_detail.surname, users.phone, users.email
            FROM announcements
            NATURAL JOIN announcement_detail
            NATURAL JOIN animal_types
            JOIN users on announcements.user_id = users.user_id
            JOIN user_detail on users.user_id = user_detail.user_id
            LEFT JOIN deleted_announcements on announcements.announcement_id = deleted_announcements.announcement_id
            WHERE announcements.accepted = false AND deleted_announcements.delete_id IS NULL
            ORDER BY announcements.created_at DESC';

            $params = [];
            if ($limit > 0) {
                $sql .= ' LIMIT ?';
                $params[] = $limit;
            }

            $stmt = $connection->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $announcements = [];
            foreach ($results as $result) {
                $announcements[] = AnnouncementsRepository::createAnnouncementFromResult($result);
            }
            return $announcements;
        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }
    }

    public function getReportedAnnouncements($limit = 0)
    {
        $connection = $this->database->connect();
        try {
            $sql = '
            SELECT announcements.*, ad.*, animal_types.*, user_detail.avatar_name, user_detail.name, user_detail.surname, users.phone, users.email, COUNT(announcement_report.report_id) AS report_count
            FROM announcements
            LEFT JOIN announcement_detail ad on announcements.announcement_id = ad.announcement_id
            LEFT JOIN animal_types on announcements.type_id = animal_types.type_id
            LEFT JOIN users ON announcements.user_id = users.user_id
            LEFT JOIN user_detail ON users.user_id = user_detail.user_id
            LEFT JOIN deleted_announcements ON announcements.announcement_id = deleted_announcements.announcement_id
            JOIN announcement_report ON announcements.announcement_id = announcement_report.announcement_id
            WHERE announcements.accepted = true AND deleted_announcements.delete_id IS NULL AND announcement_report.accepted IS NULL
            GROUP BY announcements.announcement_id, ad.announcement_detail_id, animal_types.type_id, user_detail.detail_id, users.user_id
            ORDER BY report_count';

            $params = [];
            if ($limit > 0) {
                $sql .= ' LIMIT ?';
                $params[] = $limit;
            }

            $stmt = $connection->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $announcements = [];
            foreach ($results as $result) {
                $announcement = AnnouncementsRepository::createAnnouncementFromResult($result);
                $announcement->getDetails()->setReportsCount($result['report_count']);
                $announcements[] = $announcement;
            }
            return $announcements;

        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }
    }

    public function approveAnnouncement(int $id)
    {
        $connection = $this->database->connect();
        try {
            $stmt = $connection->prepare('
                UPDATE announcements
                SET accepted = true
                WHERE announcement_id =?');
            $stmt->execute([$id]);
            return true;
        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }
    }
}
