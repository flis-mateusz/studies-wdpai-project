<?php

require_once 'User.php';
require_once 'AnimalType.php';
require_once 'AnimalFeature.php';

class AnnouncementDetail
{
    private $name;
    private $locality;
    private $price;
    private $description;
    private $age;
    private $ageType;
    private $gender;
    private $avatarName;
    private $kind;
    private array $features;

    public function __construct(
        string $name,
        string $locality,
        ?float $price,
        string $description,
        ?int $age,
        ?string $age_type,
        string $gender,
        ?string $avatarName,
        ?string $kind,
        array $features
    ) {
        $this->name = $name;
        $this->locality = $locality;
        $this->price = $price;
        $this->description = $description;
        $this->age = $age;
        $this->ageType = $age_type;
        $this->gender = $gender;
        $this->avatarName = $avatarName;
        $this->kind = $kind;
        $this->features = $features;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLocality(): string
    {
        return $this->locality;
    }

    public function setLocality(string $locality): void
    {
        $this->locality = $locality;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    public function setAvatarName(string $avatarName): void
    {
        $this->avatarName = $avatarName;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(string $kind): void
    {
        $this->kind = $kind;
    }

    public function getAgeType(): ?string
    {
        return $this->ageType;
    }

    public function setAgeType(string $ageType): void
    {
        $this->ageType = $ageType;
    }

    public function addFeature(AnimalFeature $feature): void
    {
        array_push($this->features, $feature);
    }
}

class Announcement
{
    private $announcement_id;
    private AnimalType $type;
    private User $user;
    private bool $accepted;
    private AnnouncementDetail $details;

    public function __construct(
        ?int $announcement_id,
        AnimalType $type,
        ?User $user,
        ?string $kind,
        bool $accepted,
        string $name,
        string $locality,
        ?int $price,
        string $description,
        ?string $age,
        ?string $age_type,
        string $gender,
        ?string $avatarName,
        array $features
    ) {
        $this->announcement_id = $announcement_id;
        $this->type = $type;
        $this->user = $user;
        $this->accepted = $accepted;
        $this->details = new AnnouncementDetail($name, $locality, $price, $description, $age, $age_type, $gender, $avatarName, $kind, $features);
    }

    public function getId(): int
    {
        return $this->announcement_id;
    }

    public function setId(int $announcement_id): void
    {
        $this->announcement_id = $announcement_id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getDetails(): AnnouncementDetail
    {
        return $this->details;
    }

    public function getType(): AnimalType
    {
        return $this->type;
    }

    public function isAccepted(): bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): void
    {
        $this->accepted = $accepted;
    }
}
