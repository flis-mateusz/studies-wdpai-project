<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/AnimalFeature.php';
require_once __DIR__ . '/../models/AnimalType.php';
require_once __DIR__ . '/../models/Announcement.php';

class AnnouncementsRepository extends Repository
{
    public function add_or_edit(Announcement $announcement)
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
            INSERT INTO announcement_detail (announcement_id, name, locality, price, description, age, age_type, gender, avatar_name, kind)
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

            foreach ($announcement->getDetails()->getFeatures() as $feature) {
                $stmt = $connection->prepare('
                INSERT INTO announcement_animal_features (feature_id, value, announcement_id)
                VALUES (?,?,?)');

                $stmt->execute([
                    $feature->getId(),
                    json_encode($feature->getValue()),
                    $announcement_id,
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

    public function getAnimalFeatures(): array
    {
        $result = [];
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM animal_features ORDER BY feature_id ASC;
        ');
        $stmt->execute();
        $features = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($features as $feature) {
            $result[] = new AnimalFeature(
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
            $result[] = new AnimalType(
                $type['type_id'],
                $type['type_name']
            );
        }

        return $result;
    }
}
