<?php

require_once 'Repository.php';

class DogsRepository extends Repository
{    
    public function getDogs(): array
    {
        $result = [];

        $stmt = $this->database->connect()->prepare('
            SELECT * FROM dogs;
        ');
        $stmt->execute();
        $dogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

         foreach ($dogs as $dog) {

         }

        return $result;
    }
}
