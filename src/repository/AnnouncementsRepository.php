<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/AnimalFeature.php';
require_once __DIR__ . '/../models/AnimalType.php';

class AnnouncementsRepository extends Repository
{
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
                $feature['feature_name']
            );
        }
        return $result;
    }

    public function getAnimalTypes(): array
    {
        $result = [];
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM animal_types ORDER BY type_id ASC;
        ');
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
