<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/animal/AnimalFeature.php';
require_once __DIR__ . '/../models/animal/AnimalType.php';
require_once __DIR__ . '/../models/announcement/Announcement.php';
require_once __DIR__ . '/../models/announcement/AnnouncementWithUserContext.php';
require_once __DIR__ . '/../models/announcement/DeletedAnnouncement.php';
require_once __DIR__ . '/../models/announcement/AnnouncementReport.php';

class AnnouncementsRepository extends Repository
{
    public function addAnnouncement(Announcement $announcement)
    {
        $connection = $this->database->connect();
        try {
            $connection->beginTransaction();

            $stmt = $connection->prepare('
            INSERT INTO announcements (type_id, user_id, accepted)
            VALUES (?, ?, ?) RETURNING announcement_id;');
            $stmt->execute([
                $announcement->getType()->getId(),
                $announcement->getUser()->getId(),
                json_encode($announcement->isAccepted())
            ]);

            $announcement_id = $stmt->fetchColumn();
            if (!$announcement_id) {
                throw new Exception();
            }
            $announcement->setId($announcement_id);

            $stmt = $connection->prepare('
            INSERT INTO announcement_detail (announcement_id, animal_name, animal_locality, animal_price, animal_description, animal_age, animal_age_type, animal_gender, animal_avatar_name, animal_kind)
            VALUES (?,?,?,?,?,?,?,?,?,?) RETURNING announcement_detail_id;');

            $stmt->execute([
                $announcement_id,
                $announcement->getDetails()->getName(),
                $announcement->getDetails()->getLocality(),
                $announcement->getDetails()->getPrice(),
                $announcement->getDetails()->getDescription(),
                $announcement->getDetails()->getAge(),
                $announcement->getDetails()->getAgeType(),
                $announcement->getDetails()->getGender(),
                $announcement->getDetails()->getAvatarName(),
                $announcement->getDetails()->getKind()
            ]);

            $announcement_detail_id = $stmt->fetchColumn();
            if (!$announcement_detail_id) {
                throw new Exception();
            }
            $announcement->getDetails()->setId($announcement_detail_id);

            foreach ($announcement->getDetails()->getFeatures() as $feature) {
                $stmt = $connection->prepare('
                INSERT INTO announcement_animal_features (feature_id, value, announcement_detail_id)
                VALUES (?,?,?)');

                $stmt->execute([
                    $feature->getId(),
                    json_encode($feature->getValue()),
                    $announcement_detail_id,
                ]);
            }

            $connection->commit();
            return true;
        } catch (Exception $e) {
            $connection->rollback();
            error_log($e);
            throw $e;
        }
    }

    public function updateAnnouncement(Announcement $announcement)
    {
        $connection = $this->database->connect();
        try {
            $connection->beginTransaction();

            $stmt = $connection->prepare('
            UPDATE announcements
            SET type_id = ?, accepted = false
            WHERE announcement_id = ?;');
            $stmt->execute([
                $announcement->getType()->getId(),
                $announcement->getId()
            ]);

            $stmt = $connection->prepare('
            UPDATE announcement_detail
            SET animal_name = ?, animal_locality = ?, animal_price = ?, animal_description = ?, animal_age = ?, animal_age_type = ?, animal_gender = ?, animal_avatar_name = ?, animal_kind = ?
            WHERE announcement_id = ?;');
            $stmt->execute([
                $announcement->getDetails()->getName(),
                $announcement->getDetails()->getLocality(),
                $announcement->getDetails()->getPrice(),
                $announcement->getDetails()->getDescription(),
                $announcement->getDetails()->getAge(),
                $announcement->getDetails()->getAgeType(),
                $announcement->getDetails()->getGender(),
                $announcement->getDetails()->getAvatarName(),
                $announcement->getDetails()->getKind(),
                $announcement->getId()
            ]);

            $stmt = $connection->prepare('
            DELETE FROM announcement_animal_features WHERE announcement_detail_id = ?');
            $stmt->execute([$announcement->getDetails()->getId()]);

            foreach ($announcement->getDetails()->getFeatures() as $feature) {
                $stmt = $connection->prepare('
                INSERT INTO announcement_animal_features (feature_id, value, announcement_detail_id)
                VALUES (?,?,?)');
                $stmt->execute([
                    $feature->getId(),
                    json_encode($feature->getValue()),
                    $announcement->getDetails()->getId(),
                ]);
            }

            $connection->commit();
            return true;
        } catch (Exception $e) {
            $connection->rollback();
            error_log($e);
            throw $e;
        }
    }

    public function getAnnouncementWithUserContext($id, $userId)
    {
        $connection = $this->database->connect();

        $sql = '
        SELECT announcements.*, announcement_detail.*, animal_types.*, user_detail.avatar_name, user_detail.name, user_detail.surname, users.phone,
        deleted_announcements.delete_id, deleted_announcements.deleted_at, deleted_announcements.violated';
        if ($userId !== null) {
            $sql .= ', announcement_report.report_id, announcement_likes.like_id ';
        }
        $sql .= '
        FROM announcements
        NATURAL JOIN announcement_detail
        JOIN users on announcements.user_id = users.user_id
        JOIN user_detail on users.user_id = user_detail.user_id
        LEFT JOIN animal_types on announcements.type_id = animal_types.type_id
        LEFT JOIN deleted_announcements on announcements.announcement_id = deleted_announcements.announcement_id';
        if ($userId !== null) {
            $sql .= ' LEFT JOIN announcement_likes on announcements.announcement_id = announcement_likes.announcement_id AND announcement_likes.user_id = :userId';
            $sql .= ' LEFT JOIN announcement_report on announcements.announcement_id = announcement_report.announcement_id AND announcement_report.user_id = :userId';
        }
        $sql .= ' WHERE announcements.announcement_id = :id';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if ($userId !== null) {
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            return null;
        }

        $announcement = AnnouncementWithUserContext::createFromAnnouncement(self::createAnnouncementFromResult($result));

        if ($result['deleted_at']) {
            $announcement->setDeleted(new DeletedAnnouncement($result['delete_id'], $result['deleted_at'], $result['violated']));
        }

        if ($userId && $result['report_id']) {
            $announcement->setReportedByUser(true);
        }

        if ($userId && $result['like_id']) {
            $announcement->setUsersFavourite(true);
        }

        return $announcement;
    }

    public function getAnnouncements($limit = 5, User $user = null)
    {
        $connection = $this->database->connect();
        try {
            $sql = '
            SELECT announcements.*, announcement_detail.*, animal_types.*, user_detail.avatar_name, user_detail.name, user_detail.surname, users.phone
            FROM announcements
            NATURAL JOIN announcement_detail
            JOIN users on announcements.user_id = users.user_id
            JOIN user_detail on users.user_id = user_detail.user_id
            LEFT JOIN animal_types on announcements.type_id = animal_types.type_id
            LEFT JOIN deleted_announcements on announcements.announcement_id = deleted_announcements.announcement_id
            WHERE deleted_announcements.delete_id IS NULL';

            $params = [];

            if ($user === null) {
                $sql .= ' AND announcements.accepted = true';
            } else {
                $sql .= ' AND users.user_id = ?';
                $params[] = $user->getId();
            }

            // Sortowanie - jeśli jest podany użytkownik, najpierw pokazuje accepted = true
            if ($user !== null) {
                $sql .= ' ORDER BY announcements.accepted DESC, announcements.created_at DESC';
            } else {
                $sql .= ' ORDER BY announcements.created_at DESC';
            }

            if ($limit > 0) {
                $sql .= ' LIMIT ?';
                $params[] = $limit;
            }

            $stmt = $connection->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $announcements = [];
            foreach ($results as $result) {
                $announcements[] = $this->createAnnouncementFromResult($result);
            }
            return $announcements;
        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }
    }

    public function getAnnouncementsToFilter()
    {
        try {
            $connection = $this->database->connect();

            $sql = "
            SELECT announcements.*, announcement_detail.*, animal_types.*, user_detail.*, users.*,
            string_agg(announcement_animal_features.feature_id::TEXT || '-' || announcement_animal_features.value::TEXT, ',') AS aggregated_features,
            string_agg(announcement_likes.user_id::TEXT, ',') as aggregated_likes
            FROM announcements
            NATURAL JOIN announcement_detail
            JOIN users on announcements.user_id = users.user_id
            JOIN user_detail on users.user_id = user_detail.user_id
            LEFT JOIN animal_types on announcements.type_id = animal_types.type_id
            LEFT JOIN announcement_animal_features on announcement_detail.announcement_detail_id = announcement_animal_features.announcement_detail_id
            LEFT JOIN animal_features on announcement_animal_features.feature_id = animal_features.feature_id
            LEFT JOIN deleted_announcements on announcements.announcement_id = deleted_announcements.announcement_id
            LEFT JOIN announcement_likes on announcements.announcement_id = announcement_likes.announcement_id
            WHERE deleted_announcements.delete_id IS NULL AND announcements.accepted = true
            GROUP BY announcements.announcement_id, announcement_detail.announcement_detail_id, animal_types.type_id, user_detail.detail_id, users.user_id, announcements.created_at
            ORDER BY announcements.created_at DESC";

            $stmt = $connection->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $announcements = [];
            foreach ($results as $result) {
                if (!empty($result['aggregated_features'])) {
                    $animalFeatures = explode(',', $result['aggregated_features']);
                    $features = [];
                    foreach ($animalFeatures as $feature) {
                        $featureParts = explode('-', $feature);
                        $features[] = new AnimalFeature($featureParts[0], null, filter_var($featureParts[1], FILTER_VALIDATE_BOOLEAN));
                    }
                    $result['animal_features'] = $features;
                }
                if (!empty($result['aggregated_likes'])) {
                    $likes = explode(',', $result['aggregated_likes']);
                }

                $announcement = $this->createAnnouncementFromResult($result);
                if (isset($likes)) {
                    $announcement->getDetails()->setLikesIds($likes);
                }

                $announcements[] = $announcement;
            }
            return $announcements;
        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }
    }

    public function getUsersFavoriteCount(User $user)
    {
        $connection = $this->database->connect();
        try {
            $stmt = $connection->prepare('
            SELECT count(announcements.announcement_id)
            FROM announcements
            LEFT JOIN announcement_likes on announcements.announcement_id = announcement_likes.announcement_id
            LEFT JOIN deleted_announcements on announcements.announcement_id = deleted_announcements.announcement_id
            WHERE announcements.accepted=true AND deleted_announcements.delete_id IS NULL AND announcement_likes.user_id = ?');
            $stmt->execute([$user->getId()]);
            $count = $stmt->fetchColumn();
            return $count;
        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }
    }

    public function getUsersFavorite($user)
    {
        $connection = $this->database->connect();
        try {
            $stmt = $connection->prepare('
            SELECT announcements.*, announcement_detail.*, animal_types.*, user_detail.avatar_name, user_detail.name, user_detail.surname, users.phone, announcement_likes.like_id
            FROM announcements
            NATURAL JOIN announcement_detail
            NATURAL JOIN animal_types
            JOIN users on announcements.user_id = users.user_id
            JOIN user_detail on users.user_id = user_detail.user_id
            LEFT JOIN announcement_likes on announcements.announcement_id = announcement_likes.announcement_id
            WHERE announcement_likes.user_id = ?
            ORDER BY announcement_likes.given_at
            ');
            $stmt->execute([$user->getId()]);

            $results = [];
            $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($announcements as $announcement) {
                $results[] = $this->createAnnouncementFromResult($announcement);
            }
        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }
    }

    public function delete(int $id, ?int $admin_id = null)
    {
        $connection = $this->database->connect();
        try {
            $connection->beginTransaction();

            $stmt = $connection->prepare('
                INSERT INTO deleted_announcements (announcement_id, admin_id, violated)
                VALUES (?,?,?) RETURNING delete_id');

            $stmt->execute([$id, $admin_id, json_encode($admin_id != null)]);
            $delete_id = $stmt->fetchColumn();

            $stmt = $connection->prepare('
            UPDATE announcement_report SET accepted=true WHERE announcement_id =? AND accepted IS NULL');
            $stmt->execute([$id]);

            $connection->commit();
            return $delete_id;
        } catch (Exception $e) {
            $connection->rollback();
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

    public function like(int $userId, int $announcementId)
    {
        $connection = $this->database->connect();
        try {
            $stmt = $connection->prepare('
                INSERT INTO announcement_likes (user_id, announcement_id) VALUES (?,?) RETURNING like_id
            ');
            $stmt->execute([$userId, $announcementId]);
            return true;
        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }
    }

    public function unlike(int $userId, int $announcementId)
    {
        $connection = $this->database->connect();
        try {
            $stmt = $connection->prepare('
                DELETE FROM announcement_likes WHERE user_id =? AND announcement_id =?
            ');
            $stmt->execute([$userId, $announcementId]);
            return true;
        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }
    }

    private static function createUserFromResult($result)
    {
        return new User(
            $result['user_id'],
            isset($result['email']) ? $result['email'] : null,
            null,
            null,
            $result['name'],
            $result['surname'],
            $result['avatar_name'],
            isset($result['phone']) ? $result['phone'] : null,
            null
        );
    }

    public static function createAnnouncementFromResult($result): Announcement
    {
        $announcementDetails = new AnnouncementDetail(
            $result['announcement_detail_id'],
            $result['animal_name'],
            $result['animal_locality'],
            $result['animal_price'],
            $result['animal_description'],
            $result['animal_age'],
            $result['animal_age_type'],
            $result['animal_gender'],
            $result['animal_avatar_name'],
            $result['animal_kind'],
            isset($result['animal_features']) ? $result['animal_features'] : []
        );

        return new Announcement(
            $result['announcement_id'],
            new AnimalType($result['type_id'], $result['type_name']),
            self::createUserFromResult($result),
            $announcementDetails,
            $result['accepted'],
            $result['created_at']
        );
    }
}
