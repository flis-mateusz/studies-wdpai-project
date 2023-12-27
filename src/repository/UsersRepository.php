<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/User.php';

class UsersRepository extends Repository
{
    public function getUser($email): ?User
    {

        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users WHERE email = :email;
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
            'test',
            'test2'
        );
    }

    public function addUser(User $user)
    {
        $stmt = $this->database->connect()->prepare('
            INSERT INTO users (username, email, password_hash)
            VALUES (?, ?, ?) RETURNING user_id;
        ');
        $stmt->execute([
            $user->getName(),
            $user->getEmail(),
            $user->getPassword()
        ]);

        return $stmt->fetchColumn();
    }
}
