<?php

class User implements \JsonSerializable
{
    private $id;
    private $email;
    private $password;
    private $created_at;
    private $name;
    private $surname;
    private $avatar_name;
    private $phone;
    private $is_admin;

    public function __construct(
        ?int $id,
        ?string $email,
        ?string $password,
        ?string $created_at,
        string $name,
        string $surname,
        ?string $avatar_name,
        ?string $phone = null,
        ?bool $is_admin = false
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;

        if ($created_at) {
            $dateTime = new DateTime($created_at);
            $dateTime->setTimezone(new DateTimeZone('UTC'));
            $this->created_at = $dateTime;
        } else {
            $this->created_at = null;
        }

        $this->name = $name;
        $this->surname = $surname;
        $this->avatar_name = $avatar_name;
        $this->phone = $phone;
        $this->is_admin = $is_admin;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    public function getFullName(): string
    {
        return $this->name . ' ' . $this->surname;
    }

    public function getAvatarUrl(): ?string
    {
        if ($this->avatar_name) {
            return '/public/images/uploads/' . $this->avatar_name;
        }
        return $this->avatar_name;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatar_name;
    }

    public function setAvatarName(?string $avatar_name): void
    {
        $this->avatar_name = $avatar_name;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function setIsAdmin(bool $is_admin): void
    {
        $this->is_admin = $is_admin;
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
