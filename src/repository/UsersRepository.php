<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/User.php';

class EmailExistsException extends Exception
{
}
class PhoneExistsException extends Exception
{
}

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
            $user['avatar_name'],
            $user['phone'],
            false
        );
    }

    public function addUser(User $user)
    {
        $this->credentialsExistsCheck($user->getEmail(), $user->getPhone());

        $stmt = $this->database->connect()->prepare('
            INSERT INTO users (email, password_hash, phone)
            VALUES (?, ?, ?) RETURNING user_id;
        ');
        $stmt->execute([
            $user->getEmail(),
            $user->getPassword(),
            $user->getPhone()
        ]);

        $result = $stmt->fetchColumn();
        if ($result) {
            $user->setId($result);

            $stmt = $this->database->connect()->prepare('
                INSERT INTO user_detail (user_id, name, surname, avatar_name)
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

    public function updateUser(User $user)
    {
        Logger::debug($user);
        $this->credentialsExistsCheck($user->getEmail(), $user->getPhone(), $user->getId());

        $stmt = $this->database->connect()->prepare('
            UPDATE users SET email =?, password_hash =?, phone =?, is_admin =?
            WHERE user_id =? ;
        ');
        $stmt->execute([
            $user->getEmail(),
            $user->getPassword(),
            $user->getPhone(),
            json_encode($user->isAdmin()),
            $user->getId()
        ]);

        $stmt = $this->database->connect()->prepare('
            UPDATE user_detail SET name =?, surname =?, avatar_name =?
            WHERE user_id =?;
        ');
        $stmt->execute([
            $user->getName(),
            $user->getSurname(),
            $user->getAvatarUrl(),
            $user->getId()
        ]);
    }

    /**
     * Checks if email or phone credentials are already in use
     *
     * @param string $email
     * @param string $phone
     * @param int $user_id
     * @return bool
     * @throws EmailExistsException if email is already in use
     * @throws PhoneExistsException if phone is already in use
     */
    public function credentialsExistsCheck($email, $phone, $user_id = null)
    {
        $emailCheckQuery = $this->database->connect()->prepare('SELECT COUNT(*) FROM users WHERE email = ? AND user_id != ?');
        $emailCheckQuery->execute([$email, $user_id]);
        $emailCount = $emailCheckQuery->fetchColumn();
        if ($emailCount > 0) {
            throw new EmailExistsException('Email jest już zajęty');
        }

        $phoneCheckQuery = $this->database->connect()->prepare('SELECT COUNT(*) FROM users WHERE phone = ? AND user_id != ?');
        $phoneCheckQuery->execute([$phone, $user_id]);
        $phoneCount = $phoneCheckQuery->fetchColumn();
        if ($phoneCount > 0) {
            throw new PhoneExistsException('Numer telefonu jest już zajęty');
        }
        return false;
    }
}
