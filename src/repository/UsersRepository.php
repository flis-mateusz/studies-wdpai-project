<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/User.php';

class EmailExistsException extends Exception
{
    public function __construct()
    {
        parent::__construct('Adres e-mail już istnieje w bazie danych');
    }
}
class PhoneExistsException extends Exception
{
    public function __construct()
    {
        parent::__construct('Numer telefonu już istnieje w bazie danych');
    }
}

class UsersRepository extends Repository
{
    public function getUser($email, $id = null): ?User
    {
        if ($email === null && $id === null) {
            throw new InvalidArgumentException('Email or ID must not be null');
        }

        $queryParams = [];
        $conditions = [];

        if ($email !== null) {
            $conditions[] = 'email = :email';
            $queryParams[':email'] = $email;
        }

        if ($id !== null) {
            $conditions[] = 'user_id = :id';
            $queryParams[':id'] = $id;
        }

        $query = 'SELECT * FROM users NATURAL JOIN user_detail WHERE ' . implode(' AND ', $conditions) . ';';

        $stmt = $this->database->connect()->prepare($query);
        foreach ($queryParams as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user === false) {
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
        $this->checkUnique($user->getEmail(), $user->getPhone());

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
        $this->checkUnique($user->getEmail(), $user->getPhone(), $user->getId());

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
            $user->getAvatarName(),
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
    private function checkUnique($email, $phone, $user_id = null)
    {
        $this->checkFieldAvailability('email', $email, $user_id);
        $this->checkFieldAvailability('phone', $phone, $user_id);

        return true;
    }

    /**
     * Checks if a given field value already exists in the database for a specific field.
     *
     * @param string $fieldName The name of the field to check (e.g., 'email', 'phone').
     * @param mixed $fieldValue The value to be checked for uniqueness in the specified field.
     * @param int|null $userId Optional user ID to exclude from the check (useful for updates).
     *
     * @throws EmailExistsException If an email address already exists (when $fieldName is 'email').
     * @throws PhoneExistsException If a phone number already exists (when $fieldName is 'phone').
     * 
     * @return void This function does not return a value. It throws an exception if the value
     *              is not unique in the specified field.
     */
    private function checkFieldAvailability($fieldName, $fieldValue, $userId)
    {
        $queryParams = [$fieldValue];
        $query = "SELECT COUNT(*) FROM users WHERE $fieldName = ?";

        if ($userId !== null) {
            $query .= " AND user_id != ?";
            $queryParams[] = $userId;
        }

        $stmt = $this->database->connect()->prepare($query);
        $stmt->execute($queryParams);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $exceptionClass = ucfirst($fieldName) . 'ExistsException';
            if (class_exists($exceptionClass)) {
                throw new $exceptionClass();
            }
        }
    }
}
