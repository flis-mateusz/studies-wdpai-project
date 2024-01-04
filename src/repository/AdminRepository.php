<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/pet/PetFeature.php';
require_once __DIR__ . '/../models/pet/PetType.php';
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

            $params=[];
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

    public function AnnouncementApprove(int $id)
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
