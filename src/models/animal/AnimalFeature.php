<?php

class AnimalFeature
{
    private $id;
    private $name;
    private $value;
    private $usageCount;

    public function __construct(int $id, ?string $name, int|bool|null $value, ?int $usageCount = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->setValue($value);
        $this->usageCount = $usageCount;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getValue(): bool | null
    {
        return $this->value;
    }

    public function setValue(int|bool|null $value): void
    {
        if (is_numeric($value)) {
            $this->value = $value == 2 ? true : false;
        } else {
            $this->value = $value;
        }
    }

    public function getUsageCount():?int
    {
        return $this->usageCount;
    }
}
