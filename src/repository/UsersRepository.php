<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/User.php';

class UsersRepository extends Repository
{
    public function getUser($email): ?User
    {

        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users NATURAL JOIN user_detail WHERE email = :email;
        ');
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user == false) {
            return null;
        }

        return new User(
            $user['user_id'],
            $user['email'],
            $user['password_hash'],
            $user['created_at'],
            $user['name'],
            $user['surname'],
            $user['avatar_url']
        );
    }

    public function addUser(User $user)
    {
        $stmt = $this->database->connect()->prepare('
            INSERT INTO users (email, password_hash)
            VALUES (?, ?) RETURNING user_id;
        ');
        $stmt->execute([
            $user->getEmail(),
            $user->getPassword()
        ]);

        $result = $stmt->fetchColumn();
        if ($result) {
            $user->setId($result);

            $stmt = $this->database->connect()->prepare('
                INSERT INTO user_detail (user_id, name, surname, avatar_url)
                VALUES (?,?,?,?) RETURNING detail_id;
            ');

            $stmt->execute([
                $user->getId(),
                $user->getName(),
                $user->getSurname(),
                $user->getAvatarUrl()
            ]);

            return $stmt->fetchColumn();
        }
    }
}
