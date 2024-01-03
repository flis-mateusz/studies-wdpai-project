<?php

require_once __DIR__ . '/../pet/PetType.php';
require_once __DIR__ . '/../pet/PetFeature.php';

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

    public function getFormattedPrice(): string
    {
        if (!$this->price) {
            return 'Oddam za darmo';
        }
        return $this->price . ' zł';
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

    public function getFormattedGender(): string
    {
        switch ($this->gender) {
            case 'male':
                return 'On';
                break;
            case 'female':
                return 'Ona';
                break;
            default:
                return 'Nieznana';
                break;
        }
    }

    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarName ? '/public/images/uploads/' . $this->avatarName : null;
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

    public function getFormattedKind(): string
    {
        if (!$this->kind) {
            return 'Nieznany';
        }
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

    public function getFormattedAge(): string
    {
        if (!$this->ageType) {
            return 'Wiek nieznany';
        }
        return formatTimeUnits($this->age, $this->ageType);
    }

    public function setAgeType(string $ageType): void
    {
        $this->ageType = $ageType;
    }

    public function addFeature(PetFeature $feature): void
    {
        array_push($this->features, $feature);
    }

    public function setFeatures(array $features): void
    {
        $this->features = $features;
    }

    public static function featuresToAssociativeArray(array $features): array
    {
        $result = [];
        foreach ($features as $feature) {
            /**
             * @var PetFeature $feature 
             */
            $result[$feature->getId()] = [
                'name' => $feature->getName(),
                'value' => $feature->getValue(),
            ];
        }
        return $result;
    }
}
