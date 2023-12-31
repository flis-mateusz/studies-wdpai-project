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
