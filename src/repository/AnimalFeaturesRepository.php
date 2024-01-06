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
}
