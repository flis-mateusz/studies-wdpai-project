<?php
class AnimalType implements \JsonSerializable
{
    private $id;
    private $name;
    private $usageCount;

    public function __construct(?int $id, ?string $name, ?int $usageCount = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->usageCount = $usageCount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getUsageCount():?int
    {
        return $this->usageCount;
    }

    public function jsonSerialize(): mixed
    {
        return [
            $this->id => $this->name,
        ];
    }
}
