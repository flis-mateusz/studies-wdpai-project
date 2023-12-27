<?php

require_once 'Repository.php';

class AnnouncementsRepository extends Repository
{
    public function getAnnouncements(): array
    {

        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users;
        ');
        $stmt->execute();
        $dogs = $stmt->fetchAll(PDO::FETCH_ASSOC);


        return $dogs;
    }
}
