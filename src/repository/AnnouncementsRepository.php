<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/pet/PetFeature.php';
require_once __DIR__ . '/../models/pet/PetType.php';
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
            INSERT INTO announcement_detail (announcement_id, pet_name, pet_locality, pet_price, pet_description, pet_age, pet_age_type, pet_gender, pet_avatar_name, pet_kind)
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
            SET pet_name = ?, pet_locality = ?, pet_price = ?, pet_description = ?, pet_age = ?, pet_age_type = ?, pet_gender = ?, pet_avatar_name = ?, pet_kind = ?
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

    public function getAnnouncementWithUserContext($id, $userId, $includeFeatures = true)
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
        NATURAL JOIN animal_types
        JOIN users on announcements.user_id = users.user_id
        JOIN user_detail on users.user_id = user_detail.user_id
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

        if ($includeFeatures) {
            $announcement->getDetails()->setFeatures($this->getAnnouncementFeatures($announcement->getDetails()->getId()));
        }

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
            NATURAL JOIN animal_types
            JOIN users on announcements.user_id = users.user_id
            JOIN user_detail on users.user_id = user_detail.user_id
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
            UPDATE announcement_report SET checked=true WHERE announcement_id =?');
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

    public function getAnnouncementFeatures($announcementDetailId)
    {
        $connection = $this->database->connect();
        $stmt = $connection->prepare('
        SELECT animal_features.*, announcement_animal_features.value
        FROM announcement_animal_features
        NATURAL JOIN animal_features
        WHERE announcement_detail_id = ?');
        $stmt->execute([$announcementDetailId]);

        $result = [];
        $features = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($features as $feature) {
            $result[] = new PetFeature($feature['feature_id'], $feature['feature_name'], $feature['value']);
        }
        return $result;
    }

    public function getAnimalFeatures(): array
    {
        $result = [];
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM animal_features ORDER BY feature_id ASC;
        ');
        $stmt->execute();
        $features = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($features as $feature) {
            $result[] = new PetFeature(
                $feature['feature_id'],
                $feature['feature_name'],
                null
            );
        }
        return $result;
    }

    public function getAnimalTypes($query = null): array
    {
        $result = [];

        $sql = 'SELECT * FROM animal_types';
        if ($query) {
            $sql .= ' WHERE type_name LIKE :query';
        }
        $sql .= ' ORDER BY type_name ASC;';

        $stmt = $this->database->connect()->prepare($sql);
        if ($query) {
            $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        }
        $stmt->execute();
        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($types as $type) {
            $result[] = new PetType(
                $type['type_id'],
                $type['type_name']
            );
        }

        return $result;
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

    public static function createAnnouncementFromResult($result)
    {
        $announcementDetails = new AnnouncementDetail(
            $result['announcement_detail_id'],
            $result['pet_name'],
            $result['pet_locality'],
            $result['pet_price'],
            $result['pet_description'],
            $result['pet_age'],
            $result['pet_age_type'],
            $result['pet_gender'],
            $result['pet_avatar_name'],
            $result['pet_kind'],
            []
        );

        return new Announcement(
            $result['announcement_id'],
            new PetType($result['type_id'], $result['type_name']),
            self::createUserFromResult($result),
            $announcementDetails,
            $result['accepted'],
            $result['created_at']
        );
    }
}
