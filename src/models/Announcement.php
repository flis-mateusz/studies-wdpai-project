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
    private $gender;
    private $avatar_url;
    private array $features;

    public function __construct(
        string $name,
        string $locality,
        float $price,
        string $description,
        int $age,
        string $gender,
        string $avatar_url
    ) {
        $this->name = $name;
        $this->locality = $locality;
        $this->price = $price;
        $this->description = $description;
        $this->age = $age;
        $this->gender = $gender;
        $this->avatar_url = $avatar_url;
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

    public function getPrice(): float
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
    
    public function getAge(): int
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
    
    public function getAvatarUrl(): string
    {
        return $this->avatar_url;
    }
    
    public function setAvatarUrl(string $avatar_url): void
    {
        $this->avatar_url = $avatar_url;
    }

    public function getFeatures(): array
    {
        return $this->features;
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
    private $species_name;
    private User $user;
    private AnnouncementDetail $details;

    public function __construct(
        int $announcement_id,
        AnimalType $type,
        string $species_name,
        User $user,
        string $name,
        string $locality,
        ?int $price,
        string $description,
        string $age,
        string $gender,
        string $avatar_url
    ) {
        $this->announcement_id = $announcement_id;
        $this->type = $type;
        $this->species_name = $species_name;
        $this->user = $user;
        $this->details = new AnnouncementDetail($name, $locality, $price, $description, $age, $gender, $avatar_url);
    }

    public function getAnnouncementId(): int
    {
        return $this->announcement_id;
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

    public function getSpeciesName(): string
    {
        return $this->species_name;
    }
}
