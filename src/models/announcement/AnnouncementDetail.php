<?php

require_once __DIR__ . '/../animal/AnimalType.php';
require_once __DIR__ . '/../animal/AnimalFeature.php';

class AnnouncementDetail
{
    private $id;
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

    // ADMIN ONLY
    private array $likesIds;
    private int $reportsCount;

    public function __construct(
        ?int $id,
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
        $this->id = $id;
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
        $this->likesIds = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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
            return '<span class="italic">Nieznany</span>';
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
            return '<span class="italic">Wiek nieznany</span>';
        }
        return formatTimeUnits($this->age, $this->ageType);
    }

    public function setAgeType(string $ageType): void
    {
        $this->ageType = $ageType;
    }

    public function addFeature(AnimalFeature $feature): void
    {
        array_push($this->features, $feature);
    }

    public function setFeatures(array $features): void
    {
        $this->features = $features;
    }

    public function getLikesIds(): array
    {
        return $this->likesIds;
    }

    public function setLikesIds(array $likesIds): void
    {
        $this->likesIds = $likesIds;
    }

    public function getReportsCount(): ?int
    {
        return isset($this->reportsCount) ? $this->reportsCount : null;
    }

    public function setReportsCount(int $reportsCount): void
    {
        $this->reportsCount = $reportsCount;
    }

    public static function featuresToAssociativeArray(array $features): array
    {

        $result = [];
        foreach ($features as $feature) {
            /**
             * @var AnimalFeature $feature 
             */
            $result[$feature->getId()] = [
                'name' => $feature->getName(),
                'value' => $feature->getValue(),
            ];
        }
        return $result;
    }

    public static function typesToAssociativeArray(array $types): array
    {
        $result = [];
        foreach ($types as $type) {
            /**
             * @var AnimalType $type 
             */
            $result[$type->getId()] = [
                'name' => $type->getName(),
            ];
        }
        return $result;
    }
}
