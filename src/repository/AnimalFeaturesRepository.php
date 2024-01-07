<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/animal/AnimalFeature.php';

class AnimalFeaturesRepository extends Repository
{
    public function getAll(): array
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

    public function getForAnnouncement($announcementDetailId)
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
            $result[] = new AnimalFeature($feature['feature_id'], $feature['feature_name'], $feature['value']);
        }
        return $result;
    }

    public function getByPopularity(): array
    {
        $connection = $this->database->connect();

        $sql = '
        SELECT animal_features.feature_id, animal_features.feature_name, COUNT(announcement_animal_features.announcement_detail_id) AS usage_count
        FROM animal_features
        LEFT JOIN announcement_animal_features ON animal_features.feature_id = announcement_animal_features.feature_id
        GROUP BY animal_features.feature_id, animal_features.feature_name
        ORDER BY usage_count DESC;';

        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $features = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($features as $feature) {
            $result[] = new AnimalFeature(
                $feature['feature_id'],
                $feature['feature_name'],
                null,
                $feature['usage_count']
            );
        }

        return $result;
    }

    public function getByName(string $name): ?AnimalFeature
    {
        $result = null;
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM animal_features WHERE feature_name = :name;
        ');
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $feature = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($feature) {
            $result = new AnimalFeature(
                $feature['feature_id'],
                $feature['feature_name'],
                null
            );
        }
        return $result;
    }

    public function add($featureName)
    {
        $connection = $this->database->connect();
        $stmt = $connection->prepare('
            INSERT INTO animal_features (feature_name) VALUES (?)
        ');
        $stmt->execute([$featureName]);
        return $connection->lastInsertId();
    }

    public function delete($featureId)
    {
        $connection = $this->database->connect();
        $stmt = $connection->prepare('
            DELETE FROM animal_features WHERE feature_id =?
        ');
        $stmt->execute([$featureId]);
        return $stmt->rowCount();
    }
}
