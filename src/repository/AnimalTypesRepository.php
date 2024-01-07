<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/animal/AnimalType.php';

class AnimalTypesRepository extends Repository
{
    public function getAll($query = null): array
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

    public function getByName(string $name): ?AnimalType
    {
        $result = null;
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM animal_types WHERE type_name = :name;
        ');
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $type = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($type) {
            $result = new AnimalType(
                $type['type_id'],
                $type['type_name']
            );
        }
        return $result;
    }

    public function getByPopularity(): array
    {
        $connection = $this->database->connect();

        $sql = '
        SELECT animal_types.type_id, animal_types.type_name, COUNT(announcements.announcement_id) AS usage_count
        FROM animal_types
        LEFT JOIN announcements ON animal_types.type_id = announcements.type_id
        GROUP BY animal_types.type_id
        ORDER BY usage_count DESC;';

        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($types as $type) {
            $result[] = new AnimalType(
                $type['type_id'],
                $type['type_name'],
                $type['usage_count']
            );
        }

        return $result;
    }

    public function add(string $name)
    {
        $connection = $this->database->connect();
        $stmt = $connection->prepare('
            INSERT INTO animal_types (type_name) VALUES (?)
        ');
        $stmt->execute([$name]);
        return $connection->lastInsertId();
    }

    public function delete(int $typeId)
    {
        $connection = $this->database->connect();
        $stmt = $connection->prepare('
            DELETE FROM animal_types WHERE type_id =?
        ');
        $stmt->execute([$typeId]);
        return $stmt->rowCount();
    }
}
