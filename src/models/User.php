<?php

class User
{
    private $id;
    private $email;
    private $password;
    private $created_at;
    private $name;
    private $surname;
    private $avatar_url;
    private $phone;
    private $is_admin;

    public function __construct(
        ?int $id,
        string $email,
        string $password,
        ?string $created_at,
        string $name,
        string $surname,
        ?string $avatar_url,
        ?string $phone,
        ?bool $is_admin
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;

        $dateTime = new DateTime($created_at);
        $dateTime->setTimezone(new DateTimeZone('UTC'));
        $this->created_at = $dateTime;

        $this->name = $name;
        $this->surname = $surname;
        $this->avatar_url = $avatar_url;
        $this->phone = $phone;
        $this->is_admin = $is_admin;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getCreatedAt(): DateTime
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

    public function getAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function setAvatarUrl(string $avatar_url): void
    {
        $this->avatar_url = $avatar_url;
    }

    public function getPhone():?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function isAdmin():?bool
    {
        return $this->is_admin;
    }

    public function setIsAdmin(bool $is_admin): void
    {
        $this->is_admin = $is_admin;
    }
}
